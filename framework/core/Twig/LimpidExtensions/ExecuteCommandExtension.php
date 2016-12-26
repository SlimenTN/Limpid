<?php
namespace framework\core\Twig\LimpidExtensions;

use framework\core\Controller\CommandExecutor;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class ExecuteCommandExtension
 * Execute specific command from selected controller
 * @package framework\core\Twig\LimpidExtensions
 * 
 * 
 */
class ExecuteCommandExtension implements TwigCustomExtension
{

    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'execute',
            array($this, 'execute')
        );
    }

    /**
     * Execute specific command inside controller
     * ex: HelloLimpid_Default:command
     * @param string $pathToCommand
     */
    public function execute($pathToCommand){
        $executor = new CommandExecutor($pathToCommand);
        $executor->execute();
    }
}