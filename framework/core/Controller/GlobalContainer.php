<?php
namespace framework\core\Controller;

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use framework\core\Forms\Form;
use framework\core\Forms\FormBuilder;
use framework\core\Forms\FormBuilderInterface;
use framework\core\PHPMailer\MailerLauncher;
use framework\core\Repository\DoctrineLoader;
use framework\core\Request\HTTPHandler;
use framework\core\Request\SessionHandler;
use framework\core\Twig\TwigLauncher;

/**
 * Class GlobalContainer
 * @package framework\core\Controller
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class GlobalContainer
{
    /**
     * Launch twig functions
     * @var TwigLauncher
     */
    public $twigLauncher;

    /**
     * Container to inject dependencies
     * @var \DI\Container
     */
    public $container;

    function __construct()
    {
        SessionHandler::initSession();
        $this->container = ContainerBuilder::buildDevContainer();
        $this->twigLauncher = $this->container->get('framework\core\Twig\TwigLauncher');//----Inject TwigLauncher dependency

    }

    /**
     * @return SessionHandler
     * @throws \DI\NotFoundException
     */
    public function session(){
        return $this->container->get('framework\core\Request\SessionHandler');
    }

    /**
     * @return HTTPHandler
     * @throws \DI\NotFoundException
     */
    public function getHttpHandler(){
        return $this->container->get('framework\core\Request\HTTPHandler');
    }

    /**
     * Call service from settings.php file
     * @param $service
     * @return object called service class
     * @throws \DI\NotFoundException
     */
    public function call($service){
        $settings = include __DIR__ . '/../../config/settings.php';
        $serviceClass = $settings['services'][$service];
        return $this->container->get($serviceClass['class']);
    }

    /**
     * Inject repository of given entity
     * @param string $entityName String contains module's name and entity ex: 'HelloWorld:Entity'
     * @return EntityRepository
     */
    public function getRepository($entityName){

        list($module, $entity) = explode(':', $entityName);
        return $this->getEntityManager()->getRepository(CrossRoadsRooter::generateRepositoryNamespace($module, $entity));
    }

    /**
     * Get entity's namespace
     * @param $entityDestination
     * @return string
     */
    public function getEntityNamespace($entityDestination){
        list($module, $entity) = explode(':', $entityDestination);
        return CrossRoadsRooter::generateEntityNamespace($module, $entity);
    }

    /**
     * Inject Doctrine's EntityManager
     * @return EntityManager|null
     */
    public function getEntityManager(){
        return $this->getDoctrine()->getEntityManager();
    }

    /**
     * Inject Doctrine loader
     * @return DoctrineLoader
     * @throws \DI\NotFoundException
     */
    public function getDoctrine(){
        return $this->container->get('framework\core\Repository\DoctrineLoader');
    }

    /**
     * @return FormBuilder
     * @throws \DI\NotFoundException
     */
    public function getFormBuilder(){
        return new FormBuilder($this);
    }

    /**
     * @param $customFormClass
     * @param $object
     * @return \framework\core\Forms\Form
     */
    public function buildForm($destination, $object){

        if(null === $object){
            throw new \Exception(__METHOD__.'() needs object to build form , null given!');
        }

        $prototypeNamespace = $this->getFormPrototypeNamespcae($destination);
        $builder = $this->getFormBuilder();
        $builder = $this->getFormPrototype($prototypeNamespace)->buildFormPrototype($builder);
        $form = new Form($object, $this, $builder->getInputs());
        return $form;
    }

    /**
     * Generate form prototype namespace
     * @param $destination
     * @return string
     */
    public function getFormPrototypeNamespcae($destination){
        list($module, $entity) = explode(':', $destination);
        return CrossRoadsRooter::generateFormPrototypeNamespace($module, $entity);
    }

    /**
     * @param $class
     * @return FormBuilderInterface
     * @throws \DI\NotFoundException
     */
    public function getFormPrototype($class){
        $prototype = $this->container->get($class);
        if(!$prototype instanceof FormBuilderInterface){
            throw new \Exception('The prototype form must implement FormBuilderInterface.');
        }
        return $prototype;
    }

    /**
     * @return MailerLauncher
     * @throws \DI\NotFoundException
     */
    public function getMailer(){
        return $this->container->get('framework\core\PHPMailer\MailerLauncher');
    }

    /**
     * get assets directory
     * @return string
     */
    public function getAssetsDirectory(){
        return __DIR__.'/../../../assets/';
    }

    /**
     * Redirect to url by given route name
     * @param $routeName
     * @throws \Exception
     */
    public function redirectToRoute($routeName, $params = array()){
        CrossRoadsRooter::redirectToRoute($routeName, $params);
    }
    
}