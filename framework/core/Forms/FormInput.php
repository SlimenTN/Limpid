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
    private $name;
    private $type;
    private $label;
    private $options;

    private $notAttributes = array(
        'ajax',
        'target_entity',
        'static',
    );

    function __construct($name, $type, $label = null, $options = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->options = $options;
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


}