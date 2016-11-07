<?php
namespace framework\core\Twig\LimpidExtensions\FormToTwigBridge;


use framework\core\Forms\Form;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class LaunchFormBridge
 * Twig extension to render the form's open tag
 * @package framework\core\Twig\LightExtensions\FormToTwigBridge
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class LaunchFormBridge implements TwigCustomExtension
{
    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'form_begin',
            array($this, 'launchForm')
        );
    }

    public function launchForm(Form $form, $action = '', $method = 'POST', $attributes = array()){
        echo $form->getFormHeaderHtml($action, $method, $attributes);
    }
}