<?php
/**
 * Created by PhpStorm.
 * User: Slimen-PC
 * Date: 02/11/2016
 * Time: 15:55
 */

namespace framework\core\Forms\FormElements;


use framework\core\Forms\FormInput;

class Label
{
    /**
     * @var FormInput
     */
    private $input;

    /**
     * @var string
     */
    private $childName;

    
    function __construct(FormInput $input)
    {
        $this->input = $input;
    }

    /**
     * @return FormInput
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param FormInput $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * @return string
     */
    public function getChildName()
    {
        return $this->childName;
    }

    /**
     * @param string $childName
     */
    public function setChildName($childName)
    {
        $this->childName = $childName;
    }
    
    

    function __toString()
    {
        return $this->getHtml();
    }


    /**
     * @return string
     */
    public function getHtml(){
        $for = ($this->childName === null) ? $this->input->getName() : $this->childName;
        
        $html = '<label ';
        $html .= 'for="'.$for.'">';
        $html .= $this->guessLabel();
        $html .= '</label>';
        return $html;
    }

    /**
     * Guess the label's nma in case if it was not given
     * @param FormInput $input
     * @return mixed
     */
    private function guessLabel()
    {
        if (null === $this->input->getLabel()) {
            return ucfirst($this->input->getName());
        }
        return $this->input->getLabel();
    }
}