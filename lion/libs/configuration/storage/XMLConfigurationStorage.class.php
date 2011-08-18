<?php
/**
 * This class is based on PEAR::Config by Bertrand Mansion under the PHP License, version 2.02 (http://www.php.net/license/2_02.txt)
 * A copy of the license terms can be found in /thrdparty-licenses/CONFIG
 * 
 * This class was created on 11/28/2006
 *
 */

class __XMLConfigurationStorage extends __ConfigurationStorage {
    
    public function parse($content, __Configuration &$configuration) {
        libxml_use_internal_errors(true);
        $dom = new DomDocument("1.0");
        $dom->loadXml($content);
        if( $dom->documentElement != null ) {
        	$this->_parseDomNode($dom->documentElement, $configuration);
        }
        else {
            $xml_content_array = preg_split('/[\n\r]+/', $content);            
            $parse_errors = libxml_get_errors();
            $error_descriptions_array = array();
            foreach ($parse_errors as $_parse_error) {
                $error_descriptions_array[] = $this->_getReadableXMLErrorDescription($_parse_error, $xml_content_array);
            }
            $error_description = join("\n", $error_descriptions_array);
            libxml_clear_errors();
            throw new __ConfigurationException($error_description);
        }
    }
    
    protected function _parseDomNode($dom_node, __ComplexConfigurationComponent &$configuration_component) {
        //Process root element
        $section_name = $dom_node->nodeName;
        $configuration_section =& $configuration_component->createSection($section_name);
        if ( $dom_node->hasAttributes() )
        {
            $dom_attributes = $dom_node->attributes;
            $attributes     = array();
            foreach ($dom_attributes as $dom_attribute)
            {
                $attributes[$dom_attribute->name] = $dom_attribute->value;
            }
            $configuration_section->setAttributes($attributes);
        }
        //Process childrens:
        $dom_node = $dom_node->firstChild;
        while ($dom_node != null)
        {
            switch ($dom_node->nodeType)
            {
                case XML_TEXT_NODE:
                case XML_CDATA_SECTION_NODE:
                    if(!(trim($dom_node->nodeValue) == "")) {
                        $configuration_section->createProperty($dom_node->nodeName, $dom_node->nodeValue);
                    }
                    break;
                case XML_COMMENT_NODE:
                    if(!(trim($dom_node->nodeValue) == "")) {
                        $configuration_section->createComment($dom_node->nodeValue);
                    }
                    break;
                case XML_ELEMENT_NODE:
                    $this->_parseDomNode($dom_node, $configuration_section);
                    break;
            }
            $dom_node = $dom_node->nextSibling;
        }
    }

    public function _getReadableXMLErrorDescription($error, $xml)
    {
        $return = '';
        if(count($xml) >= $error->line - 1) {
            $return  = $xml[$error->line - 1] . "\n";
            $return .= str_repeat('-', $error->column) . "^\n";
        }

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }

        $return .= trim($error->message) .
        "\n  Line: $error->line" .
        "\n  Column: $error->column";

        if ($error->file) {
            $return .= "\n  File: $error->file";
        }

        return $return;
    }

    public function save($filename, __Configuration &$configuration) {

    }

    public function toString(__ConfigurationComponent &$configuration_component) {
        $indent = '';
        if (!$configuration_component->isRoot()) {
            // no indent for root
            $this->_deep++;
            $indent = str_repeat($this->options['indent'], $this->_deep);
        } else {
            // Initialize string with xml declaration
            $string = '';
            if ($this->options['addDecl']) {
                $string .= XML_Util::getXMLDeclaration($this->options['version'], $this->options['encoding']);
                $string .= $this->options['linebreak'];
            }
            if (!empty($this->options['name'])) {
                $string .= '<'.$this->options['name'].'>'.$this->options['linebreak'];
                $this->_deep++;
                $indent = str_repeat($this->options['indent'], $this->_deep);
            }
        }
        if (!isset($string)) {
            $string = '';
        }
        if ($configuration_component instanceof __ConfigurationProperty) {
            $attributes = ($this->options['useAttr']) ? $configuration_component->attributes : array();
            $string .= $indent.XML_Util::createTag($configuration_component->name, $attributes, $configuration_component->content, null,
            ($this->options['useCData'] ? XML_UTIL_CDATA_SECTION : XML_UTIL_REPLACE_ENTITIES));
            $string .= $this->options['linebreak'];
        }
        else if ($configuration_component instanceof __ConfigurationComment ) {
            $string .= $indent.'<!-- '.$configuration_component->content.' -->';
            $string .= $this->options['linebreak'];
        }
        else if($configuration_component instanceof __ConfigurationSection ) {
            if (!$configuration_component->isRoot()) {
                $string = $indent.'<'.$configuration_component->name;
                $string .= ($this->options['useAttr']) ? XML_Util::attributesToString($configuration_component->attributes) : '';
            }
            if ($children = count($configuration_component->children)) {
                if (!$configuration_component->isRoot()) {
                    $string .= '>'.$this->options['linebreak'];
                }
                for ($i = 0; $i < $children; $i++) {
                    $string .= $this->toString($configuration_component->getChild($i));
                }
            }
            if (!$configuration_component->isRoot()) {
                if ($children) {
                    $string .= $indent.'</'.$configuration_component->name.'>'.$this->options['linebreak'];
                } else {
                    $string .= '/>'.$this->options['linebreak'];
                }
            } else {
                if (!empty($this->options['name'])) {
                    $string .= '</'.$this->options['name'].'>'.$this->options['linebreak'];
                }
            }
        }
        else {
            $string = '';
        }
        if (!$configuration_component->isRoot()) {
            $this->_deep--;
        }
        return $string;
    }

}

