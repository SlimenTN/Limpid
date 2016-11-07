<?php

namespace framework\core\Twig\LimpidExtensions;


use framework\core\Controller\CrossRoadsRooter;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class TranslatorExtension
 * @package framework\core\Twig\LimpidExtensions
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class TranslatorExtension implements TwigCustomExtension
{

    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'translate',
            array($this, 'translateWord')
        );
    }

    public function translateWord($module, $word){
        $lang = CrossRoadsRooter::$LANG;
        if($lang == '') throw new \Exception('You have used the "translate" extension without specifying the lang in your routes!');
        $translationBook = CrossRoadsRooter::getTranslationBook($module);
        $translation = $this->findWord($translationBook, $word, $lang);
        echo $translation;
    }

    /**
     * @param $translationBook
     * @param $w
     * @param $lang
     * @return null
     */
    private function findWord($translationBook, $w, $lang){
        foreach ($translationBook as $word => $array){
            if($word == $w)
                return $array[$lang];
        }
        return $w;
    }
}