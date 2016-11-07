<?php
namespace framework\config;

use framework\core\Router\ListRoutes;
use framework\core\Router\Route;

class AppRoutes{
    
    private $routes;
    
    public function __construct(){
        $this->routes = new ListRoutes();
        $this->prepeareRoutes();
    }

    /**
     * @return ListRoutes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * -------------------------------------------------
     * You can declare your routes here
     * -------------------------------------------------
     */
    public function prepeareRoutes(){
        $this->routes->addRoute(new Route(
            'hello',
            '/',
            'HelloLimpid_Default:hello'
        ));
    }
}