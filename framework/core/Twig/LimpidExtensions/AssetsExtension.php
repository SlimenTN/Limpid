<?php
namespace framework\core\Twig\LimpidExtensions;


use framework\core\Twig\TwigCustomExtension;

/**
 * Class AssetsExtension
 * Twig extension to get real path of assets
 * @package framework\core\Twig\LightExtensions
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class AssetsExtension implements TwigCustomExtension
{

    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'assets',
            array($this, 'getAssetsPath')
        );
    }

    public function getAssetsPath($path){
        $exclude = '/app_launcher.php';
        $hote = str_replace($exclude, '', $_SERVER['PHP_SELF']);
        return $hote.'/assets/'.$path;
    }
}