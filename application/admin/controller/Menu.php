<?php
/**
 * Created by dailinlin.
 * Date: 2017/10/29 16:15
 * for: 菜单管理
 */

namespace app\admin\controller;


use app\common\controller\BaseAdmin;
use think\auth\AuthManager;
use think\Db;

class Menu extends BaseAdmin
{


    /**
     * 菜单列表
     * @return mixed
     */
    public function index(){
        if (request()->isAjax()){
            $data = $this->search('menu',false,$cond=[],$desc='asc');   //获取查询的数据
            $menu =  \Data::tree( $data['lists'],'title');                  //进行菜单结构重组
            $lists = [];
            foreach ($menu as $value){
                $lists[] = $value;
            }
            $data['lists'] = $lists;
            return $data;
        }else{
            return $this->fetch();
        }
    }

    /**
     * 添加菜单
     * @return mixed
     */
    public function add(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $manager = AuthManager::getInstance();
                if($manager->insertMenu($data)){
                    return ['status'=>true,'message'=>'菜单插入成功'];
                }else{
                    return ['status'=>false,'message'=>$manager->getError()];
                }
            }else{
                return Db::name('menu')->where('pid','eq',0)->select();
            }
        }else{
            return $this->fetch();
        }
    }


    public function edit(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $manager = AuthManager::getInstance();
                if($manager->updateMenu($data)){
                    return ['status'=>true,'message'=>'菜单编辑成功'];
                }else{
                    return ['status'=>false,'message'=>$manager->getError()];
                }
            }else{
                return [
                    'menus'=>Db::name('menu')->where('pid','eq',0)->select(),
                    'model'=>Db::name('menu')->find(request()->get('id'))
                ];
            }
        }else{
            $this->assign('edit_id',request()->get('id'));
            return $this->fetch();
        }
    }

    /**
     * 删除菜单
     * @return array
     */
    public function remove(){
        $ids = request()->post('ids');
        if(request()->isAjax()){
            if(!is_array($ids)){
                $data[] = $ids;
            }else{
                $data = $ids;
            }

            $child = Db::name('menu')->where('pid','in',$data)->find();
            if($child){
                return ['status'=>false,'message'=>'存在子菜单!'];
            }

            $result = Db::name('menu')->delete($data);
            if($result){
                return ['status'=>true,'message'=>'删除成功!'];
            }else{
                return ['status'=>false,'message'=>'删除失败!'];
            }
        }else{
            new \HttpException(502,'请求不合法');
        }
    }
}