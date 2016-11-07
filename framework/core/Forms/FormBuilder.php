<?php
/**
 * Created by PhpStorm.
 * User: Slimen-PC
 * Date: 25/10/2016
 * Time: 15:32
 */

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
    private $container;

    /**
     * @var Form
     */
    private $form;

    /**
     * @var object
     */
    private $object;
    
    function __construct()
    {
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
    public function addInput($name, $type, $label = null, $options = array()){
        $this->inputs[] = new FormInput($name, $type, $label, $options);
        return $this;
    }

    /**Build form
     * @param $object
     * @return Form
     */
//    public function buildForm($object){
//        $this->object = $object;
//        $this->form = new Form($object, $this->container);
//        $this->form->decomposeAndBuildForm($this->inputs);
//        return $this->form;
//    }

    /**
     * @return array
     */
    public function getInputs()
    {
        return $this->inputs;
    }
}