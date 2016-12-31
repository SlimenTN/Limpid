<?php
namespace framework\core\Controller;

use Doctrine\DBAL\Logging\DebugStack;
use framework\core\Exception\NotFoundException;
use framework\core\Repository\DoctrineLoader;
use framework\core\Router\RoutesCollector;
use framework\core\Router\URLParser;
use Symfony\Component\Debug\Debug;


/**
 * Class CrossRoadsRooter
 * Router manager that decides which function to fire based on given url
 * @package framework\core\Controller
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class CrossRoadsRooter
{

    const MODULE = 'Module';
    const CONTROLLER = 'Controller';
    const REPOSITORY = 'Repository';
    const ENTITY = 'Entity';
    const COMMAND = 'Command';
    const VIEW = 'View';
    const FORM_DIRECTORY = 'FormPrototype';
    const FORM = 'Form';
    const CONFIG = 'Config';
    const TRANSLATOR = 'Translator';
    const TWIG = 'Twig';
    public static $prod = true;
    public static $SETTINGS;
    public static $LANG;

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
     * @var int
     */
    private $starExecutionTime;

    /**
     * @var DebugStack
     */
    private $sqlStack = null;

    /**
     * CrossRoadsRooter constructor.
     * @param array $root
     */
    function __construct($prod = false)
    {
        self::$SETTINGS = include __DIR__ . '/../../config/settings.php';
        self::$LANG = self::$SETTINGS['translator']['default_lang'];
        self::$prod = $prod;

        if (!self::$prod) {
            $this->executeDebugger();
        }

        $collector = new RoutesCollector();
        $this->routes = $collector->getRoutes();
        $this->request = $this->findUserRequest();

    }

    /**
     * @return string
     */
    private function findUserRequest()
    {
        $serverDir = (dirname($_SERVER['PHP_SELF']) == '/') ? '' : dirname($_SERVER['PHP_SELF']);
        $request = str_replace($serverDir, '', str_replace('%20', ' ', $_SERVER['REQUEST_URI']));
        return $request;
    }

    /**
     * Parse user's request and get the right command to execute
     */
    public function parseRequest()
    {
        $this->commandToExecute = URLParser::parse($this->request, $this->routes);
        if ($this->commandToExecute !== null) {
            self::$CURRENT_ROUTE = $this->commandToExecute['route_name'];
            $executor = new CommandExecutor($this->commandToExecute['command'], $this->commandToExecute['parameters']);
            $executor->execute($this->sqlStack);
            $this->sqlStack = $executor->getDebugStack();
            $this->terminate();
        } else {
            throw new NotFoundException('No route found for the url "' . $this->request . '". Please check your routes!');
        }
    }

    /**
     * Enable log mode
     * to save logs data
     */
    private function executeDebugger()
    {
        $this->starExecutionTime = microtime(true);
        Debug::enable();
        $this->sqlStack = new DebugStack();
    }
    
    /**
     * Terminate script execution
     * and display logs barre
     */
    private function terminate()
    {
        //If it's debug mode display logs
        if(!self::$prod){
            $timeend = microtime(true);
            $time = $timeend - $this->starExecutionTime;
            $page_load_time = number_format($time, 3);

            $logBarre = '<div style="
                        width: 100%; 
                        background: #297157; 
                        color: #fff;
                        position: fixed;
                        bottom: 0;">';
            $logBarre .= '<div style="width: 210px;
                        float: left;
                        padding: 5px;
                        background: #13324d;">Script executed in ' . $page_load_time . ' sec</div>';
            $logBarre .= '<div style="width: 90px;
                        float: left;
                        padding: 5px;
                        background: #297157;">Queries: ' . $this->sqlStack->currentQuery.'</div>';
            $logBarre .= '</div>';
            echo $logBarre;
        }
    }

    /**
     * @param $module
     * @param $entity
     * @return string
     */
    public static function generateRepositoryNamespace($module, $entity)
    {
        return 'app\\' . $module . self::MODULE . '\\' . self::ENTITY . '\\' . $entity;
    }

    /**
     * @param $module
     * @param $entity
     * @return string
     */
    public static function generateFormPrototypeNamespace($module, $entity)
    {
        return 'app\\' . $module . self::MODULE . '\\' . self::FORM_DIRECTORY . '\\' . $entity . self::FORM;
    }

    /**
     * @param $module
     * @param $entity
     * @return string
     */
    public static function generateEntityNamespace($module, $entity)
    {
        return 'app\\' . $module . self::MODULE . '\\' . self::ENTITY . '\\' . $entity;
    }

    /**
     * @param $module
     * @return string
     */
    public static function generateModuleViewTemplates($module)
    {
        return __DIR__ . '/../../../app/' . $module . self::MODULE . '/View/';
    }

    /**
     * Find translation book for given module
     * @param $module
     * @return mixed
     */
    public static function getTranslationBook($module)
    {
        return include __DIR__ . '/../../../app/' . $module . self::MODULE . '/' . CrossRoadsRooter::TRANSLATOR . '/book.php';
    }

    /**
     * Get related routes of given module
     * @param $module
     * @return mixed
     */
    public static function getRoutesFiles($module)
    {
        return include __DIR__ . '/../../../app/' . $module . self::MODULE . '/Config/routes.php';
    }

    /**
     * Get URL of given route name
     * @param $routeName
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public static function getURLOfRoute($routeName, $params = array())
    {
        $routes = new RoutesCollector();
        $url = null;
        foreach ($routes->getRoutes()->getRoutes() as $route) {
            if ($route->getName() === $routeName) {
                $url = $route->getPattern();

                if (count($params) > 0) {
                    $tab = explode('/', $url);
                    $i = 0;
                    foreach ($tab as $segment) {
                        if (strpos($segment, '%') !== false) {
                            $tab[$i] = $params[str_replace('%', '', $segment)];
                        }
                        $i++;
                    }
                    $url = implode('/', $tab);
                }
            }
        }

        if ($url == null) throw new \Exception('Exception in ' . __METHOD__ . '(): We can\'t find a declared route with the name "' . $routeName . '" ');

        if (self::$SETTINGS['translator']['enabled']) $url = '/' . CrossRoadsRooter::$LANG . $url;

        $exclude = '/app_launcher.php';
        $hote = str_replace($exclude, '', $_SERVER['PHP_SELF']);
        return $hote . $url;
    }

    /**
     * Redirect to url by route's name
     * @param $name
     * @param array $params
     * @throws \Exception
     */
    public static function redirectToRoute($name, $params = array())
    {
        $url = self::getURLOfRoute($name, $params);
        header('Location: ' . $url);
        exit;
    }

    /**
     * @return string
     */
    public static function getHote()
    {
        $exclude = '/app_launcher.php';
        $hote = str_replace($exclude, '', $_SERVER['PHP_SELF']);
        return $hote;
    }
}