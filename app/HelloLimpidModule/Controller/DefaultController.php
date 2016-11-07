<?php

namespace app\HelloLimpidModule\Controller;

use framework\core\Controller\AppController;

class DefaultController extends AppController
{
    /**
     * Index command
     */ 
    public function helloCommand(){
        $this->paintView('HelloLimpid:app.html.twig');
    }
}