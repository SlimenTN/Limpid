<?php

namespace framework\core\Console\Commands;

use framework\core\Controller\CrossRoadsRooter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LaunchModuleCommand
 * Create limpid module
 * @package framework\core\Console\Commands
 * 
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class LaunchModuleCommand extends Command
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var string
     */
    private $fullModuleName;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('create:module')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates new module.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to create modules with it's needed directories and files such as routes")

            ->addArgument('module_name', InputArgument::REQUIRED, 'Module\s name.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->moduleName = $input->getArgument('module_name');
        $this->fullModuleName = $this->moduleName.CrossRoadsRooter::MODULE;

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Module Launcher... Limpid is about to prepare your module "'.$this->moduleName.'"',
            '============',
        ]);

        $this->createDirectories();

        $output->writeln('Your module "'.$this->fullModuleName.'" is now ready :)');
    }

    /**
     * Create directories and files of module
     */
    private function createDirectories(){
        $modulePath = __DIR__.'/../../../../app/'.$this->fullModuleName;
        mkdir($modulePath);
        mkdir($modulePath.'/'.CrossRoadsRooter::ENTITY);
        mkdir($modulePath.'/'.CrossRoadsRooter::CONTROLLER);
        mkdir($modulePath.'/'.CrossRoadsRooter::VIEW);
        mkdir($modulePath.'/'.CrossRoadsRooter::REPOSITORY);
        mkdir($modulePath.'/'.CrossRoadsRooter::FORM_DIRECTORY);
        mkdir($modulePath.'/'.CrossRoadsRooter::CONFIG);

        //---create view file-----
        $viewFilePath = $modulePath.'/'.CrossRoadsRooter::VIEW.'/index.html.twig';
        fopen($viewFilePath, 'a');
        $viewContent = file_get_contents($viewFilePath);
        $viewContent .= $this->generateIndexContent();
        file_put_contents($viewFilePath, $viewContent);

        //---create controller file---
        $controllerFilePath = $modulePath.'/'.CrossRoadsRooter::CONTROLLER.'/DefaultController.php';
        fopen($controllerFilePath, 'a');
        $controllerFile = fopen($controllerFilePath,"w");
        $content = $this->generateControllerContent();
        fwrite($controllerFile, $content);
        fclose($controllerFile);
        
        //---create routes file---
        $routesPath = $modulePath.'/'.CrossRoadsRooter::CONFIG.'/routes.php';
        fopen($routesPath, 'a');
        $routesFile = fopen($routesPath,"w");
        $content = $this->generateModuleRoutesContent();
        fwrite($routesFile, $content);
        fclose($routesFile);

        //---add module pointer to modules_routes.php
        $pointerPath = __DIR__.'/../../../config/router.php';
        $pointerFile = fopen($pointerPath, 'c');
        fseek($pointerFile, -2, SEEK_END);
        $content = $this->generateRoutesPointerContent();
        fwrite($pointerFile, $content);
        fclose($pointerFile);
    }

    /**
     * Generate index file's content
     * @return string
     */
    private function generateIndexContent(){
        return '<!DOCTYPE html>'.
'<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>'.$this->moduleName.'</title>
    
    <!--Your css files goes here-->
    {% block css %}{% endblock %}
    
</head>
<body style="font-family: Trebuchet ms">
    
    Your '.$this->fullModuleName.' is ready :)<br><br>
    Limpid provides you a bunch of commands that helps you accelerate your work:<br>
    <code>php console create:module</code>: Helps you generate your module quickly.<br>
    <code>php console create:entity</code>: Helps you generate an entity with it\'s repository based on Doctrine annotations.<br>
    <code>php console create:form</code>: Create a form prototype based on the entity\'s attributes.<br>
    <code>php console update:schema</code>: Update database schema<br>
    <code>php console debug:routes</code>: List all available routes of your application
    
    <!--Your pages goes here-->
    {% block body %}{% endblock %}
    
    <!--Your scripts goes here-->
    {% block js %}{% endblock %}
</body>
</html>';
    }
    
    /**
     * Generate controller content
     * @return string
     */
    private function generateControllerContent(){
        return '<?php
namespace app\\'.$this->fullModuleName.'\Controller;

use framework\core\Controller\AppController;

class DefaultController extends AppController
{
    /**
    * index command
    */
    public function indexCommand(){
        $this->paintView(\''.$this->moduleName.':index.html.twig\');
    }
}';
    }

    /**
     * Generate module's routes file content
     * @return string
     */
    private function generateModuleRoutesContent(){
        return '<?php

return [
    \'index_route\' => [
        \'pattern\' => \'/\',
        \'command\' => \''.$this->moduleName.'_Default:index\',
    ],
];';
    }

    /**
     * Update modules_routes.php file with new module
     * @return string
     */
    private function generateRoutesPointerContent(){
        return '
    [
        //---Points on '.$this->fullModuleName.'
        \'module\' => \''.$this->moduleName.'\',
        \'prefix\' => \'/'.strtolower($this->moduleName).'\'
    ],
];';
    }

}