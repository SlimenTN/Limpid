<?php

namespace framework\core\Router;

use framework\config\AppParamters;
use framework\core\Controller\CrossRoadsRooter;

/**
 * Class URLParser
 * Where we parse given request and return null or command to execute
 * @package framework\core\Router
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class URLParser{

    const MATCH = 'MATCH';
    const DOES_NOT_MATCH = 'DOES_NOT_MATCH';

    /**
     * @param string $request
     * @param ListRoutes $routes
     * @return array|null
     */
    static public function parse($request, ListRoutes $routes){
        if($request == '/' && CrossRoadsRooter::$SETTINGS['translator']['enabled']){
            $url = CrossRoadsRooter::getHote().'/'.CrossRoadsRooter::$LANG.$request;
            header('Location: '.$url);
            exit;
        }

        $arrayRequest = explode('/', $request);

        $counter = 1;
        foreach ($routes->getRoutes() as $route){
            
            $parseReport = self::compare($arrayRequest, explode('/', $route->getPattern()));

            if($parseReport['match_result'] === self::MATCH){
                return array(
                    'route_name' => $route->getName(),
                    'command' => $route->getCommandPath(),
                    'parameters' => $parseReport['params'],
                );
            }
            $counter++;
        }
        return null;
    }

    /**
     * @param array $arrayRequest
     * @param array $arrayPattern
     * @return array
     */
    static private function compare(array $arrayRequest, array $arrayPattern){

        if (CrossRoadsRooter::$SETTINGS['translator']['enabled'] && count($arrayRequest) > 0){
            CrossRoadsRooter::$LANG = $arrayRequest[1];
            array_splice($arrayRequest, 1, 1);//---if translator is enabled remove the element of the request that contains lang
        }


        $countRoute = count($arrayRequest);
        $arrayParameters = array();//----array where we will store given parameters if exist
        $match_res = self::DOES_NOT_MATCH;
        
        //-----if arrayRoute and arrayPattern has the same number of segments start parsing
        if($countRoute == count($arrayPattern)){
            $match_res = self::MATCH;
            for ($x = 0; $x < $countRoute; $x++){
                $segmentRequest =  $arrayRequest[$x];
                $segmentPattern = $arrayPattern[$x];

                if(strpos($segmentPattern, '%') !== false){ // it's a parameter (dynamic side of the url)

                    $arrayParameters[] = $segmentRequest;//---store parameter

                }else{// not a parameter (static side of the url)
                    if($segmentRequest !== $segmentPattern){
                        $match_res = self::DOES_NOT_MATCH;
                        break;
                    }
                }
            }
        }
        
        return array(
            'params' => $arrayParameters,
            'match_result' => $match_res,
        );
    }
}