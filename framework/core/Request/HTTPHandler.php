<?php
namespace framework\core\Request;
use framework\core\Forms\FormElements\FileField;

/**
 * Class HTTPHandler
 * Handle sent post or get data
 * @package framework\core\Request
 * 
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class HTTPHandler
{
    const POST = 'POST';
    const GET = 'GET';
    
    /**
     * @var array
     */
    private $get;

    /**
     * @var array
     */
    private $post;

    function __construct()
    {
        $this->builGetArray();
        $this->buildPostArray();
    }

    /**
     * 
     */
    private function builGetArray(){
        $this->get = ($_SERVER['REQUEST_METHOD'] == self::GET) ? $_GET : null;
    }

    /**
     * 
     */
    private function buildPostArray(){
        if ($_SERVER['REQUEST_METHOD'] == self::POST) {
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

            $this->post = self::array_merge_recursive_ex($_POST, $files);
        }else{
            $this->post = null;
        }
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

    /**
     * @param $request
     * @return array
     * @throws \Exception
     */
    public function get($request){
        switch ($request){
            case self::GET:
                return $this->get;
                break;
            case self::POST:
                return $this->post;
                break;
            default:
                throw new \Exception('Unknown request "'.$request.'"! Available requests are "'.self::GET.'" or "'.self::POST.'".');
                break;
        }    
    }
}