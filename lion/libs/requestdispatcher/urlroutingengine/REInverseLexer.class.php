<?php
/**
 * This file is part of lion framework.
 * 
 * Copyright (c) 2011 Antonio Parraga Navarro
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 *
 * @copyright  Copyright (c) 2011 Antonio Parraga Navarro
 * @author     Antonio Parraga Navarro
 * @link       http://www.lionframework.org
 * @license    http://www.lionframework.org/license.html
 * @version    1.4
 * @package    UrlRoutingEngine
 * 
 */

class __REInverseLexer
{
    const ESCSEQ         = __REInverseParser::ESCSEQ;         // \
    const CARET          = __REInverseParser::CARET;          // ^
    const DOLLAR         = __REInverseParser::DOLLAR;         // $
    const DOT            = __REInverseParser::DOT;            // .
    const COMMA          = __REInverseParser::COMMA;          // ,
    const LBRACKET       = __REInverseParser::LBRACKET;       // [
    const RBRACKET       = __REInverseParser::RBRACKET;       // ]
    const PIPE           = __REInverseParser::PIPE;           // |
    const LPAR           = __REInverseParser::LPAR;           // (
    const RPAR           = __REInverseParser::RPAR;           // )
    const QUESTIONMARK   = __REInverseParser::QUESTIONMARK;   // ?
    const ASTERISK       = __REInverseParser::ASTERISK;       // *
    const PLUS           = __REInverseParser::PLUS;           // +
    const LBRACE         = __REInverseParser::LBRACE;         // {
    const RBRACE         = __REInverseParser::RBRACE;         // }
    const SUB            = __REInverseParser::SUB;            // -

    //Group modifiers:
    const NONGROUPINGGRP = __REInverseParser::NONGROUPINGGRP; // ?:
    const NAMEDGROUPDEF  = __REInverseParser::NAMEDGROUPDEF;  // ?P
    const NAMEDGROUP     = __REInverseParser::NAMEDGROUP;     // ?P=
    const COMMENT        = __REInverseParser::COMMENT;        // ?#
    const MATCHIFNEXT    = __REInverseParser::MATCHIFNEXT;    // ?=
    const MATCHIFNOTNEXT = __REInverseParser::MATCHIFNOTNEXT; // ?!
    const POSILOOKBEHIND = __REInverseParser::POSILOOKBEHIND; // ?<=
    const NEGLOOKBEHIND  = __REInverseParser::NEGLOOKBEHIND;  // ?<!

    const INTEGER        = __REInverseParser::INTEGER;        // any integer number
    const ANYCHAR        = __REInverseParser::ANYCHAR;        // anything except the line break
    const URLVAR         = __REInverseParser::URLVAR;         // variable

    const QMMODIFIER     = 100;
    
 
    private $input;
    private $N;
    public $token;
    public $value;
    public $line;
    private $_string;
    private $debug = 0;
    
    function __construct($data)
    {
        $this->input = str_replace("\r\n", "\n", $data);
        $this->N = 0;
    }
 

    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    function yypushstate($state)
    {
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
    }

    function yypopstate()
    {
        $this->_yy_state = array_pop($this->_yy_stack);
    }

    function yybegin($state)
    {
        $this->_yy_state = $state;
    }



