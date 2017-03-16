<?php
namespace framework\core\Forms;

use framework\core\Controller\GlobalContainer;


/**
 * Class FormBuilder
 * @package framework\core\Forms
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class FormBuilder
{
    /**
     * @var array
     */
    private $inputs;

    /**
     * @var GlobalContainer
     */
    private $controller;

    function __construct(GlobalContainer $controller)
    {
        $this->controller = $controller;
        $this->inputs = array();
    }

    /**
     * Add new Input to form
     * @param $name
     * @param $type
     * @param $label
     * @param array $options
     * @return $this
     */
    public function addInput($name, $type, $label = null, $options = array(), $transformerClass = null){
        $transformer = ($transformerClass != null) ? new $transformerClass() : null;
        $input = new FormInput($name, $type, $label, $options, $transformer);
        if($type == 'collection'){
            $input->setSubInputs($this->buildSubInputs($input));
        }
        $this->inputs[] = $input;
        return $this;
    }

    /**
     * Remove input from FormBuilder
     * @param $name
     */
    public function removeInput($name){
        foreach ($this->inputs as $key => $input){
            if($input instanceof FormInput){
                if($input->getName() == $name) unset($this->inputs[$key]);
            }
        }
    }

    /**
     * @param FormInput $input
     * @return array
     * @throws \Exception
     */
    private function buildSubInputs(FormInput $input){
        $target_entity = $input->getOptions()['target_entity'];
        $prototypeNamespace = $this->controller->getFormPrototypeNamespcae($target_entity);
        $form = $this->controller->getFormPrototype($prototypeNamespace);
        $subInputs = $form->buildFormPrototype(new FormBuilder($this->controller))->getInputs();
        return $subInputs;
    }

    /**
     * @return array
     */
    public function getInputs()
    {
        return $this->inputs;
    }
}