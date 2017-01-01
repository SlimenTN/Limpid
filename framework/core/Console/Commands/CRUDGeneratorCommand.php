<?php
namespace framework\core\Console\Commands;


use framework\core\Controller\CrossRoadsRooter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CRUDGeneratorCommand extends Command
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $fullModule;

    /**
     * @var string
     */
    private $entity;
    
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('generate:crud')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates entity\'s crud functions.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to create crud (create, read, update and delete) functions for selected entity.")

            ->addArgument('entity_name', InputArgument::REQUIRED, 'Entity\'s name.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tab = explode(':', $input->getArgument('entity_name'));

        //----Check if the input has been wrote in the right format
        if(count($tab) != 2){
            $output->writeln('Entity\'s must be in this format "Module:Entity"');
            return;
        }

        $this->module = $tab[0];
        $this->entity = $tab[1];
        $this->fullModule = $this->module.CrossRoadsRooter::MODULE;

        //--Check if given module exist
        if(!is_dir(__DIR__.'/../../../../app/'.$this->fullModule)){
            $output->writeln('We can\'t find the module "'.$this->fullModule.'" in app directory!');
            return;
        }

        //--Check if entity exist
        if(!file_exists(__DIR__.'/../../../../app/'.$this->fullModule.'/'.CrossRoadsRooter::ENTITY.'/'.$this->entity.'.php')){
            $output->writeln('We can\'t find "'.$this->entity.'.php"! Please check your entities directory.');
            return;
        }

        //--Make sure prototype does not exist
        if(file_exists(__DIR__.'/../../../../app/'.$this->fullModule.'/'.CrossRoadsRooter::FORM_DIRECTORY.'/'.$this->entity.CrossRoadsRooter::FORM.'.php')){
            $this->proceed();
        }else{
            $formGenerator = new GenerateFormCommand();
            $formGenerator
                ->setEntity($this->entity)
                ->setModule($this->module)
                ->setFullModule($this->fullModule)
            ;
            $formGenerator->generatePrototype();
            $this->proceed();
        }
    }
    
    private function proceed(){
        
    }
}