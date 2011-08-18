<?php
/**
 * This class is based on PEAR::Config by Bertrand Mansion under the PHP License, version 2.02 (http://www.php.net/license/2_02.txt)
 * A copy of the license terms can be found in /thrdparty-licenses/CONFIG
 * 
 * This class was created on 11/28/2006
 * 
 */

class __IniCommentedFileConfigurationStorage extends __IniFileConfigurationStorage {

    public function load($filename, __Configuration &$configuration) {
        if(is_readable($filename) && is_file($filename)) {
            try {
                $file_content = file_get_contents($filename);
                $this->parse($file_content, $configuration);
            }
            catch (Exception $e) {
                throw new __ConfigurationException("Error parsing configuration file '$filename':\n\n" . $e->getMessage());
            }
        }
    }    
    
    public function parse($content, __Configuration &$configuration) {
        $return = true;
        $lines = preg_split('/\r?\n/', $content);
        $n = 0;
        $lastline = '';
        $current_section =& $configuration;
        foreach ($lines as $line) {
            $n++;
            if (preg_match('/^\s*' . $this->_comment_symbol . '(.*?)\s*$/', $line, $match)) {
                // a comment
                $current_section->createComment($match[1]);
            } elseif (trim($line) == '') {
                // a blank line
                $current_section->createBlank();
            } elseif (preg_match('/^\s*([a-zA-Z0-9_\-\.\s:]*)\s*=\s*(.*)\s*$/', $line, $match)) {
                // a directive

                $values = $this->_quoteAndCommaParser($match[2]);
                if (count($values)) {
                    foreach($values as $value) {
                        if ($value[0] == 'normal') {
                            $current_section->createProperty(trim($match[1]), $value[1]);
                        }
                        if ($value[0] == 'comment') {
                            $current_section->createComment(substr($value[1], 1));
                        }
                    }
                }
            } elseif (preg_match('/^\s*\[\s*(.*)\s*\]\s*$/', $line, $match)) {
                // a section
                $current_section =& $configuration->createSection($match[1]);
            } elseif (trim($line) == '') {
                $current_section =& $obj->container->createBlank();
            } else {
                throw new __ConfigurationException("Syntax error in '$line' at line $n.");
            }
        }
        return $return;

    }

    /**
     * Quote and Comma Parser for INI files
     *
     * This function allows complex values such as:
     *
     * <samp>
     * mydirective = "Item, number \"1\"", Item 2 ; "This" is really, really tricky
     * </samp>
     * @param  string  $text    value of a directive to parse for quotes/multiple values
     * @return array   The array returned contains multiple values, if any (unquoted literals
     *                 to be used as is), and a comment, if any.  The format of the array is:
     *
     * <pre>
     * array(array('normal', 'first value'),
     *       array('normal', 'next value'),...
     *       array('comment', '; comment with leading ;'))
     * </pre>
     * @author Greg Beaver <cellog@users.sourceforge.net>
     * @access private
     */
    function _quoteAndCommaParser($text)
    {
        $text = trim($text);
        if ($text == '') {
            $emptyNode = array();
            $emptyNode[0][0] = 'normal';
            $emptyNode[0][1] = '';
            return $emptyNode;
        }

        // tokens
        $tokens['normal'] = array('"', ';', ',');
        $tokens['quote'] = array('"', '\\');
        $tokens['escape'] = false; // cycle
        $tokens['after_quote'] = array(',', ';');

        // events
        $events['normal'] = array('"' => 'quote', ';' => 'comment', ',' => 'normal');
        $events['quote'] = array('"' => 'after_quote', '\\' => 'escape');
        $events['after_quote'] = array(',' => 'normal', ';' => 'comment');

        // state stack
        $stack = array();

        // return information
        $return = array();
        $returnpos = 0;
        $returntype = 'normal';

        // initialize
        array_push($stack, 'normal');
        $pos = 0; // position in $text

        do {
            $char = $text{$pos};
            $state = array_pop($stack);
            if (key_exists($state, $tokens)) {
                if (in_array($char, $tokens[$state])) {
                    switch($events[$state][$char]) {
                        case 'quote' :
                            if ($state == 'normal' &&
                            isset($return[$returnpos]) &&
                            !empty($return[$returnpos][1])) {
                                throw new __ConfigurationException("invalid ini syntax, quotes cannot follow text '$text'");
                            }
                            if ($returnpos >= 0 && isset($return[$returnpos])) {
                                // trim any unnecessary whitespace in earlier entries
                                $return[$returnpos][1] = trim($return[$returnpos][1]);
                            } else {
                                $returnpos++;
                            }
                            $return[$returnpos] = array('normal', '');
                            array_push($stack, 'quote');
                            continue 2;
                            break;
                        case 'comment' :
                            // comments go to the end of the line, so we are done
                            $return[++$returnpos] = array('comment', substr($text, $pos));
                            return $return;
                            break;
                        case 'after_quote' :
                            array_push($stack, 'after_quote');
                            break;
                        case 'escape' :
                            // don't save the first slash
                            array_push($stack, 'escape');
                            continue 2;
                            break;
                        case 'normal' :
                            // start a new segment
                            if ($state == 'normal') {
                                $returnpos++;
                                continue 2;
                            } else {
                                while ($state != 'normal') {
                                    array_pop($stack);
                                    $state = array_pop($stack);
                                }
                                $returnpos++;
                            }
                            break;
                        default :
                            throw new __ConfigurationException("::_quoteAndCommaParser oops, state missing");
                            break;
                    }
                } else {
                    if ($state != 'after_quote') {
                        if (!isset($return[$returnpos])) {
                            $return[$returnpos] = array('normal', '');
                        }
                        // add this character to the current ini segment if non-empty, or if in a quote
                        if ($state == 'quote') {
                            $return[$returnpos][1] .= $char;
                        } elseif (!empty($return[$returnpos][1]) ||
                        (empty($return[$returnpos][1]) && trim($char) != '')) {
                            if (!isset($return[$returnpos])) {
                                $return[$returnpos] = array('normal', '');
                            }
                            $return[$returnpos][1] .= $char;
                            if (strcasecmp('true', $return[$returnpos][1]) == 0) {
                                $return[$returnpos][1] = '1';
                            } elseif (strcasecmp('false', $return[$returnpos][1]) == 0) {
                                $return[$returnpos][1] = '';
                            }
                        }
                    } else {
                        if (trim($char) != '') {
                            throw new __ConfigurationException("invalid ini syntax, text after a quote not allowed '$text'");
                        }
                    }
                }
            } else {
                // no tokens, so add this one and cycle to previous state
                $return[$returnpos][1] .= $char;
                array_pop($stack);
            }
        } while (++$pos < strlen($text));
        return $return;
    }


}