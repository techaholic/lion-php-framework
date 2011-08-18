<?php

class __DocumentationController extends __LionController {
    
    protected $_page = null;
    protected $_doc_content = null;
    protected $_doc_directory = null;
    
    public function preExecute() {
        $request = __FrontController::getInstance()->getRequest();
        if($request->hasParameter('page')) {
            $this->_page = $request->getParameter('page');
            $doc_file = LION_DIR . DIRECTORY_SEPARATOR . 'documentation' . DIRECTORY_SEPARATOR . $this->_doc_directory . DIRECTORY_SEPARATOR . $this->_page . '.html';
            if(is_file($doc_file) && is_readable($doc_file)) {
               $doc_content = file_get_contents($doc_file);
               $doc_content = preg_replace('/(href=\"(?!http).+?)\.html/', '$1.lion', $doc_content);
               $doc_content = preg_replace('/src\="(\.\.\/)*images/', 'src="' . __UrlHelper::resolveUrl('resources/documentation/images'), $doc_content);
               $this->_doc_content = $doc_content;
               if(preg_match('/\<h1\>([^<]+)\<\/h1\>/', $doc_content, $matched)) {
                   $title = $matched[1];
                   $title = str_replace('Class:', 'Class ', $title);
                   $request->addParameter('header::title', $title);
               }
            }
        }
        parent::preExecute();
    }
    
    public function defaultAction() {
        $mav = new __ModelAndView('documentation');
        return $mav;
    }
    
    public function showDocumentationAction() {
        $mav = new __ModelAndView('documentation');
        if($this->_page != null && $this->_doc_content != null) {
            $mav->doc_content = $this->_doc_content;
            if(strpos($this->_page, '.pkg') !== false || $this->_page == 'index') {
                $mav->show_toc = true;
                $doc_toc = LION_DIR . DIRECTORY_SEPARATOR . 'documentation' . DIRECTORY_SEPARATOR . $this->_doc_directory . DIRECTORY_SEPARATOR . 'Lion' . DIRECTORY_SEPARATOR . 'tutorial_Lion.Toc.pkg.html';
                if(is_file($doc_toc) && is_readable($doc_toc)) {
                   $toc_content = file_get_contents($doc_toc);
                   $toc_content = str_replace('../', __UrlHelper::resolveUrl('documentation/'), $toc_content);
                   $toc_content = preg_replace('/(\/documentation\/.+?)\.html/', '$1.lion', $toc_content);
                   $mav->toc = $toc_content;
                }
            }
            else {
                $mav->show_toc = false;
            }
        }
        return $mav;
    }

    
}