<?php

namespace framework\core\Twig\LimpidExtensions;


use framework\core\Controller\CrossRoadsRooter;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class CurrentRouteExtension
 * Get current active route's name
 * @package framework\core\Twig\LimpidExtensions
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class CurrentRouteExtension implements TwigCustomExtension
{

    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'current_route',
            array($this, 'getCurrentRoute')
        );
    }
    
    public function getCurrentRoute(){
        return CrossRoadsRooter::$CURRENT_ROUTE;
    }
}