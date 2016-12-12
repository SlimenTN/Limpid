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
        'framework\core\Console\Commands\GenerateEntityCommand',
        'framework\core\Console\Commands\UpdateDatabaseCommand',
        'framework\core\Console\Commands\DebugRoutesCommand',
        'framework\core\Console\Commands\GenerateFormCommand',
        'framework\core\Console\Commands\CreateSchemaCommand',
        'framework\core\Console\Commands\CreateTranslatorCommand',

        /**
         * You can add your commands here--------------
         *
         * ex: 'app\HelloWorldModule\Command\MyNewCommand',
         */
    );
}