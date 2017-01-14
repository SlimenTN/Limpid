<?php
/**
 * Created by PhpStorm.
 * User: Brux
 * Date: 14/01/2017
 * Time: 16:07
 */

namespace framework\core\Twig\LimpidExtensions;


use framework\core\Request\SessionHandler;
use framework\core\Twig\TwigCustomExtension;

class SessionExtension implements TwigCustomExtension
{

    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'session',
            array($this, 'session')
        );
    }
    
    public function session($index){
        SessionHandler::initSession();
        $session = new SessionHandler();
        return $session->get($index);
    }
}