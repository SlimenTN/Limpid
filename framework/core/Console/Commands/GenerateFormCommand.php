<?php
namespace framework\core\Console\Commands;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Column;
use framework\core\Controller\CrossRoadsRooter;
use framework\core\Repository\DoctrineLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateFormCommand
 * @package framework\core\Console\Commands
 * 
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class GenerateFormCommand extends Command
{
    /**
     * @var string
     */
    private $entity;

    /**
     * @var \ReflectionProperty
     */
    private $fields;

    /**
     * Define the primary key of the entity so it will not be considered in the form builder
     * @var string
     */
    private $primaryKey;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;
    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $fullModule;

    /**
     * Map types to right fields
     * @var array
     */
    private $typeFieldsMapping = array(
        'string' => 'text',
        'text' => 'textarea',
        'boolean' => 'checkbox',
        'integer' => 'numeric',
        'date' => 'date'
    );
    
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('create:form')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates an entity form.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command helps you create a FormPrototype for a specific entity.")

            ->addArgument('entity_name', InputArgument::REQUIRED, 'Entity\'s name.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tab = explode(':', $input->getArgument('entity_name'));
        if(count($tab) != 2){
            $output->writeln('Please enter the entity\'s name under this format "Module:Entity"');
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
            $output->writeln('You have already create a prototype for this entity');
            return;
        }

        $className = 'app\\'.$this->fullModule.'\\'.CrossRoadsRooter::ENTITY.'\\'.$this->entity;
        
        $doc = new DoctrineLoader();
        $em = $doc->getEntityManager();
        $this->primaryKey = $em->getClassMetadata($className)->getSingleIdentifierFieldName();
        
        $this->reflectionClass = new \ReflectionClass($className);        
        $this->fields = $this->reflectionClass->getProperties();        
        
        $this->generatePrototype();

        $output->writeln('The FormPrototype has been successfully generated.');
    }


    private function generatePrototype(){
        $prototypePath = __DIR__.'/../../../../app/'.$this->fullModule.'/'.CrossRoadsRooter::FORM_DIRECTORY.'/'.$this->entity.CrossRoadsRooter::FORM.'.php';
        fopen($prototypePath, 'a');
        $content = $this->buildPrototypeContent();
        file_put_contents($prototypePath, $content);
    }
    
    private function buildPrototypeContent(){
        $content = '<?php
namespace app\\'.$this->fullModule.'\\'.CrossRoadsRooter::FORM_DIRECTORY.';


use framework\core\Forms\FormBuilder;
use framework\core\Forms\FormBuilderInterface;

class '.$this->entity.CrossRoadsRooter::FORM.' implements FormBuilderInterface
{

    public function buildFormPrototype(FormBuilder $builder)
    {
        $builder'.$this->buildFormBuilder().' 
        ;
        
        return $builder;
    }
}
        ';
        
        return $content;
    }
    
    private function buildFormBuilder(){
        $reader = new AnnotationReader();
        $builder = '';
        foreach ($this->fields as $field){
            if($field->name != $this->primaryKey){
                $f = $this->reflectionClass->getProperty($field->name);
                $annotations = $reader->getPropertyAnnotations($f);
                
                if(isset($annotations[0])){
                    $object = $annotations[0];
                    $type= '';
                    if($object instanceof Column){
                        $type = $object->type;
                    }
                    $builder .= "
            ->addInput('".$field->name."', '".$this->typeFieldsMapping[$type]."')";
                }
                
            }
        }
        return $builder;
    }
}