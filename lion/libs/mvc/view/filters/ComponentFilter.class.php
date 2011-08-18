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
 * @package    View
 * 
 */


class __ComponentFilter extends __TemplateFilter {
       
    protected $_type = self::POST_FILTER;
	
    /**
     * This method executes a filter that detect special tag <component:xxx> and generates code according to the component type
     *
     * @param string Already compiled code by the template engine
     * @param __View The current __View derived instance
     * @return string The compiled code with the filter applied
     */
    public function executeFilter($compiled, __View &$view)
    {
        $return_value = $compiled; //by default will return the $compiled content without changes
        if( $view instanceof __TemplateEngineView ) {
            $component_parser_class = $view->getComponentParserClass();
            $lex    = new __ComponentLexer($compiled);
            $parser = new $component_parser_class($view);
            while ($lex->yylex()) {
                $parser->doParse($lex->token, $lex->value);
            }
            $parser->doParse(0, 0);
            $return_value = $parser->getResult();
        }
        return $return_value;
    }
    
}
