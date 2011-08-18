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
 * @package    ComponentModel
 * 
 */


class __MenuHtmlWriter extends __ComponentWriter {
    
    protected $_component = null;
    
    public function startRender(__IComponent &$component)
    {
        $this->_component =& $component;
        $return_value = $this->_generateStartMenuEngine();
        $configuration_file = 'config/menu.xml'; //by default
        if(!empty($this->_component->configuration)) {
            $configuration_file = $this->_component->configuration;
        }
        $configuration = __CurrentContext::getInstance()->getConfigurationLoader()->loadConfigurationFile($configuration_file);
        $return_value .= $this->_loadMenuItem($configuration->getSection('menu_pc'));
        $return_value .= $this->_generateEndMenuEngine();
        return $return_value;
    }
    
    protected function _generateStartMenuEngine() {
        $menu_resources_dir = $this->_component->menuResourcesDir;
        if($menu_resources_dir == null) {
            $menu_background  = __UriFactory::getInstance()->createUri()->setRouteId('resource')->setParameters(array('resource' => 'images/menu/menu_background.gif'))->getUrl();
            $menu_background2 = __UriFactory::getInstance()->createUri()->setRouteId('resource')->setParameters(array('resource' => 'images/menu/menu_background2.gif'))->getUrl();
            $dmb_i            = __UriFactory::getInstance()->createUri()->setRouteId('resource')->setParameters(array('resource' => 'images/menu/dmb_i.gif'))->getUrl();
            $dmb_m            = __UriFactory::getInstance()->createUri()->setRouteId('resource')->setParameters(array('resource' => 'images/menu/dmb_m.gif'))->getUrl();
            $ns_menu          = __UriFactory::getInstance()->createUri()->setRouteId('resource')->setParameters(array('resource' => 'images/menu/nsmenu.js'))->getUrl();
            $ie_menu          = __UriFactory::getInstance()->createUri()->setRouteId('resource')->setParameters(array('resource' => 'images/menu/iemenu.js'))->getUrl();
        }
        else {
            $menu_background  = $menu_resources_dir . '/menu_background.gif';
            $menu_background2 = $menu_resources_dir . '/menu_background2.gif';
            $dmb_i            = $menu_resources_dir . '/dmb_i.gif';
            $dmb_m            = $menu_resources_dir . '/dmb_m.gif';
            $ns_menu          = $menu_resources_dir . '/nsmenu.js';
            $ie_menu          = $menu_resources_dir . '/iemenu.js';
        }
        
        $return_value = <<<CODE
<!-- DHTML Menu Builder Loader Code START -->
<SCRIPT LANGUAGE="JavaScript">
<!-- hide from none JavaScript Browsers 
Image1= new Image();
Image1.src = "$menu_background";
Image2= new Image();
Image2.src = "$menu_background2";
// End Hiding -->
</SCRIPT>  
<div id="dmbri" style="position:absolute;">
<img src="$dmb_i" name="dmbif" width="1" height="1" border="0" alt="">
<img src="$dmb_m" name="dmbjs" width="1" height="1" border="0" alt="">
</div>
<script language="JavaScript" type="text/javascript">
var rimPath=null;var rjsPath=null;var rPath2Root=null;function InitRelCode(){var iImg;var jImg;var tObj;if(!document.layers){iImg=document.images['dmbif'];jImg=document.images['dmbjs'];tObj=jImg;}else{tObj=document.layers['DMBRI'];if(tObj){iImg=tObj.document.images['dmbif'];jImg=tObj.document.images['dmbjs'];}}if(!tObj){window.setTimeout("InitRelCode()",700);return false;}rimPath=_gp(iImg.src);rjsPath=_gp(jImg.src);rPath2Root=rjsPath+"../";return true;}function _purl(u){return xrep(xrep(u,"%%REP%%",rPath2Root),"\\\\","/");}function _fip(img){if(img.src.indexOf("%%REL%%")!=-1) img.src=rimPath+img.src.split("%%REL%%")[1];return img.src;}function _gp(p){return p.substr(0,p.lastIndexOf("/")+1);}function xrep(s,f,n){if(s) s=s.split(f).join(n);return s;}InitRelCode();
</script>
<script language="JavaScript" type="text/javascript">
function LoadMenus() {if(!rjsPath){window.setTimeout("LoadMenus()", 10);return false;}var navVer = navigator.appVersion;
if(navVer.substr(0,3) >= 4)
if((navigator.appName=="Netscape") && (parseInt(navigator.appVersion)==4)) {
document.write('<' + 'script language="JavaScript" type="text/javascript" src="$ns_menu"><\/script\>');
} else {
document.write('<' + 'script language="JavaScript" type="text/javascript" src="$ie_menu"><\/script\>');
}return true;}LoadMenus();</script>
<!-- DHTML Menu Builder Loader Code END -->
<script language="javascript">
\tdmbAPI_Init();

CODE;
        return $return_value;
    }
    
