<?php
/**
 * Created by dailinlin.
 * Date: 2017/9/23 14:40
 * for: 后台公共 控制器
 */

namespace app\common\controller;


use think\Controller;
use think\Db;
use think\exception\ErrorException;
use think\facade\Config;
use think\Hook;
use think\Model;

class BaseAdmin extends Controller
{

    /*public function _initialize(){
        //判断用户是否登录--行为钩子
        Hook::listen('admin_login',$params);
    }*/

    /**
     * 自动获取搜索条件 组装
     * @param $table
     * @return $this|\think\db\Query
     */
    protected function where($table){
        $search = request()->except([Config::get('paginate.var_page'),'_'],'get');

        $model = Db::name($table);
        if(is_array($search)){
            foreach($search as $key=>$value){
                if($value!=''){
                    if(is_numeric($value)){
                        if($value>-1){
                            $model = $model->where($key,$value);
                        }
                    }else{
                        $time = explode(' - ',$value);
                        if(is_array($time)&&count($time)==2){ //如果是时间
                            $model = $model->whereBetween($key,[strtotime($time[0]),strtotime($time[1])]);
                        }else{
                            $model = $model->where($key,'like','%'.$value.'%');
                        }
                    }
                }
            }
        }
        return $model;
    }

    /**
     * 万能搜索
     * @param $table 表名
     * @param bool $page 是否分页 默认分页
     * @param array $cond 附加条件  $map['key'] = ['op','value']
     * @param string $desc
     * @param string $order
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    protected function search($table,$page=true,$cond=[],$desc='desc',$order='id'){

        $search = request()->except([Config::get('paginate.var_page'),'_'],'get');
        if($table instanceof Model){
            if(method_exists($table,'search')){
                return $table->search($search);
            }else{
                new ErrorException(1,'模型不存在search方法',dirname(__FILE__),dirname(__LINE__ ));
            }
        }

        $model = Db::name($table);

        if(is_array($search)){
            foreach($search as $key=>$value){
                if($value!=''){
                    if(is_numeric($value)){
                        if($value>-1){
                            $model = $model->where($key,$value);
                        }
                    }else{
                        $time = explode(' - ',$value);
                        if(is_array($time)&&count($time)==2){ //如果是时间
                            $model = $model->whereBetween($key,[strtotime($time[0]),strtotime($time[1])]);
                        }else{
                            $model = $model->where($key,'like','%'.$value.'%');
                        }
                    }
                }
            }
        }

        //分页处理
        if ($page){
            $count = $model->where($cond)->count();
            $Page = new \VuePage($count);
            $lists = $model->where($cond)->limit($Page->getStart(),$Page->getEnd())->order($order,$desc)->select();
            $Page->setData($lists);
            return $Page->show();
        }else{

            return [
                'lists' =>$model->where($cond)->order($order,$desc)->select(),
                'count' => $model->where($cond)->count(),
                'total' => 1,
                'current' =>1,
                'size' => Config::get('paginate.list_rows')
            ];

        }

    }

}