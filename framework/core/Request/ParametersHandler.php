<?php
/**
 * Created by PhpStorm.
 * User: Slimen-PC
 * Date: 02/11/2016
 * Time: 13:45
 */

namespace framework\core\Request;


use Gregwar\Formidable\Fields\FileField;

/**
 * Class ParametersHandler
 * @package framework\core\Request
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class ParametersHandler
{
    /**
     * Handel send parameters $_POST or $_GET
     * @return array
     * @throws \Exception
     */
    public static function handle(){
        $res = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $files = array();
            foreach ($_FILES as $index => $file) {
                $f = null;
                if (!is_array($file['name'])) {//---file not inside array
                    $f = new FileField();
                    $f->setValue($file);
                    $files[$index] = $f;
                } else {//-----array files
                    $d = array();
                    foreach ($file['name'] as $k => $array) {
                        $sd = array();
                        foreach ($array as $sk => $sv){
                            $datas = array();
                            $f = new FileField();
                            $datas['name'] = $file['name'][$k][$sk];
                            $datas['type'] = $file['type'][$k][$sk];
                            $datas['tmp_name'] = $file['tmp_name'][$k][$sk];
                            $datas['error'] = $file['error'][$k][$sk];
                            $datas['size'] = $file['size'][$k][$sk];
                            $f->setValue($datas);
                            $sd[$sk] = $f;
                        }
                        $d[$k] = $sd;
                    }
                    $files[$index] = $d;
                }
            }
            
            $res = self::array_merge_recursive_ex($_POST, $files);

        }else if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $res = $_GET;
        }else{
            throw new \Exception('No parameters to handle.');
        }

        return $res;
    }

    /**
     * Merge arrays and sub arrays by keys
     * @param array $array1
     * @param array $array2
     * @return array
     *
     * <http://stackoverflow.com/questions/25712099/php-multidimensional-array-merge-recursive>
     */
    private static function array_merge_recursive_ex(array & $array1, array & $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => & $value)
        {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key]))
            {
                $merged[$key] = self::array_merge_recursive_ex($merged[$key], $value);
            } else if (is_numeric($key))
            {
                if (!in_array($value, $merged))
                    $merged[] = $value;
            } else
                $merged[$key] = $value;
        }

        return $merged;
    }
}