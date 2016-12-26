<?php
namespace framework\core\Twig;

use framework\core\Controller\CrossRoadsRooter;

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
    public function addTemplate($template, $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE){
        $this->loader->addPath($template, $namespace);
    }

    /**
     * Paint twig template
     * @param $view
     * @param $parameters
     * @return string
     */
    public function paintView($view, $parameters = array()){
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
        $this->addLimpidExtensions();
        $this->addExternalExtensions();
    }

    public function addLimpidExtensions(){
        $arrayExtensionsClass = LimpidExtensionsBook::$EXTENSIONS;
        foreach ($arrayExtensionsClass as $class){
            $instance = new $class();
            if($instance instanceof TwigCustomExtension){
                $this->addExtension($instance->getExtension());
            }else{
                throw new \Exception('Your Twig extension "'.$class.'" must implements TwigCustomExtension');
            }
        }
    }

    private function addExternalExtensions(){
        $modules = $this->getAvailableModules();
        foreach ($modules as $module){
            $twigDirectory = __DIR__ . '/../../../app/' . $module . '/' . CrossRoadsRooter::TWIG;
            if(is_dir($twigDirectory)){
                $extensionsFiles = scandir(__DIR__ . '/../../../app/' . $module . '/' . CrossRoadsRooter::TWIG);
                foreach ($extensionsFiles as $file) {
                    if ($file != '.' && $file != '..') {
                        $className = str_replace('.php', '', $file);
                        $namespace = "app\\".$module."\\".CrossRoadsRooter::TWIG."\\".$className;
                        $instance = new $namespace();
                        if($instance instanceof TwigCustomExtension){
                            $this->addExtension($instance->getExtension());
                        }else{
                            throw new \Exception('Your Twig extension "'.$className.'" under "'.$module.'" must implements TwigCustomExtension');
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getAvailableModules()
    {
        $directories = scandir(__DIR__ . '/../../../app');
        $modules = array();

        foreach ($directories as $dir) {
            if (strpos($dir, CrossRoadsRooter::MODULE) !== false) {
                $modules[] = $dir;
            }
        }

        return $modules;
    }
}