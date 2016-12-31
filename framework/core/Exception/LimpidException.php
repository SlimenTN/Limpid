<?php
namespace framework\core\Exception;


use framework\core\Controller\CrossRoadsRooter;
use framework\core\Twig\TwigLauncher;

class LimpidException extends \Exception
{
    /**
     * Exception message
     * @var string
     */
    protected $message;

    protected $status;
    
    function __construct($message, $code)
    {
        $this->status = $code;
        parent::__construct($message, $this->status, null);
        $this->handle();
    }

    private function handle()
    {
        if (!CrossRoadsRooter::$prod) {
            throw new \Exception($this->message);
        } else {
            if (isset(CrossRoadsRooter::$SETTINGS['errors'][$this->status])) {
                $page = CrossRoadsRooter::$SETTINGS['errors'][$this->status];
                if($page == ''){
                    $this->defaultErrorPage();
                }else{
                    $this->customPage($page);
                }
            } else {
                $this->defaultErrorPage();
            }
        }

    }

    /**
     * Display custom page
     * @param $pagePath
     */
    private function customPage($pagePath){
        header($_SERVER["SERVER_PROTOCOL"] . " ".$this->status." error", true, $this->status);
        $twig = new TwigLauncher();
        list($module, $view) = explode(":", $pagePath);
        $twig->addTemplate(CrossRoadsRooter::generateModuleViewTemplates($module));
        echo $twig->paintView($view);
        exit();
    }

    /**
     * Display default error page
     */
    protected function defaultErrorPage(){}
}