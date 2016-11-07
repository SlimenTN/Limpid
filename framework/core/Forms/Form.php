<?php
namespace framework\core\Forms;

use Doctrine\Common\Collections\ArrayCollection;
use framework\core\Controller\GlobalContainer;
use framework\core\Forms\FormElements\Label;
use framework\core\Request\ParametersHandler;
use Gregwar\Formidable\Fields\DateField;
use Gregwar\Formidable\Fields\Field;
use Gregwar\Formidable\Fields\FileField;
use Gregwar\Formidable\Fields\HiddenField;
use Gregwar\Formidable\Fields\Option;
use Gregwar\Formidable\Fields\Select;
use Gregwar\Formidable\Fields\Textarea;
use Gregwar\Formidable\Fields\TextField;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class Form
 * @package framework\core\Forms
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class Form
{
    private static $DEFINED_TYPES = array(
        'string',
        'int',
        'integer',
        'bool',
        'boolean',
        'FileField',
        'ArrayCollection',
        'Collection'
    );

    /**
     * @var array
     */
    private $formWidgets;

    /**
     * @var array
     */
    private $formLabels;

    /**
     * @var string
     */
    private $formHtml;

    /**
     * @var object
     */
    private $object;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var GlobalContainer
     */
    private $container;

    /**
     * @var array
     */
    private $inputs;

    /**
     * Form constructor.
     * @param $object
     * @param GlobalContainer $container
     * @param array $inputs
     */
    public function __construct($object, GlobalContainer $container, array $inputs)
    {
        $this->formWidgets = array();
        $this->inputs = $inputs;
        $this->object = $object;
        $this->container = $container;
        $this->accessor = PropertyAccess::createPropertyAccessor();

        $this->decomposeAndBuildForm();

    }

    /**
     * @param array $inputs
     */
    public function decomposeAndBuildForm()
    {
        foreach ($this->inputs as $input) {
//            $this->formLabels[$input->getName()] = new ;
            $this->formWidgets[$input->getName()] = $this->buildInputElemenet($input);
        }

//        var_dump($this->formWidgets['fiches']);

    }

    /**
     * @param FormInput $input
     * @return Field
     */
    private function buildInputElemenet(FormInput $input, $subObject = null, $subFieldName = null)
    {
        $o = ($subObject == null) ? $this->object : $subObject;
        $n = ($subFieldName == null) ? $input->getName() : $subFieldName;

        $field = null;

        switch ($input->getType()) {
            case 'text':
                $field = new TextField();
                $field->setName($n);
                $field->setValue($this->accessor->getValue($o, $input->getName()));

                break;
            case 'textarea':
                $field = new Textarea();
                $field->setName($n);
                $field->setValue($this->accessor->getValue($o, $input->getName()));

                break;
            case 'select':
                $field = new Select();
                $field->setName($n);
                $this->buildSelectOptions($field, $input, $o);
                break;
            case 'file':
                $field = new FileField();
                $field->setName($n);
                break;
            case 'date':
                $field = new DateField();
                $field->setName($n);
                $field->setValue($this->accessor->getValue($o, $input->getName()));
                break;
            case 'collection':
                $entities = $this->accessor->getValue($o, $input->getName());

                $prototypeNamespace = $this->container->getFormPrototypeNamespcae($input->getOptions()['target_entity']);
                $form = $this->container->getFormPrototype($prototypeNamespace);
                $field = $this->buildCollection($entities, $form, $input->getName());

                break;
        }

        return $field;
    }

    /**
     * @param $entities
     * @param FormBuilderInterface $form
     * @param $parentName
     * @return string
     */
    private function buildCollection($entities,FormBuilderInterface $form, $parentName){
        $sw = array();
        $counter = 0;
        $subInputs = $form->buildFormPrototype(new FormBuilder())->getInputs();
        foreach ($entities as $entity) {
            $subWidgets = array();
//            var_dump($entity);
            $hiddenId = new HiddenField();
            $hiddenId->setName($parentName.'['.$counter.'][id]');
            $hiddenId->setValue($this->accessor->getValue($entity, 'id'));

            $subWidgets['id'] = $hiddenId;

            foreach ($subInputs as $si) {
                $nameSubField = $si->getName();
                $name = $parentName . '[' . $counter . '][' . $nameSubField . ']';
                $subWidgets[$nameSubField] = $this->buildInputElemenet($si, $entity, $name);

            }
            $sw[] = $subWidgets;
            $counter++;
        }
        return $sw;
    }

    /**
     * @param Select $field
     * @param FormInput $input
     * @param object $object
     */
    private function buildSelectOptions(Select $field, FormInput $input, $object)
    {
        if (array_key_exists('static', $input->getOptions())) {
            foreach ($input->getOptions()['static'] as $option) {
                $op = new Option();
                $op->setValue($option['value']);
                $op->setLabel($option['label']);
                if ($this->accessor->getValue($object, $input->getName()) == $option['value'])
                    $op->push('selected');
                $field->addOption($op);
            }
        } else if (array_key_exists('target_entity', $input->getOptions())) {
            $entity = $this->container->getEntityNamespace($input->getOptions()['target_entity']);
            $list = $this->container->getEntityManager()->getRepository($entity)->findAll();
            foreach ($list as $item) {
                $op = new Option();
                $op->setValue($item->getId());
                $op->setLabel($item->__toString());
                if ($this->accessor->getValue($object, $input->getName()) != null && $this->accessor->getValue($object, $input->getName() . '.id') == $item->getId())
                    $op->push('selected');
                $field->addOption($op);
            }
        }
    }



    /**
     * @param GlobalContainer $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Return the html format of the form
     * @return string
     */
    public function getFormHtml()
    {
        return $this->generateHtmlFromWidgets($this->formWidgets);
    }

    /**
     * Recursicve function to generate html view from widgets
     * @param array $widgets
     * @return string
     */
    private function generateHtmlFromWidgets($widgets){
        $html = '<table>';
        foreach ($widgets as $key => $widget){
            if(!is_array($widget)){
                $html .= $this->generateTableRow($widget);
            }else{
                $html .= '<tr><td><label>'.$this->guessLabel($key).'</label></td>';
                $html .= '<td>'.$this->generateHtmlFromWidgets($widget).'</td></tr>';
            }
        }
        $html .= '</table>';
        return $html;
    }

    /**
     * @param Field $field
     * @return string
     */
    private function generateTableRow(Field $field){
        $tr = '';
        if(!$field instanceof HiddenField){
            $tr = '<tr>';
            $tr .= '<td><label>'.$this->guessLabel($field->getName()).'</label></td>';
            $tr .= '<td>'.$field->getHtml().'</td>';
            $tr .= '</tr>';
        }else{
            $tr .= $field->getHtml();
        }

        return $tr;
    }

    /**
     * @param string $action
     * @param string $method
     * @param array $attributes
     * @return string
     */
    public function getFormHeaderHtml($action = '', $method = 'POST', $attributes = array()){
        $header = '<form ';
        $header .= 'action="'.$action.'" ';
        $header .= 'method="'.$method.'" ';
        foreach ($attributes as $key => $value){
            $header .= $key.'="'.$value.'" ';
        }
        $header .= 'enctype="multipart/form-data">';
        return $header;
    }

    public function getFormFooterHtml(){
        return '</form>';
    }

    /**
     * @return bool
     */
    public function isPosted()
    {

       $res = ParametersHandler::handle();

        if(!empty($res)){
//            var_dump($res);
            $this->handleRequest($res);
            return true;
        }else{
            return false;
        }
    }

    /**
     * Refresh form view with new data
     */
    public function refreshView(){
        $this->formWidgets = array();
        $this->container->getEntityManager()->refresh($this->object);
        $this->decomposeAndBuildForm();
    }

    /**
     * Handle the form's request and fill given object
     */
    private function handleRequest(array $data){

        foreach ($data as $key => $value) {
            
            if ($this->isCustomObject($key)) {//----if it's custom object create instance of it
                $entity = $this->container->getEntityManager()->getRepository($this->getTargetEntity($key))->find($value);
                $this->accessor->setValue($this->object, $key, $entity);

            } else if ($this->typeOf($key) == 'ArrayCollection') {//---if it's doctrine's ArrayCollection type
                $collection = $this->accessor->getValue($this->object, $key);

                $objectsToRemove = $this->collectRemovedObject($collection, $value);

                $counter = 0;
                foreach ($value as $k => $v) {
                    $c = null;
                    if(array_key_exists('id', $v) && $v['id'] != ''){
                        foreach ($collection as $en){
                            if($v['id'] == $en->getId()){
                                $c = $en;
                            }
                        }
                    }else{
                        $class = $this->getTargetEntity($key);
                        $c = new $class();
                        $collection->set($counter, $c);//---update or add it
                    }
                    foreach ($v as $sk => $sv) {//---fill entity
//                        var_dump('$sk: '.$sk);
                        $this->accessor->setValue($c, $sk, $sv);
                    }

                    $counter++;
                }

                foreach ($objectsToRemove as $or){
                    $collection->removeElement($or);
                }

            } else {///----else if it's a simple type just push it to the object
                $this->accessor->setValue($this->object, $key, $value);
            }

        }
    }

    /**
     * @param $collection
     * @param $value
     * @return array
     */
    private function collectRemovedObject($collection, $value) {
        $objectsToRemove = array();
        foreach ($collection as $en){
            $id = $en->getId();
            $exist = false;
            foreach ($value as $k => $v){
                if(array_key_exists('id', $v)){
                    if($v['id'] == $id){
                        $exist = true;
                    }
                }
            }
            if(!$exist){
                $objectsToRemove[] = $en;
            }
        }
        return $objectsToRemove;
    }

    /**
     * @param $attr
     * @return null
     */
    private function getTargetEntity($attr)
    {
        $entity = null;
        foreach ($this->inputs as $input) {
            if ($input->getName() == $attr) {
                $entityDestination = $input->getOptions()['target_entity'];
                $entity = $this->container->getEntityNamespace($entityDestination);
            }
        }
        if ($entity == null) {
            throw new \Exception('The parameter "target_entity" is not defined for the attribute ' . $attr);
        }
        return $entity;
    }

    /**
     * Get type of attribut
     * @param $attr
     * @return null
     */
    private function typeOf($attr)
    {
        $reflect = new \ReflectionClass($this->object);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($props as $prop) {
            preg_match_all('#@(.*?)\n#s', $prop->getDocComment(), $annotations);
//            $type = str_replace('@var ', '', trim($annotations[0][0]));
            list($anno, $type) = explode(' ', trim($annotations[0][0]));
            if ($prop->getName() === $attr) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Check if it's a custom object
     * @param $attr
     * @param $class
     * @return bool
     */
    private function isCustomObject($attr)
    {
        if (in_array($this->typeOf($attr), self::$DEFINED_TYPES)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $name
     * @return string
     */
    public function guessLabel($name)
    {
        return ucfirst($name);
    }

    /**
     * @return array
     */
    public function getFormWidgets()
    {
        return $this->formWidgets;
    }

    /**
     * Get field object by name
     * @param string $name
     * @return Field
     * @throws \Exception
     */
    public function getFieldbyName($name){
        foreach ($this->formWidgets as $field){
            if($field instanceof Field && $field->getName() == $name)
                return $field;
        }

        throw new \Exception('Unknown field "'.$name.'"');
    }
}