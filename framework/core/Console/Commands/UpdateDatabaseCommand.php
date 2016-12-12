<?php
namespace framework\core\Console\Commands;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use framework\core\Controller\CrossRoadsRooter;
use framework\core\Repository\DoctrineLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateDatabaseCommand
 * @package framework\core\Console\Commands
 *
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class UpdateDatabaseCommand extends Command
{

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('update:schema')
            // the short description shown while running "php bin/console list"
            ->setDescription('Update database schema.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("By firing this command limpid will check all entities declared in 
            all modules then update database using doctrin's schema update command.")
            ->addArgument('order');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $order = $input->getArgument('order');
        $docLoader = new DoctrineLoader();
        $em = $docLoader->getEntityManager();
        $schemaTool = new SchemaTool($em);

        $entities = $this->getAvailableEntities();
        $classes = array();
        
        foreach ($entities as $entity){
            $classes[] = $em->getClassMetadata($entity);
        }

        if($order == null){
            $res = $schemaTool->getUpdateSchemaSql($classes);
            if(!empty($res)){
                foreach ($res as $o){
                    $output->writeln($o);
                }
            }else{
                $output->writeln('Database is up to date.');
            }
        }else if($order == 'force'){
            $output->writeln('Updating database...');
            $schemaTool->updateSchema($classes);
            $output->writeln('Database has been updated successfully.');
        }else{
            $output->writeln('Unknown option '.$order.'! If you want to force database update tap "force".');
        }

    }

    /**
     * @return array
     */
    private function getAvailableEntities(){
        $availableModules = $this->getAvailableModules();
        
        $entities = array();
        foreach ($availableModules as $module) {
            $entitiesFiles = scandir(__DIR__ . '/../../../../app/' . $module . '/' . CrossRoadsRooter::ENTITY);
            foreach ($entitiesFiles as $entityFile) {
                if ($entityFile != '.' && $entityFile != '..') {

                    $entityNameSpace = CrossRoadsRooter::generateEntityNamespace(
                        str_replace(CrossRoadsRooter::MODULE, '', $module),
                        str_replace('.php', '', $entityFile)
                    );
                    $entities[] = $entityNameSpace;
                }
            }
        }
        return $entities;
    }

    /**
     * @return array
     */
    public function getAvailableModules()
    {
        $directories = scandir(__DIR__ . '/../../../../app');
        $modules = array();

        foreach ($directories as $dir) {
            if (strpos($dir, CrossRoadsRooter::MODULE) !== false) {
                $modules[] = $dir;
            }
        }

        return $modules;
    }
}