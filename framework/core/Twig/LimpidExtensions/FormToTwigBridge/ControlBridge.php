<?php

namespace framework\core\Twig\LimpidExtensions\FormToTwigBridge;


use framework\core\Forms\Form;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class ControlBridge
 * Twig extension to render a specific control in a form
 * @package framework\core\Twig\LightExtensions\FormToTwigBridge
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class ControlBridge implements TwigCustomExtension
{

    public function getExtension(){

        return new \Twig_SimpleFunction(
            'form_control',
            array($this, 'renderControl')
        );
    }

    /**
     * Display form's input
     * @param mixed $form
     * @param $input
     * @return mixed
     */
    public function renderControl($form, $input, $attributes =  array()){
//        var_dump($form[$input]);
        $field = null;
        if($form instanceof Form){
            $field = $form->getFieldbyName($input);
            if(count($attributes) > 0){
                foreach ($attributes as $key => $value){
                    $field->setAttribute($key, $value);
                }
            }
        }else if(is_array($form)){
            $field = $form[$input];
        }
        
        echo $field->getHtml();
    }
    
}