<?php
namespace framework\core\Twig\LimpidExtensions\FormToTwigBridge;


use framework\core\Forms\Form;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class CloseFormBridge
 * Twig extension to render the form's close tag
 * @package framework\core\Twig\LightExtensions\FormToTwigBridge
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class CloseFormBridge implements TwigCustomExtension
{
    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'form_close',
            array($this, 'closeForm')
        );
    }

    public function closeForm(Form $form){
        echo $form->getFormFooterHtml();
    }

}