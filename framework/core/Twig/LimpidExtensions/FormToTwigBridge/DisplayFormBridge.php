<?php

namespace framework\core\Twig\LimpidExtensions\FormToTwigBridge;


use framework\core\Forms\Form;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class DisplayFormBridge
 * Render the default view of a given form
 * @package framework\core\Twig\LimpidExtensions\FormToTwigBridge
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class DisplayFormBridge implements TwigCustomExtension
{

    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'render_form',
            array($this, 'renderDefaultForm')
        );
    }
    
    public function renderDefaultForm(Form $form){
        $formHTML = $form->getFormHeaderHtml();
        $formHTML .= $form->getFormHtml();
        $formHTML .= '<input type="submit" value="send" />';
        $formHTML .= $form->getFormFooterHtml();
        echo $formHTML;
    }
}