<?php
namespace framework\core\Router;
use framework\core\Controller\CrossRoadsRooter;

/**
 * Class RoutesCollector
 * Collect and organize routes of each module so Limpid can recognize them
 * @package framework\core\Router
 *
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class RoutesCollector
{
    /**
     * Array of collected routes (type: framework\core\Router)
     * @var ListRoutes
     */
    private $routes;

    public function __construct()
    {
        $this->routes = new ListRoutes();
        $this->collect();
    }

    /**
     * Collect routes from modules
     */
    private function collect(){
        $table = include __DIR__.'/../../config/router.php';
        foreach ($table as $modulePointer){
            
            if(!array_key_exists('module', $modulePointer) || !array_key_exists('prefix', $modulePointer)){
                throw new \Exception('Please check the correct syntax of modules_routes.php keys "module" or "prefix" is missing !');
            }
            
            $module = $modulePointer['module'];
            $prefix = ($modulePointer['prefix'] == '/') ? '' : $modulePointer['prefix'];
            $routesFile = CrossRoadsRooter::getRoutesFiles($module);

            foreach ($routesFile as $name => $route){
                $this->routes->addRoute(new Route(
                    $name,
                    $prefix.$route['pattern'],
                    $route['command']
                ));
            }
        }
        
    }

    /**
     * Get collected routes
     * @return ListRoutes
     */
    public function getRoutes(){
        return $this->routes;
    }
}