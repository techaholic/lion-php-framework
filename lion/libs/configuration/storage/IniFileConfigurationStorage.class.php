<?php
/**
 * This class is based on PEAR::Config by Bertrand Mansion under the PHP License, version 2.02 (http://www.php.net/license/2_02.txt)
 * A copy of the license terms can be found in /thrdparty-licenses/CONFIG
 * 
 * This class was created on 11/28/2006
 * 
 */
class __IniFileConfigurationStorage extends __ConfigurationStorage {

    protected $_comment_symbol = ';';
    protected $_bool_true      = 'yes';
    protected $_bool_false     = 'no';
    protected $_tidy_content   = false;    
    protected $_use_quotes     = true;
    
    public function load($filename, __Configuration &$configuration) {
        //Set the name for current __Configuration as  the name of configuration file:
        $configuration->setName(strtoupper(basename($filename)));
        if (!file_exists($filename)) {
            throw new __ConfigurationException('Configuration file not found: "' . $filename . '"');
        }
        $current_section =& $configuration;
        $configuration_array = parse_ini_file($filename, true);
        if ($configuration_array === false) {
            throw new __ConfigurationException('Unknow configuration file format for : "' . $filename . '"');
        }
        foreach ($configuration_array as $key => $value) {
            if (is_array($value)) {
                $current_section =& $configuration->createSection($key);
                foreach ($value as $property => $content) {
                    $current_section->createProperty($property, $content);
                }
            } else {
                $current_section->createProperty($key, $value);
            }
        }
    }

    public function parse($content, __Configuration &$configuration) {
        $temp_dir = __PathResolver::resolvePath(__Lion::getInstance()->getRuntimeDirectives()->getDirective('TEMP_DIR'));
        $prefix = uniqid();
        $filename = tempnam($temp_dir, $prefix);
        $fp = fopen($filename, 'w');
        fwrite($fp, $content);
        fclose($fp);
        try{
            $this->load($filename, $configuration);
        }
        catch (Exception $e) {
            unlink($file);
            throw $e;
        }
        unlink($filename);
    }

    public function save($filename, __Configuration &$configuration) {
        $fp = fopen($filename, 'w');
        fwrite($fp, $this->toString($configuration));
        fclose($fp);
    }

    /**
     * Returns a formatted string for a __ConfigurationComponent instance
     * @param __ConfigurationComponent $configuration_component The __ConfigurationComponent instance to be output as string
     * @return string The formatted string
     */
    public function toString(__ConfigurationComponent &$configuration_component)
    {
        static $childrenCount, $commaString;
        $return_value = '';

        if( $configuration_component instanceof __ConfigurationBlank ) {
            $return_value = "\n";
        }
        else if($configuration_component instanceof __ConfigurationComment) {
            $return_value = $this->_comment_symbol . $configuration_component->getContent()."\n";
        }
        else if($configuration_component instanceof __ConfigurationProperty ) {
            $count = $configuration_component->getParent()->countChildren('__ConfigurationProperty', $configuration_component->getName());
            $content = $configuration_component->getContent();
            if ($content === false) {
                $content = $this->_bool_false;
            } elseif ($content === true) {
                $content = $this->_bool_true;
            } elseif ($this->_use_quotes === true &&
                      (strlen(trim($content)) < strlen($content) ||
                       strpos($content, ',') !== false ||
                       strpos($content, ';') !== false ||
                       strpos($content, '=') !== false ||
                       strpos($content, '"') !== false ||
                       strpos($content, '%') !== false ||
                       strpos($content, '~') !== false)) {
                $content = '"'.addslashes($content).'"';
            }
            if ($count > 1) {
                // multiple values for a directive are separated by a comma
                if (isset($childrenCount[$configuration_component->getName()])) {
                    $childrenCount[$configuration_component->getName()]++;
                } else {
                    $childrenCount[$configuration_component->getName()] = 0;
                    $commaString[$configuration_component->getName()] = $configuration_component->getName() . '=';
                }
                if ($childrenCount[$configuration_component->getName()] == $count-1) {
                    // Clean the static for future calls to toString
                    $return_value .= $commaString[$configuration_component->getName()].$content."\n";
                    unset($childrenCount[$configuration_component->getName()]);
                    unset($commaString[$configuration_component->getName()]);
                } else {
                    $commaString[$configuration_component->getName()] .= $content.', ';
                }
            } else {
                $return_value = $configuration_component->getName().'='.$content."\n";
            }
        }
        else if($configuration_component instanceof __ConfigurationSection ||
        $configuration_component instanceof __Configuration) {
            if (!$configuration_component->isRoot()) {
                $return_value = '['.$configuration_component->getName()."]\n";
            }
            $configuration_childrens = $configuration_component->getChildrens();
            foreach($configuration_childrens as $configuration_children) {
                $return_value .= $this->toString($configuration_children);
            }
        }
        else {
            $return_value = '';
        }
        return $return_value;
    }

}