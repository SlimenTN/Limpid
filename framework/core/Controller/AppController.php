<?php
namespace framework\core\Controller;

/**
 * Class AppController
 * @package framework\core\Controller
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class AppController extends GlobalContainer{

    /**
     * @param $templateDestination
     * @param array $parameters
     */
    protected function paintView($templateDestination, $parameters = array()){
        list($module, $view) = explode(":", $templateDestination);
        $this->twigLauncher->addTemplate(CrossRoadsRooter::generateModuleViewTemplates($module));
        echo $this->twigLauncher->paintView($view, $parameters);
    }
    
}