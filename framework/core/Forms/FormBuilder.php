<?php
namespace framework\core\Forms;


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
    public function addInput($name, $type, $label = null, $options = array(), $transformerClass = null){
        $transformer = ($transformerClass != null) ? new $transformerClass() : null;
        $this->inputs[] = new FormInput($name, $type, $label, $options, $transformer);
        return $this;
    }

    /**
     * @return array
     */
    public function getInputs()
    {
        return $this->inputs;
    }
}