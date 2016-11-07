<?php
namespace framework\config;

/**
 * Class TwigExtensionsBook
 * All twig extensions goes here
 * @package framework\config
 */
class TwigExtensionsBook{

    public static $EXTENSIONS = array(
        /**
         * Limpid's extensions-------------------
         */
        'framework\core\Twig\LimpidExtensions\RouteConverterExtension',
        'framework\core\Twig\LimpidExtensions\AssetsExtension',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\ControlBridge',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\LaunchFormBridge',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\CloseFormBridge',
        'framework\core\Twig\LimpidExtensions\FormToTwigBridge\DisplayFormBridge',
        'framework\core\Twig\LimpidExtensions\TranslatorExtension',
        'framework\core\Twig\LimpidExtensions\SwitchTranslationExtension',
        'framework\core\Twig\LimpidExtensions\CurrentRouteExtension',

        /**
         * You can add your extensions here--------------
         * 
         * ex: 'app\HelloWorldModule\Twig\MyNewExtension',
         */
        
    );
    
}