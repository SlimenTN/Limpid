<?php
namespace framework\core\Forms;

/**
 * Class FormInput
 * @package framework\core\Forms
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class FormInput
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $label;
    /**
     * @var array
     */
    private $options;
    /**
     * @var DataTransformer
     */
    private $transformer;

    /**
     * @var array
     */
    private $subInputs = array();

    private $notAttributes = array(
        'ajax',
        'target_entity',
        'static',
    );

    function __construct($name, $type, $label = null, $options = array(), DataTransformer $transformer = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->options = $options;
        $this->transformer = $transformer;
    }

    /**
     * Check if the option is an input attribute
     * @param $value
     * @return bool
     */
    public function isInputAttribute($option){
        return (!in_array($option, $this->notAttributes));
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return DataTransformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @param DataTransformer $transformer
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @return array
     */
    public function getSubInputs()
    {
        return $this->subInputs;
    }

    /**
     * @param array $subInputs
     */
    public function setSubInputs($subInputs)
    {
        $this->subInputs = $subInputs;
        return $this;
    }


}