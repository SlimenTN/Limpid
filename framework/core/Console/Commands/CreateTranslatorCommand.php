<?php
namespace framework\core\Console\Commands;

use framework\core\Controller\CrossRoadsRooter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class CreateTranslatorCommand
 * @package framework\core\Console\Commands
 * 
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class CreateTranslatorCommand extends Command
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var string
     */
    private $fullModuleName;
    
    protected function configure()
    {
        $this
            // the name of the command (the part after "console")
            ->setName('create:translator')
            // the short description shown while running "php console list"
            ->setDescription('Create Translation book.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command helps you to generate a translation book for selected mosule")
            ->addArgument('module_name', InputArgument::REQUIRED, 'Module name');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $this->moduleName = $input->getArgument('module_name');
        $this->fullModuleName = $this->moduleName.CrossRoadsRooter::MODULE;
        
        $path = __DIR__.'/../../../../app/'.$this->fullModuleName;

        //--Check if given module exist
        if(!is_dir(__DIR__.'/../../../../app/'.$this->fullModuleName)){
            $output->writeln('We can\'t find the module "'.$this->fullModuleName.'" in app directory!');
            return;
        }
        
        //--make sure the book does not exist
        if(file_exists($path.'/'.CrossRoadsRooter::TRANSLATOR.'/book.php')){
            $output->writeln('You have already created a translation book for this module!');
            return;
        }

        $output->writeln('Write the languages you want to use under this format: fr,en,ar ...');
        $langQuestion = new Question('languages: ');
        $this->languages = explode(',', $helper->ask($input, $output, $langQuestion));


        mkdir($path.'/'.CrossRoadsRooter::TRANSLATOR);
        $this->generate();
        $output->writeln('Translation book has been successfully generated.');
        $output->writeln('Don\'t forget to enable Limpid\'s translation service by putting "TRANSLATOR_ENABLED" to true in AppParameters.php file.');
    }

    private function generate(){
        $viewFilePath = __DIR__.'/../../../../app/'.$this->fullModuleName.'/'.CrossRoadsRooter::TRANSLATOR.'/book.php';
        fopen($viewFilePath, 'a');
        $viewContent = $this->writeContent();
        file_put_contents($viewFilePath, $viewContent);
    }

    private function writeContent(){
        $content = "<?php

return [
    'word.key' => [//---key word to put it in your views ".$this->buildLanguages()."
    ],
];";

        return $content;
    }

    private function buildLanguages(){
        $lang = '';

        foreach ($this->languages as $ln){
            $lang .= "
        '".$ln."' => '".$ln." word',//-Translation word";
        }
        return $lang;
    }
}