    function yylex1()
    {
        $tokenMap = array (
              1 => 0,
              2 => 0,
              3 => 0,
              4 => 0,
              5 => 0,
              6 => 0,
              7 => 0,
              8 => 0,
              9 => 0,
              10 => 1,
              12 => 0,
              13 => 0,
              14 => 0,
              15 => 0,
              16 => 0,
              17 => 0,
              18 => 0,
              19 => 1,
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = "/^(\\\\)|^(\\^)|^(\\.)|^(,)|^(\\[)|^(\\])|^(\\|)|^(\\()|^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)/";

        do {
            if (preg_match($yy_global_pattern, substr($this->input, $this->N), $yymatches)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        'an empty string.  Input "' . substr($this->input,
                        $this->N, 5) . '... state YYINITIAL');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {                    $yy_yymore_patterns = array(
        1 => "^(\\^)|^(\\.)|^(,)|^(\\[)|^(\\])|^(\\|)|^(\\()|^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        2 => "^(\\.)|^(,)|^(\\[)|^(\\])|^(\\|)|^(\\()|^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        3 => "^(,)|^(\\[)|^(\\])|^(\\|)|^(\\()|^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        4 => "^(\\[)|^(\\])|^(\\|)|^(\\()|^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        5 => "^(\\])|^(\\|)|^(\\()|^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        6 => "^(\\|)|^(\\()|^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        7 => "^(\\()|^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        8 => "^(\\))|^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        9 => "^(\\?(:|P<[A-Za-z_][A-Za-z_0-9]*>|P=[A-Za-z_][A-Za-z_0-9]*|=|!|<=|<!)?)|^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        10 => "^(\\*)|^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        12 => "^(\\+)|^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        13 => "^(\\{)|^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        14 => "^(\\})|^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        15 => "^(-)|^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        16 => "^(\\d+)|^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        17 => "^([^\\\\\^$\.,[\]|()?*+{}\-])|^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        18 => "^(\\$([A-Za-z_][A-Za-z_0-9]+)?)",
        19 => "",
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        if (preg_match($yy_yymore_patterns[$this->token],
                              substr($this->input, $this->N), $yymatches)) {
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token = key($yymatches); // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count("\n", $this->value);
                        }
                    	$r = $this->{'yy_r1_' . $this->token}();
                    } while ($r !== null || !$r);
			        if ($r === true) {
			            // we have changed state
			            // process this token in the new state
			            return $this->yylex();
			        } else {
	                    // accept
	                    $this->N += strlen($this->value);
	                    $this->line += substr_count("\n", $this->value);
	                    return true;
			        }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    const YYINITIAL = 1;
    function yy_r1_1($yy_subpatterns)
    {

    $this->token = self::ESCSEQ;
    }
    function yy_r1_2($yy_subpatterns)
    {

    $this->token = self::CARET;
    }
    function yy_r1_3($yy_subpatterns)
    {

    $this->token = self::DOT;
    }
    function yy_r1_4($yy_subpatterns)
    {

    $this->token = self::COMMA;
    }
    function yy_r1_5($yy_subpatterns)
    {

    $this->token = self::LBRACKET;
    }
    function yy_r1_6($yy_subpatterns)
    {

    $this->token = self::RBRACKET;
    }
    function yy_r1_7($yy_subpatterns)
    {

    $this->token = self::PIPE;
    }
    function yy_r1_8($yy_subpatterns)
    {

    $this->token = self::LPAR;
    }
    function yy_r1_9($yy_subpatterns)
    {

    $this->token = self::RPAR;
    }
    function yy_r1_10($yy_subpatterns)
    {

    switch($this->value) {
        case '?:':
            $this->token = self::NONGROUPINGGRP;
            break;
        case '?#':
            $this->token = self::COMMENT;
            break;
        case '?=':
            $this->token = self::MATCHIFNEXT;
            break;
        case '?!':
            $this->token = self::MATCHIFNOTNEXT;
            break;
        case '?<=':
            $this->token = self::POSILOOKBEHIND;
            break;
        case '?<!':
            $this->token = self::NEGLOOKBEHIND;
            break;
        case '?':
            $this->token = self::QUESTIONMARK;
            break;
        default:
            if(preg_match('/\?P\<[A-Za-z_][A-Za-z_0-9]*\>/', $this->value, $matched)) {
                $this->token = self::NAMEDGROUPDEF;
            }
            else if(preg_match('/\?P\=[A-Za-z_][A-Za-z_0-9]*/', $this->value, $matched)) {
                $this->token = self::NAMEDGROUP;
            }
            break;
    }
    }
    function yy_r1_12($yy_subpatterns)
    {

    $this->token = self::ASTERISK;
    }
    function yy_r1_13($yy_subpatterns)
    {

    $this->token = self::PLUS;
    }
    function yy_r1_14($yy_subpatterns)
    {

    $this->token = self::LBRACE;
    }
    function yy_r1_15($yy_subpatterns)
    {

    $this->token = self::RBRACE;
    }
    function yy_r1_16($yy_subpatterns)
    {

    $this->token = self::SUB;
    }
    function yy_r1_17($yy_subpatterns)
    {

    $this->token = self::INTEGER;
    }
    function yy_r1_18($yy_subpatterns)
    {

    $this->token = self::ANYCHAR;
    }
    function yy_r1_19($yy_subpatterns)
    {

    switch($this->value) {
        case '$':
            $this->token = self::DOLLAR;
            break;
        default:
            $this->token = self::URLVAR;
            break;
    }
    }


    /**
     * return something useful, when a parse error occurs.
     *
     * used to build error messages if the parser fails, and needs to know the line number..
     *
     * @return   string 
     * @access   public
     */
    function parseError() 
    {
        return "Error at line {$this->yyline}";
        
    }
}