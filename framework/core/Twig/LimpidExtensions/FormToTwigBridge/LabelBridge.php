<?php
namespace framework\core\Twig\LimpidExtensions\FormToTwigBridge;

use framework\core\Forms\Form;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class LabelBridge
 * @package framework\core\Twig\LimpidExtensions\FormToTwigBridge
 * 
 * @Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class LabelBridge implements TwigCustomExtension
{

    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'form_label',
            array($this, 'renderLabel')
        );
    }

    public function renderLabel($form, $input, $attributes =  array()){
        $field = null;
        if($form instanceof Form){
            $field = $form->getFieldbyName($input);
        }else if(is_array($form)){
            $field = $form[$input];
        }

        if(!empty($attributes)){
            foreach ($attributes as $key => $value){
                $field->setAttribute($key, $value);
            }
        }

        echo $field->getFieldLabel();
    }
}