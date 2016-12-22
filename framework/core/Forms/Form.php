<?php
namespace framework\core\Forms;

use Doctrine\Common\Collections\Collection;
use framework\core\Controller\GlobalContainer;
use framework\core\Forms\FormElements\CheckboxField;
use framework\core\Forms\FormElements\DateField;
use framework\core\Forms\FormElements\Field;
use framework\core\Forms\FormElements\FileField;
use framework\core\Forms\FormElements\HiddenField;
use framework\core\Forms\FormElements\MulticheckboxField;
use framework\core\Forms\FormElements\MultiradioField;
use framework\core\Forms\FormElements\NumberField;
use framework\core\Forms\FormElements\Option;
use framework\core\Forms\FormElements\RadioField;
use framework\core\Forms\FormElements\Select;
use framework\core\Forms\FormElements\Textarea;
use framework\core\Forms\FormElements\TextField;
use framework\core\Request\HTTPHandler;
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
        'date',
        '\DateTime',
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
        $fieldLabel = ($input->getLabel() == null) ? ucfirst($n) : $input->getLabel();

        switch ($input->getType()) {
            case 'text':
                $field = new TextField();
                $field->setFieldLabel($fieldLabel);
                $field->setName($n);
                $field->setValue($this->accessor->getValue($o, $input->getName()));
                foreach ($input->getOptions() as $option => $value){
                    if($input->isInputAttribute($option))
                        $field->push($option, $value);
                }
                break;
            case 'textarea':
                $field = new Textarea();
                $field->setName($n);
                $field->setFieldLabel($fieldLabel);
                $field->setValue($this->accessor->getValue($o, $input->getName()));
                foreach ($input->getOptions() as $option => $value){
                    if($input->isInputAttribute($option))
                        $field->push($option, $value);
                }
                break;
            case 'select':
                $field = new Select();
                $field->setName($n);
                $field->setFieldLabel($fieldLabel);
                foreach ($input->getOptions() as $option => $value){
                    if($input->isInputAttribute($option))
                        $field->push($option, $value);
                }
                $this->buildSelectOptions($field, $input, $o);
                break;
            case 'file':
                $field = new FileField();
                $field->setName($n);
                $field->setFieldLabel($fieldLabel);
                foreach ($input->getOptions() as $option => $value){
                    if($input->isInputAttribute($option))
                        $field->push($option, $value);
                }
                break;
            case 'date':
                $field = new DateField();
                $field->setName($n);
                $field->setFieldLabel($fieldLabel);
                $field->setValue($this->accessor->getValue($o, $input->getName()));
                foreach ($input->getOptions() as $option => $value){
                    if($input->isInputAttribute($option))
                        $field->push($option, $value);
                }
                break;
            case 'numeric':
                $field = new NumberField();
                $field->setName($n);
                $field->setFieldLabel($fieldLabel);
                $field->setValue($this->accessor->getValue($o, $input->getName()));
                foreach ($input->getOptions() as $option => $value){
                    if($input->isInputAttribute($option))
                        $field->push($option, $value);
                }
                break;
            case 'checkbox':
                $field = new CheckboxField();
                $field->setFieldLabel($fieldLabel);
                $field->setName($n);
                $field->setValue($this->accessor->getValue($o, $input->getName()));
                foreach ($input->getOptions() as $option => $value){
                    if($input->isInputAttribute($option))
                        $field->push($option, $value);
                }
                break;
            case 'multi-checkbox':
                $field = new MulticheckboxField();
                $field->setName($n);
                $data = array('10' => 'Piscine', '20' => 'Garage', '25' => 'Jardin');
                $field->source($data);
                $v = array('20' => 'Garage', '25' => 'Jardin');
                $field->setValue($v);

                break;
            case 'radio':
                $field = new RadioField();
                $field->setName($n);
                $field->setFieldLabel($fieldLabel);
                $field->setValue($this->accessor->getValue($o, $input->getName()));
                foreach ($input->getOptions() as $option => $value){
                    if($input->isInputAttribute($option))
                        $field->push($option, $value);
                }
                break;
            case 'multi-radio':
                $field = new MultiradioField();
                $field->setName($n);
                $field->setFieldLabel($fieldLabel);
                $data = array(false => 'Non', true => 'Oui');
                $field->source($data);
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
            if(array_key_exists('ajax', $input->getOptions()) && $input->getOptions()['ajax']){
                $objectCollection = $this->accessor->getValue($object, $input->getName());
                foreach ($objectCollection as $entity){
                    $op = new Option();
                    $op->setValue($entity->getId());
                    $op->setLabel($entity->__toString());
                    $op->push('selected');
                    $field->addOption($op);
                }
            }else{
                $entity = $this->container->getEntityNamespace($input->getOptions()['target_entity']);
                $listFull = $this->container->getEntityManager()->getRepository($entity)->findAll();
                $objectCollection = $this->accessor->getValue($object, $input->getName());
                foreach ($listFull as $item) {
                    $op = new Option();
                    $op->setValue($item->getId());
                    $op->setLabel($item->__toString());
                    if($this->typeOf($input->getName()) == 'ArrayCollection'){
                        if($this->idExistInEntities($item->getId(), $objectCollection))
                            $op->push('selected');
                    }else{
                        if ($this->accessor->getValue($object, $input->getName()) != null && $this->accessor->getValue($object, $input->getName() . '.id') == $item->getId())
                            $op->push('selected');
                    }

                    $field->addOption($op);
                }
            }

        }
    }

    /**
     * @param $id
     * @param Collection $collection
     * @return bool
     */
    private function idExistInEntities($id, Collection $collection){
        foreach ($collection as $entity){
            if($entity->getId() == $id) return true;
        }
        return false;
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
//        var_dump($this->formWidgets);
        return $this->generateHtmlFromWidgets($this->formWidgets);
    }

    /**
     * Recursicve function to generate html view from widgets
     * @param array $widgets
     * @return string
     */
    private function generateHtmlFromWidgets($widgets){
        $html = '<table>';
        foreach ($widgets as $name => $object){
            if(is_array($object)){
                $__parentLabel = $this->guessLabel($name);
                $html .= '<tr>';
                $html .= '<td>'.$__parentLabel.'</td>';
                $html .= '<td>';
                $html .= '<table id="'.$name.'">';
                foreach ($object as $array){
                    foreach ($array as $fn => $field){
                        $html .= $this->generateTableRow($field);
                    }
                }
                $html .= '</table>';
                $html .= '</td>';
                $html .= '</tr>';
            }else{
                $html .= $this->generateTableRow($object);
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
            $tr .= '<td><label>'.$field->getFieldLabel().'</label></td>';
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

        $res = $this->container->getHttpHandler()->get(HTTPHandler::POST);

        if(!empty($res)){
            $this->handleRequest($res);
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get related object filled with data
     * @return object
     */
    public function getObject(){
        return $this->object;
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
        $this->handlerUnsentAttributes($data);
        foreach ($data as $key => $value) {
            if ($this->isCustomObject($key)) {//----if it's custom object create instance of it
                $entity = $this->container->getEntityManager()->getRepository($this->getTargetEntity($key))->find($value);
                $this->accessor->setValue($this->object, $key, $entity);

            } else if ($this->typeOf($key) == 'ArrayCollection') {//---if it's doctrine's ArrayCollection type
                $collection = $this->accessor->getValue($this->object, $key);
                $inputForm = $this->inputFormType($key);
                if($inputForm->getType() == 'collection'){
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
                            $this->accessor->setValue($c, $sk, $sv);
                        }

                        $counter++;
                    }

                    foreach ($objectsToRemove as $or){
                        $collection->removeElement($or);
                        $this->container->getEntityManager()->remove($or);
                    }

                }else{

                    $entitiesToRemove = $this->entitiesToRemove($collection, $value);
                    $counter = 0;
                    foreach ($value as $id){
                        $entity = $this->getEntityById($collection, $id);
                        if($entity == null){
                            $namespace = $this->container->getEntityNamespace($inputForm->getOptions()['target_entity']);
                            $entity = $this->container->getEntityManager()->getRepository($namespace)->find($id);
                            $collection->set($counter, $entity);
                        }
                        $counter++;
                    }

                    foreach ($entitiesToRemove as $e){
                        $collection->removeElement($e);
                    }
                }

            }else if($this->typeOf($key) == '\DateTime'){
                $date = new \DateTime($value['year'].'-'.$value['month'].'-'.$value['day']);
                $this->accessor->setValue($this->object, $key, $date);
            }else {///----else if it's a simple type just push it to the object
                $this->accessor->setValue($this->object, $key, $value);
            }

        }
    }

    /**
     * Handle unsent attributes
     * @param array $httpData
     */
    private function handlerUnsentAttributes(array $httpData){
        $unsentAttributes = $this->getUnsentAttributes($httpData);
        foreach ($unsentAttributes as $attribute){
            $type = $this->typeOf($attribute);
            if($type == 'bool' || $type == 'boolean'){
                $this->accessor->setValue($this->object, $attribute, false);
            }else if($type == 'ArrayCollection'){
                $collection = $this->accessor->getValue($this->object, $attribute);
                $collection->clear();
            }
        }
    }

    /**
     * Get unsent attributes (declared in the form builder but not sent)
     * @param array $httpData
     * @return array
     */
    private function getUnsentAttributes(array $httpData){
        $unsentAttributes = array();
        foreach ($this->inputs as $input){
            $attr = $input->getName();
            if(!array_key_exists($attr, $httpData)) $unsentAttributes[] = $attr;
        }
        return $unsentAttributes;
    }

    /**
     * @param Collection $collection
     * @param int $id
     */
    private function getEntityById(Collection $collection, $id){
        foreach ($collection as $entity){
            if($entity->getId() == $id) return $entity;
        }
        return null;
    }

    /**
     * Collect entities to remove
     * @param Collection $collection
     * @param array $ids
     * @return array
     */
    private function entitiesToRemove(Collection $collection, array $ids){
        $entitiesToRemove = array();
        foreach ($collection as $item){
            if(!in_array($item->getId(), $ids)) $entitiesToRemove[] = $item;
        }
        return $entitiesToRemove;
    }

    /**
     * Get form builder type for given attribute
     * @param $attr
     * @return FormInput|null
     */
    private function inputFormType($attr){
//        var_dump($this->inputs);
        foreach ($this->inputs as $input){
            if($input->getName() == $attr) return $input;
        }
        return null;
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
//        var_dump($attr);
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
        $label = $this->inputFormType($name)->getLabel();
        return ($label == null) ? ucfirst($name) : $label;
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