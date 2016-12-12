<?php
namespace framework\core\Console\Commands;


use framework\core\Router\RoutesCollector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DebugRoutesCommand
 * @package framework\core\Console\Commands
 * 
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class DebugRoutesCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('debug:routes')
            // the short description shown while running "php bin/console list"
            ->setDescription('List available routes.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command helps you to collect and list all available routes of tou application.");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collector = new RoutesCollector();
        $routes = $collector->getRoutes()->getRoutes();

        $__fix = 30;
        foreach ($routes as $route){
            $__name = $route->getName().':';
            $__length = strlen($__name);
            $diff = $__fix - $__length;
            for ($x = 0; $x <= $diff; $x++) {
                $__name .= ' ';
            }
            $output->writeln($__name.$route->getPattern());
        }
    }
}