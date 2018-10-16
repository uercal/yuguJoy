<?php
namespace app\store\controller;

use think\Request;
use function Qiniu\json_decode;


/**
 * 后台首页
 * Class Index
 * @package app\store\controller
 */
class Index extends Controller
{
    public function index()
    {
        return $this->fetch('index');
    }

    public function demolist()
    {
        return $this->fetch('demo-list');
    }

    public function preView(Request $request){
        $path = input('path');      
        
        header("Content-Type:text/html;charset=utf-8");
        $url = "http://s1-cdn.eqxiu.com/eqs/page/133124355?code=1aWRhOKB&time=1538113034000";
        $data =  file_get_contents("compress.zlib://".$url);
        // $data = json_decode($data,true)        ;
        echo $data;
    }

    function treatJsonString($string)
{
    $jsonData = json_decode($string, true);

    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return $jsonData;
            break;
        case JSON_ERROR_DEPTH:
            print '[Error] - Maximum stack depth exceeded' . PHP_EOL;
            break;
        case JSON_ERROR_STATE_MISMATCH:
            print '[Error] - Underflow or the modes mismatch' . PHP_EOL;
            break;
        case JSON_ERROR_CTRL_CHAR:
            print '[Error] - Unexpected control character found' . PHP_EOL;
            break;
        case JSON_ERROR_SYNTAX:
            print '[Error] - Syntax error, malformed JSON' . PHP_EOL;
            break;
        case JSON_ERROR_UTF8:
            print '[Error] - Malformed UTF-8 characters, possibly incorrectly encoded' . PHP_EOL;
            break;
        default:
            print '[Error] - Unknown error' . PHP_EOL;
            break;
    }
    return null;
}
}
