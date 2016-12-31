<?php
namespace framework\core\Twig\LimpidExtensions;

use framework\core\Controller\CrossRoadsRooter;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class SwitchTranslationExtension
 * Switch lang with the same url
 * @package framework\core\Twig\LimpidExtensions
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class SwitchTranslationExtension implements TwigCustomExtension
{
    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'switch_lang',
            array($this, 'switchTranslation')
        );
    }
    
    public function switchTranslation($lang){

        if(!CrossRoadsRooter::$SETTINGS['translator']['enabled']){
            throw new \Exception('Translation is disabled in AppParmaeters Class !');
        }

        $serverDir = (dirname($_SERVER['PHP_SELF']) == '/') ? '' : dirname($_SERVER['PHP_SELF']);
        $request =  str_replace($serverDir, '', str_replace('%20', ' ', $_SERVER['REQUEST_URI']));
        $tab = explode('/', $request);
        $tab[1] = $lang;
        $newURL = implode('/', $tab);

        $exclude = '/app_launcher.php';
        $hote = str_replace($exclude, '', $_SERVER['PHP_SELF']);

        return $hote.$newURL;
    }
}