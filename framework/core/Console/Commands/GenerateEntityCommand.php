<?php
namespace framework\core\Console\Commands;


use framework\core\Controller\CrossRoadsRooter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class GenerateEntityCommand
 * Create new entity
 * @package framework\core\Console\Commands
 *
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class GenerateEntityCommand extends Command
{
    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $fullModule;

    /**
     * @var array
     */
    private $attributes;
    
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('create:entity')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates new entity.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to create entity. The name must be composed of module's name (WITHOUT 'MODULE' WORD) and the 
            entity's name, example -> HelloLimpid:MyEntity")

            ->addArgument('entity_name', InputArgument::REQUIRED, 'Entity\'s name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        
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
        
        //--Check if the entity already exist
        if(file_exists(__DIR__.'/../../../../app/'.$this->fullModule.'/Entity/'.$this->entity.'.php')){
            $output->writeln('You have already created an entity with the name "'.$this->entity.'" in the '.$this->fullModule.' directory!');
            return;
        }

        $output->writeln('Now we need to know the attributes of the entity (example => attribute:type:length)');
        $output->writeln('You don\'t need to specify the length if it does not exist.');

        $counter = 1;
        $attrNameQuestion = new Question('attribute (keep it empty to stop execution): ');
        $attributeInfos = $helper->ask($input, $output, $attrNameQuestion);
        
        if($attributeInfos == '' && $counter == 1){
            $output->writeln('You did not specify any attribute to this entity ! Command has been canceled.');
            return;
        }

        $this->attributes[] = explode(':', $attributeInfos);
        $counter ++;

        while($attributeInfos != null){
            $attrNameQuestion = new Question('attribute (keep it empty to stop execution): ');
            $attributeInfos = $helper->ask($input, $output, $attrNameQuestion);
            if($attributeInfos != null) $this->attributes[] = explode(':', $attributeInfos);
            $counter ++;
        }
        
        $output->writeln('Generation of the entity and repository is in progress ...');
        $this->confirmGeneration();
        $this->generateRepository();
        $output->writeln('Entity and it\' repository has been successfully generated. You can find it under "app/'.$this->fullModule.'/Entity".');
    }
    
    private function confirmGeneration(){
        $entityPath = __DIR__.'/../../../../app/'.$this->fullModule.'/'.CrossRoadsRooter::ENTITY.'/'.$this->entity.'.php';
        fopen($entityPath, 'a');
        $content = $this->buildEntityContent();
        file_put_contents($entityPath, $content);
    }

    private function buildEntityContent(){
        $content = '<?php
namespace app\\'.$this->fullModule.'\\'.CrossRoadsRooter::ENTITY.';

use Doctrine\ORM\Mapping as ORM;

/**
 * Class '.$this->entity.'
 *
 * @ORM\Entity(repositoryClass="app\\'.$this->fullModule.'\\'.CrossRoadsRooter::REPOSITORY.'\\'.$this->entity.CrossRoadsRooter::REPOSITORY.'")
 * @ORM\Table(name="'.lcfirst($this->entity).'")
 *
 */
class '.$this->entity.'
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    '.$this->buildAttributes().'
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    '.$this->buildGettersAndSetters().'
}
        ';
        return $content;
    }

    private function buildAttributes(){
        $__attrString = '';
        foreach ($this->attributes as $value){
            $phpDeclaration = $this->buildPHPDeclaration($value);
            $doctrineDeclaration = $this->buildDoctrineDeclaration($value);
            $__attrString .= '
    /**
     * '.$phpDeclaration.'
     *
     * '.$doctrineDeclaration.'
     */
    private $'.$value[0].';
            ';
        }
        return $__attrString;
    }

    /**
     * @param array $value
     * @return string
     */
    private function buildPHPDeclaration(array $value){
        $__declaration = '@var ';
        $__declaration .= $this->recognizePHPType($value);

        return $__declaration;
    }

    /**
     * @param array $value
     * @return string
     */
    private function recognizePHPType($value){
        $__phpType = null;
        switch ($value[1]){
            case 'text':
                $__phpType = 'string';
                break;
            case 'date':
                $__phpType = '\DateTime';
                break;
            default:
                $__phpType = $value[1];
                break;
        }
        return $__phpType;
    }

    /**
     * @param array $value
     * @return string
     */
    private function buildDoctrineDeclaration(array $value){
        $__declaration = '@ORM\Column(name="'.$value[0].'", ';
        $__declaration .= 'type="'.$value[1].'"';
        if(isset($value[2])){
            $__declaration .= ', length='.$value[2].')';
        }else{
            $__declaration .= ')';
        }
        return $__declaration;
    }

    private function buildGettersAndSetters(){
        $__set_get = '';
        foreach ($this->attributes as $value){
            $uppercase = ucfirst($value[0]);
            $type = $this->recognizePHPType($value);
            $__set_get .= '
    /**
     * @return '.$type.'
     */
    public function get'.$uppercase.'()
    {
        return $this->'.$value[0].';
    }

    /**
     * @param '.$type.' $'.$value[0].'
     * @return '.$this->entity.'
     */
    public function set'.$uppercase.'($'.$value[0].')
    {
        $this->'.$value[0].' = $'.$value[0].';
        return $this;
    }
            ';
        }
        return $__set_get;
    }

    private function generateRepository(){
        $repoPath = __DIR__.'/../../../../app/'.$this->fullModule.'/'.CrossRoadsRooter::REPOSITORY.'/'.$this->entity.CrossRoadsRooter::REPOSITORY.'.php';
        fopen($repoPath, 'a');
        $content = $this->buildRepositoryContent();
        file_put_contents($repoPath, $content);
    }

    private function buildRepositoryContent(){
        $repoName = $this->entity.CrossRoadsRooter::REPOSITORY;
        $__repo = '<?php
namespace app\\'.$this->fullModule.'\\'.CrossRoadsRooter::REPOSITORY.';

use Doctrine\ORM\EntityRepository;

/**
 * Class '.$repoName.'
 * @package app\\'.$this->fullModule.'\\'.CrossRoadsRooter::REPOSITORY.'
 */
class '.$repoName.' extends EntityRepository
{

}';

        return $__repo;
    }
}