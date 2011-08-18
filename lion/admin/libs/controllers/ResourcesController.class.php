<?php

class __ResourcesController extends __LionController {  
    
    public function defaultAction()
    {
        $model_manager   = __ModelManager::instance();

        //Create a __CriteriaParameters instance to capture criteria parameters from the user request:
        $criteria_parameters = new __CriteriaParameters();
        $criteria_parameters->addValidSubmitCode(__CodeGenerator::getSubmitCode('resources', 'search_resources'));
        $criteria_parameters->addValidSubmitCode(__CodeGenerator::getSubmitCode('resources_table'));
        //Add the correspondent metadata:
        $criteria_parameters->addParameterMetadata('key', array('alias' => 'resource_key'));
        $criteria_parameters->addParametersValues( __Client::getInstance()->getRequest() );  
        //We will request 20 + 1 in order to know if there are more than 20 rows or not:
        $criteria_parameters->setPageSize(20);

        //Now get the requested resources list:
        $resources = $model_manager->getResources($criteria_parameters);

        //Finally will add all model information to the __ModelAndView instance to be returned
        $model_and_view = new __ModelAndView('resources');
        $model_and_view->resources = $resources;
        return $model_and_view;
    }
}