    protected function _generateEndMenuEngine() {
        return "\n</script>\n";
    }    
    
    protected function _getCreateItem($id_page, $href=NULL) {
        if($this->_component->I18nSupport === true) {
            $returnValue = "\ti" . md5($id_page) . " = dmbAPI_addTBItem(1, '" . __ResourceManager::getInstance()->getResource($id_page)->getValue() . "');\n";
        }
        else {
            $returnValue = "\ti" . md5($id_page) . " = dmbAPI_addTBItem(1, '" . $id_page . "');\n";
        }
        if($href!=NULL) {
            $returnValue .="\tdmbAPI_setOnClick(i" . md5($id_page) . ", \"/" . $href . "\");\n";
        }
        return $returnValue;
    }
    
    protected function _getCreateCommand($id_command, $id_group, $href=NULL, $has_subitems = false) {
        if($this->_component->I18nSupport === true) {
            $returnValue = "\ti" . md5($id_command) . " = dmbAPI_addCommand('" . $id_group . "_GRP', '" . __ResourceManager::getInstance()->getResource($id_command)->getValue() . "');\n";
        }
        else {
            $returnValue = "\ti" . md5($id_command) . " = dmbAPI_addCommand('" . $id_group . "_GRP', '" . $id_command . "');\n";
        }
        if($href!=NULL) {
            $returnValue .="\tdmbAPI_setOnClick(i" . md5($id_command) . ", \"/" . $href . "\");\n";
        }
        if($has_subitems == true) {
            
            $img_arrow_normal = __UriFactory::getInstance()->createUri()->setRouteId('resource')->setParameters(array('resource' => '/images/arrows/menu_arrow_normal.gif'))->getUrl();
            $img_arrow_over   = __UriFactory::getInstance()->createUri()->setRouteId('resource')->setParameters(array('resource' => '/images/arrows/menu_arrow_over.gif'))->getUrl();
            $returnValue .= "\tdmbAPI_setImageRightNormal(i" . md5($id_command) . ", '$img_arrow_normal', 4, 7);\n";
            $returnValue .= "\tdmbAPI_setImageRightOver(i" . md5($id_command) . ", '$img_arrow_over', 4, 7);\n";
        }
        return $returnValue;
    }
    
    protected function _getCreateGroup($id_group) {
        $returnValue = "\tdmbAPI_addGroup('" . $id_group . "_GRP');\n";
        return $returnValue;
    }
    
    protected function _attachGroup($id_element, $id_group, $type_element='item') {
        $align=0;
        if($type_element == 'item') {
            $align=0;
        }
        else if($type_element == 'command') {
            $align=6;
        }
        $returnValue = "\tdmbAPI_setOnMouseOver(i" . md5($id_element) . ", '" . $id_group . "_GRP', '', true, " . $align . ");\n";
        return $returnValue;
    }
    
    
    protected function _loadMenuItem($menu_item, $idParentGroup = null)
    {
        $return_value = "";
        $has_access   = true;
        $sections = $menu_item->getSections();
        foreach ($sections as $section) {
            if ($section->getName() == 'menuitem') {
                $id_menu = $section->getAttribute("idMenu");
                $accessType  = $section->getAttribute("accessType");
                if($section->hasAttribute("ref")) {
                    $href = $section->getAttribute("ref");
//                    if(!__AuthorizationManager::getInstance()->hasAccessToUrl($href)) {                    
//                        $href = null;
//                        $has_access = false;
//                    }
                }
                else {
                    $href = null;
                }
                $subitems = $this->_loadMenuItem($section, $id_menu);
                if ($subitems != '') {
                    $hasSubItems = true;
                } else {
                    $hasSubItems = false;
                }

                if ($idParentGroup == null) {
                    $item = $this->_getCreateItem($id_menu, $href);
                    $type = 'item';
                } else {
                    $item = $this->_getCreateCommand($id_menu, $idParentGroup, $href, $hasSubItems);
                    $type = 'command';
                }
                $group = $this->_getCreateGroup($id_menu);

                $attachGroup = $this->_attachGroup($id_menu, $id_menu, $type);
                if ($has_access) {
                    $return_value = $return_value . $item;
                    if ($hasSubItems) {
                        $return_value = $return_value . $group;
                        $return_value = $return_value . $subitems;
                        $return_value = $return_value . $attachGroup;
                    }
                }
            }
        }
        return $return_value;
    }
}
