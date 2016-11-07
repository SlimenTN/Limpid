<?php
/**
 * Created by PhpStorm.
 * User: Slimen-PC
 * Date: 25/10/2016
 * Time: 15:35
 */

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
    
    function __construct($name, $type, $label = null, $options = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->options = $options;
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