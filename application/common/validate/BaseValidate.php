<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2019/3/1 10:47
 * For:
 */

namespace app\common\validate;


use think\Validate;

class BaseValidate extends Validate
{
    public function except($string,array $data){

        $arr = explode(",", $string);
        foreach ($data as $key=>$value){
            if (in_array($key, $arr)){
                unset($data[$key]);
            }
        }
        return $data;
    }
}