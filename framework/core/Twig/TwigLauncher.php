<?php
namespace framework\core\Twig;


use framework\config\TwigExtensionsBook;

/**
 * Class TwigLauncher
 * Fire twig functions
 * @package framework\core\Twig
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class TwigLauncher{

    /**
     * @var \Twig_Loader_Filesystem
     */
    private $loader;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(){
        $this->loader = new \Twig_Loader_Filesystem();
        $this->twig = new \Twig_Environment($this->loader);
        $this->enableAppTwigExtensions();
    }

    /**
     * Add new template path
     * @param $template
     * @throws \Twig_Error_Loader
     */
    public function addTemplate($template){
        $this->loader->addPath($template);
    }

    /**
     * Paint twig template
     * @param $view
     * @param $parameters
     * @return string
     */
    public function paintView($view, $parameters){
        return $this->twig->render($view, $parameters);
    }

    /**
     * Add additional twig extension
     * @param \Twig_SimpleFunction $callable
     */
    public function addExtension(\Twig_SimpleFunction $callable){
        $this->twig->addFunction($callable);
    }

    /**
     * Enable all twig extensions declared in the app
     */
    private function enableAppTwigExtensions(){
        $arrayExtensionsClass = TwigExtensionsBook::$EXTENSIONS;
        foreach ($arrayExtensionsClass as $class){
            $instance = new $class();
            $this->addExtension($instance->getExtension());
        }
    }
}