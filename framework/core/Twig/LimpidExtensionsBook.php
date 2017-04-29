<?php
namespace framework\core\Twig;

/**
 * Class LimpidExtensionsBook
 * @package framework\core\Twig
 */
class LimpidExtensionsBook
{
    public static $EXTENSIONS = array(
        'framework\core\Twig\LimpidExtensions\RouteConverterExtension',
        'framework\core\Twig\LimpidExtensions\AssetsExtension',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\ControlBridge',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\LabelBridge',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\LaunchFormBridge',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\CloseFormBridge',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\DisplayFormBridge',
        'framework\core\Twig\LimpidExtensions\TranslatorExtension',
        'framework\core\Twig\LimpidExtensions\SwitchTranslationExtension',
        'framework\core\Twig\LimpidExtensions\CurrentRouteExtension',
        'framework\core\Twig\LimpidExtensions\ExecuteCommandExtension',
        'framework\core\Twig\LimpidExtensions\SessionExtension',
    );
}