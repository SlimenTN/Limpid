<?php

namespace framework\core\Router;

/**
 * Class ListRoutes
 * Where we store list of urls
 * @package framework\core\Router
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class ListRoutes{

    /**
     * @var array
     */
    private $routes = array();

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param array $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }
    
    public function addRoute(Route $route){
        $this->routes[] = $route;
    }
}