<?php
namespace framework\config;

/**
 * Class ConsoleCommandsBook
 * All console's commands goes here
 * @package framework\config
 */
class ConsoleCommandsBook
{
    public static $COMMANDS = array(
        /**
         * Limpid's commands-------------------
         */
        'framework\core\Console\Commands\LaunchModuleCommand',

        /**
         * You can add your commands here--------------
         *
         * ex: 'app\HelloWorldModule\Command\MyNewCommand',
         */
    );
}