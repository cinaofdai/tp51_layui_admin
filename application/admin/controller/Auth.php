<?php
/**
 * Created by dailinlin.
 * Date: 2017/10/10 15:06
 * for: auth 权限管理
 */

namespace app\admin\controller;


use app\common\controller\BaseAdmin;

use think\auth\AuthManager;
use think\Db;
use think\exception\HttpException;

class Auth extends BaseAdmin
{

    /**
     * 权限列表
     */
    public function index(){
        if (request()->isAjax()){
            return $this->search('rule_menu_view');
        }else{
            return $this->fetch();
        }
    }


    /**
     * 删除权限节点
     */
    public function remove(){
        $ids = request()->post('ids');
        if(request()->isAjax()){
            if(!is_array($ids)){
                $data[] = $ids;
            }else{
                $data = $ids;
            }
            $result = Db::name('auth_rule')->delete($data);
            if($result){
                return ['status'=>true,'message'=>'删除成功!'];
            }else{
                return ['status'=>false,'message'=>'删除失败!'];
            }
        }else{
            new HttpException(502,'请求不合法');
        }
    }


    /**
     * 添加权限节点
     * @return array|mixed
     */
    public function add_access(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $manager = AuthManager::getInstance();
                if($manager->insertRule($data)){
                    return ['status'=>true,'message'=>'权限添加成功'];
                }else{
                    return ['status'=>false,'message'=>$manager->getError()];
                }
            }else{

                //输出返回
                $menu = Db::name('menu')->order('id','asc')->select();
                $menu =  \Data::tree($menu,'title');
                foreach ($menu as $value){
                    $lists[] = $value;
                }
                return $lists;
            }
        }else{
            return $this->fetch();
        }
    }

    /**
     * 编辑权限节点
     * @return array|bool|false|mixed|\PDOStatement|string|\think\Model
     */
    public function edit_access(){
        if (request()->isAjax()){
            if(request()->isGet()){
                $data['model'] = Db::name('auth_rule')->find(request()->get('id'));

                //菜单模块
                $menu = Db::name('menu')->select();
                $menu =  \Data::tree($menu,'title');
                foreach ($menu as $value){
                    $lists[] = $value;
                }
                $data['menus']  =  $lists;
                return $data?$data:false;
            }else{
                $data = request()->post();
                $manager = AuthManager::getInstance();
                if($manager->updateRule($data)){
                    return ['status'=>true,'message'=>'权限编辑成功'];
                }else{
                    return ['status'=>false,'message'=>$manager->getError()];
                }
            }
        }else{
            $this->assign('edit_id',request()->get('id'));
            return $this->fetch();
        }
    }


    /**
     * 角色列表
     * @return array|mixed
     */
    public function group_list(){
        if (request()->isAjax()){
            $table = $this->search('auth_group');
            foreach ($table['lists'] as &$value){
               $admin =  Db::name('admin_group_view')->where('group_id',$value['id'])->column('username');
               $value['admin'] = '';
               if(isset($admin)&&count($admin)>0){
                   $value['admin'] = implode('|',$admin);
               }
            }
            return $table;
        }else{
            return $this->fetch();
        }
    }

    /**
     * 添加角色
     * @return array|false|mixed|\PDOStatement|string|\think\Collection
     */
    public function add_group(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $manager = AuthManager::getInstance();
                if($manager->insertGroup($data)){
                    return ['status'=>true,'message'=>'角色添加成功'];
                }else{
                    return ['status'=>false,'message'=>$manager->getError()];
                }
            }else{
                return AuthManager::getInstance()->getAllRule();
            }
        }else{
            return $this->fetch();
        }
    }


    /**
     * 编辑角色
     * @return array|mixed
     */
    public function edit_group(){

        if (request()->isAjax()){
            if(request()->isGet()){
                $group = Db::name('auth_group')->find(request()->get('id'));
                $group['rules'] = explode(',',$group['rules']);
                return [
                    'models' => $group,
                    'rules'=>AuthManager::getInstance()->getAllRule($group['rules'])
                ];
            }else{
                $data = request()->post();

                //去除空字符
                foreach ($data['rules'] as $key=> $value){
                    if ($value==''){
                        unset($data['rules'][$key]);
                    }
                }

                $manager = AuthManager::getInstance();
                if($manager->updateGroup($data)){
                    return ['status'=>true,'message'=>'角色编辑成功'];
                }else{
                    return ['status'=>false,'message'=>$manager->getError()];
                }
            }
        }else{
            $this->assign('edit_id',request()->get('id'));
            return $this->fetch();
        }
    }

    /**
     * 删除用户角色
     * @return array
     */
    public function remove_group(){
        $ids = request()->post('ids');
        if(request()->isAjax()){
            if(!is_array($ids)){
                $data[] = $ids;
            }else{
                $data = $ids;
            }


            $exist = Db::name('auth_group_access')->where('group_id','in',$data)->find();
            if($exist){
                return ['status'=>false,'message'=>'此角色下面存在用户,不能删除!'];
            }

            $result = Db::name('auth_group')->delete($data);
            if($result){
                return ['status'=>true,'message'=>'删除成功!'];
            }else{
                return ['status'=>false,'message'=>'删除失败!'];
            }
        }else{
            new HttpException(502,'请求不合法');
        }
    }

}