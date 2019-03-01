<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2018/2/11 0011 19:12
 * For:
 */

namespace app\common\model;


use think\Model;
use think\Validate;

class BaseModel extends Model
{
    protected $validate;


    /**初始化验证
     * @param $rule
     * @return Validate
     */
    public function initValidate($rule){
        return $this->validate = new Validate($rule);
    }


    /**
     * 设置验证场景
     * @param $scene
     * @return $this
     */
    public function scene($scene){
        $this->validate->scene($scene);
        return $this;
    }

    /**
     * 验证数据
     * @param $data
     * @return bool
     */
    public function check($data){
        $result = $this->validate->check($data);
        if($result){
             return true;
        }else{
            $this->error = $this->validate->getError();
            return false;
        }
    }


    /**
     * 数据增改
     * @param $data
     * @param string $scene
     * @return mixed
     */
    protected function store($data,$scene='insert'){
        $this->field = $this->field?:$data;
        //场景验证
        $result = $this->scene($scene)->check($data);
        if($result){
            return $this->$scene($this->field);
        }else{
            return false;
        }
    }
}