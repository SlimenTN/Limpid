<?php
namespace framework\core\Console\Commands;


use Doctrine\DBAL\DriverManager;
use framework\config\AppParamters;
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

        $tmConn = DriverManager::getConnection(array(
            'driver' => 'pdo_mysql',
            'user' => AppParamters::DB_USER,
            'password' => AppParamters::DB_PASSWORD,
            'host' => AppParamters::DB_HOST,
        ));
        $tmConn->getSchemaManager()->createDatabase(AppParamters::DB_NAME);
        
        $output->writeln('Database has been successfully created.');
    }
    
}