<?php

namespace framework\core\Twig\LimpidExtensions;

use framework\core\Controller\CrossRoadsRooter;
use framework\core\Router\RoutesCollector;
use framework\core\Twig\TwigCustomExtension;

/**
 * Class RouteConverterExtension
 * Twig extension to convert given route name to url
 * @package framework\core\Twig\LightExtensions
 *
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class RouteConverterExtension implements TwigCustomExtension
{
    public function getExtension()
    {
        return new \Twig_SimpleFunction(
            'route',
            array($this, 'buildURL')
        );
    }

    public function buildURL($routeName, $params = array())
    {
        $routes = new RoutesCollector();
        $url = null;
        foreach ($routes->getRoutes()->getRoutes() as $route) {
            if ($route->getName() === $routeName) {
                $url = $route->getPattern();
                $tab = explode('/', $url);
                $i = 0;
                foreach ($tab as $segment) {
                    if (strpos($segment, '%') !== false) {
                        $tab[$i] = $params[str_replace('%', '', $segment)];
                    }
                    $i++;
                }
                $url = implode('/', $tab);
            }
        }
        if ($url == null) throw new \Exception('Exception in ' . __METHOD__ . '(): We can\'t find a declared route with the name "' . $routeName . '" ');

        if (CrossRoadsRooter::$SETTINGS['translator']['enabled']) $url = '/' . CrossRoadsRooter::$LANG . $url;

        $exclude = '/app_launcher.php';
        $hote = str_replace($exclude, '', $_SERVER['PHP_SELF']);
        return $hote . $url;
    }

}