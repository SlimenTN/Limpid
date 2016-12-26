<?php
namespace framework\core\Controller;

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
    private $pathToCmmand;

    /**
     * @var array
     */
    private $parameters;
    
    function __construct($pathToCommand, $parameters = array())
    {
        $this->pathToCmmand = $pathToCommand;
        $this->parameters = $parameters;
    }

    /**
     * Fetch and execute user's command
     */
    public function execute(){
        list($module_controller, $command) = explode(":", $this->pathToCmmand);
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
    private function executeCommande($controllerClass, $commandName){
        $controller = new $controllerClass();
        if($controller instanceof AppController){
            if(method_exists($controller, $commandName)){
                call_user_func_array(array($controller, $commandName), $this->parameters);
            }else{
                throw new \Exception('Error: the command '.$commandName.' is not defined in the controller '.$controllerClass.'!');
            }
        }else{
            throw new \Exception('Your controller must extends from framework\core\Controller\AppController class');
        }
    }
}