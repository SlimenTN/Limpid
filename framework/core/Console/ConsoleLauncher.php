<?php
namespace framework\core\Console;


use Symfony\Component\Console\Application;

/**
 * Class ConsoleLauncher
 * Collect commands and prepare console
 * @package framework\core\Console
 * 
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class ConsoleLauncher
{
    /**
     * @var Application
     */
    private $console;
    
    public function __construct()
    {
        $this->console = new Application('Limpid framework', '1.0.0');
        $this->collectAndRegisterCommands();
    }

    /**
     * Load commands book and register commands
     */
    private function collectAndRegisterCommands(){
        $commands = LimpidCommandsBook::$COMMANDS;
        foreach ($commands as $cmd){
            $this->console->add(new $cmd());
        }
    }
    
    public function launch(){
        $this->console->run();
    }
}