<?php
namespace framework\core\Controller;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use framework\core\Exception\NotFoundException;
use framework\core\Exception\RuntimeException;

/**
 * Class CommandExecutor
 * Class responsible of the parsing and execution 
 * of a specific command inside a specific controller
 * @package framework\core\Controller
 * 
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class CommandExecutor
{
    /**
     * @var string
     */
    private $pathToCommand;

    /**
     * @var array
     */
    private $parameters;

    /***
     * @var DebugStack
     */
    private $debugStack = null;
    
    
    
    function __construct($pathToCommand, $parameters = array())
    {
        $this->pathToCommand = $pathToCommand;
        $this->parameters = $parameters;
    }

    /**
     * Fetch and execute user's command
     */
    public function execute(DebugStack $debugStack = null){
        $this->debugStack = $debugStack;
        list($module_controller, $command) = explode(":", $this->pathToCommand);
        list($module, $controller) = explode("_", $module_controller);

        $class = $this->buildClassName($module, $controller);

        $commandName = $this->buildCommandName($command);

        $this->executeCommand($class, $commandName);
    }

    /**
     * Build class name
     * @param $module
     * @param $controller
     * @return string
     */
    private function buildClassName($module, $controller){
        return 'app\\'.$module.CrossRoadsRooter::MODULE.'\\'.CrossRoadsRooter::CONTROLLER.'\\'.$controller.'Controller';
    }

    /**
     * Build command name
     * @param $command
     * @return string
     */
    private function buildCommandName($command){
        return $command.CrossRoadsRooter::COMMAND;
    }

    /**
     * Execute command
     * @param $class
     * @param $commandName
     */
    private function executeCommand($controllerClass, $commandName){
        $controller = new $controllerClass();
        if($controller instanceof AppController){
            if(method_exists($controller, $commandName)){
                $this->runDebugStack($controller);
                call_user_func_array(array($controller, $commandName), $this->parameters);
            }else{
                throw new RuntimeException('Error: the command '.$commandName.' is not defined in the controller '.$controllerClass.'!');
            }
        }else{
            throw new \Exception('Your controller must extends from framework\core\Controller\AppController class');
        }
    }
    
    private function runDebugStack(AppController $controller){
        if($this->debugStack != null) 
            $controller->getEntityManager()->getConfiguration()->setSQLLogger($this->debugStack);
    }

    /**
     * @return DebugStack
     */
    public function getDebugStack()
    {
        return $this->debugStack;
    }
    
    
}