<?php
namespace framework\core\Controller;

use framework\config\AppParamters;
use framework\config\AppRoutes;
use framework\core\Router\RoutesCollector;
use framework\core\Router\URLParser;


/**
 * Class CrossRoadsRooter
 * Router manager that decides which function to fire based on given url
 * @package framework\core\Controller
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class CrossRoadsRooter{
    
    const MODULE = 'Module';
    const CONTROLLER = 'Controller';
    const REPOSITORY = 'Repository';
    const ENTITY = 'Entity';
    const COMMAND = 'Command';
    const VIEW = 'View';
    const FORM_DIRECTORY = 'FormPrototype';
    const FORM = 'Form';
    const CONFIG = 'Config';
    
    public static $LANG = AppParamters::DEFAULT_LANG;
    
    public static $CURRENT_ROUTE = '';
    /**
     * @var array
     */
    private $routes;
    
    /**
     * @var string
     */
    private $request;

    /**
     * @var array
     */
    private $commandToExecute;
    
    /**
     * CrossRoadsRooter constructor.
     * @param array $root
     */
    function __construct()
    {
        $collector = new RoutesCollector();
        $this->routes = $collector->getRoutes();
        $this->request = $this->findUserRequest();
    }

    /**
     * @return string
     */
    private function findUserRequest(){
        $serverDir = (dirname($_SERVER['PHP_SELF']) == '/') ? '' : dirname($_SERVER['PHP_SELF']);
        $request =  str_replace($serverDir, '', str_replace('%20', ' ', $_SERVER['REQUEST_URI']));
       return $request;
    }

    /**
     * Parse user's request and get the right command to execute
     */
    public function parseRequest(){
        $this->commandToExecute = URLParser::parse($this->request, $this->routes);
        if($this->commandToExecute !== null){
            self::$CURRENT_ROUTE = $this->commandToExecute['route_name'];
            $this->executeURLCommand();
        }else{
            if(AppParamters::PAGE_NOT_FOUND_ROUTE != ''){
                self::redirectToRoute(AppParamters::PAGE_NOT_FOUND_ROUTE);
            }else{
//                header('HTTP/1.0 404 Not Found');
//                echo "<h1>404 Not Found</h1>";
//                echo "The page that you have requested could not be found.";
//                exit;
                throw new \Exception('No route found for the url "'.$this->request.'". Please check your routes!');
            }

        }
    }

    /**
     * Fetch and execute user's command
     */
    private function executeURLCommand(){
        list($module_controller, $command) = explode(":", $this->commandToExecute['command']);
        list($module, $controller) = explode("_", $module_controller);

        $class = $this->buildClassName($module, $controller);

        $commandName = $this->buildCommandName($command);

        $this->executeCommande($class, $commandName);
    }

    /**
     * Build class name
     * @param $module
     * @param $controller
     * @return string
     */
    private function buildClassName($module, $controller){
        return 'app\\'.$module.self::MODULE.'\\'.self::CONTROLLER.'\\'.$controller.'Controller';
    }

    /**
     * Build command name
     * @param $command
     * @return string
     */
    private function buildCommandName($command){
        return $command.self::COMMAND;
    }

    /**
     * Execute command
     * @param $class
     * @param $commandName
     */
    private function executeCommande($controllerClass, $commandName){
        $controller = new $controllerClass();
        call_user_func_array(array($controller, $commandName), $this->commandToExecute['parameters']);
    }

    /**
     * @param $module
     * @param $entity
     * @return string
     */
    public static function generateRepositoryNamespace($module, $entity){
        return 'app\\' . $module . self::MODULE . '\\' . self::ENTITY . '\\' . $entity ;
    }

    /**
     * @param $module
     * @param $entity
     * @return string
     */
    public static function generateFormPrototypeNamespace($module, $entity){
        return 'app\\' . $module . self::MODULE . '\\' . self::FORM_DIRECTORY . '\\' . $entity . self::FORM ;
    }

    /**
     * @param $module
     * @param $entity
     * @return string
     */
    public static function generateEntityNamespace($module, $entity){
        return 'app\\' . $module . self::MODULE . '\\' . self::ENTITY . '\\' . $entity;
    }

    /**
     * @param $module
     * @return string
     */
    public static function generateModuleViewTemplates($module){
        return __DIR__.'/../../../app/'.$module.self::MODULE.'/View/';
    }

    /**
     * Find translation book for given module
     * @param $module
     * @return mixed
     */
    public static function getTranslationBook($module){
        return include __DIR__.'/../../../app/'.$module.self::MODULE.'/Translator/book.php';
    }

    /**
     * Get related routes of given module
     * @param $module
     * @return mixed
     */
    public static function getRoutesFiles($module){
        return include __DIR__.'/../../../app/'.$module.self::MODULE.'/Config/routes.php';
    }

    /**
     * Get URL of given route name
     * @param $routeName
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public static function getURLOfRoute($routeName, $params = array()){
        $routes = new AppRoutes();
        $url = null;
        foreach ($routes->getRoutes()->getRoutes() as $route){
            if($route->getName() === $routeName){
                $url = $route->getPattern();

                if(count($params) > 0){
                    $tab = explode('/', $url);
                    $i  = 0;
                    foreach ($tab as $segment){
                        if(strpos($segment, '%') !== false){
                            $tab[$i] = $params[str_replace('%', '', $segment)];
                        }
                        $i++;
                    }
                    $url = implode('/', $tab);
                }
            }
        }
        
        if($url == null) throw new \Exception('Exception in '.__METHOD__.'(): We can\'t find a declared route with the name "'.$routeName.'" ');

        if (AppParamters::TRANSLATOR_ENABLED) $url = '/' . CrossRoadsRooter::$LANG . $url;

        $exclude = '/app_launcher.php';
        $hote = str_replace($exclude, '', $_SERVER['PHP_SELF']);
        return $hote.$url;
    }

    /**
     * Redirect to url by route's name
     * @param $name
     * @param array $params
     * @throws \Exception
     */
    public static function redirectToRoute($name, $params = array()){
        $url = self::getURLOfRoute($name, $params);
        header('Location: '.$url);
        exit;
    }

    /**
     * @return string
     */
    public static function getHote(){
        $exclude = '/app_launcher.php';
        $hote = str_replace($exclude, '', $_SERVER['PHP_SELF']);
        return $hote;
    }
}