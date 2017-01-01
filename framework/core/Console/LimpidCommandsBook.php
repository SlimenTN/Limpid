<?php
namespace framework\core\Console;


/**
 * Class LimpidCommandsBook
 * @package framework\core\Console
 */
class LimpidCommandsBook
{
    public static $COMMANDS = array(
        'framework\core\Console\Commands\LaunchModuleCommand',
        'framework\core\Console\Commands\GenerateEntityCommand',
        'framework\core\Console\Commands\UpdateDatabaseCommand',
        'framework\core\Console\Commands\DebugRoutesCommand',
        'framework\core\Console\Commands\GenerateFormCommand',
        'framework\core\Console\Commands\CreateSchemaCommand',
        'framework\core\Console\Commands\CreateTranslatorCommand',
        'framework\core\Console\Commands\CRUDGeneratorCommand',
    );
}