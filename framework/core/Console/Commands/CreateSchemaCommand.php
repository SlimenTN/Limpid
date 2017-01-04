<?php
namespace framework\core\Console\Commands;


use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateSchemaCommand
 * @package framework\core\Console\Commands
 *
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class CreateSchemaCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "console")
            ->setName('create:schema')
            // the short description shown while running "php console list"
            ->setDescription('Create database.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command helps you to generate database based on the informations given in the file framework/config/AppParameters.php .");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating database...');
        $settings = include __DIR__ . '/../../../config/settings.php';
        $tmConn = DriverManager::getConnection(array(
            'driver' => 'pdo_mysql',
            'user' => $settings['doctrine']['user'],
            'password' => $settings['doctrine']['password'],
            'host' => $settings['doctrine']['host'],
        ));
        $tmConn->getSchemaManager()->createDatabase($settings['doctrine']['database']);
        
        $output->writeln('Database has been successfully created.');
    }
    
}