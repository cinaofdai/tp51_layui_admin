<?php
/**
 * Created by dailinlin.
 * Date: 2017/9/26 9:56
 * for: 管理员管理
 */

namespace app\admin\controller;



use app\common\controller\BaseAdmin;
use think\Config;
use think\Db;
use think\exception\HttpException;

class Admin extends BaseAdmin
{

    /**
     * 管理员列表
     * @return mixed
     */
    public function index(){
        if (request()->isAjax()){
            $db = $this->where('admin');

            $count = $db->count();
            $Page = new \VuePage($count);
            $lists = $db->limit($Page->getStart(),$Page->getEnd())->order('id','desc')->select();
            $model = new \app\admin\model\Admin();
            foreach ($lists as &$item){
                $item['role'] = $model->getRoleName($item['id']);
            }
            $Page->setData($lists);
            return $Page->show();
        }else{
            return $this->fetch();
        }
    }


    /**
     *添加管理员
     */
    public function add(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $model = new \app\admin\model\Admin();

                if($model->insertAdmin($data)){
                    return ['status'=>true,'message'=>'管理员添加成功'];
                }else{
                    return ['status'=>false,'message'=>$model->getError()];
                }

            }else{
                $data =  Db::name('auth_group')->field('id,title')->where('status',1)->select();
                return $data;
            }
        }else{
            return $this->fetch();
        }
    }

    /**
     * 修改管理员
     */
    public function edit(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $model = new \app\admin\model\Admin();

                if($model->updateAdmin($data)){
                    return ['status'=>true,'message'=>'管理员编辑成功'];
                }else{
                    return ['status'=>false,'message'=>$model->getError()];
                }
            }else{
                $data['roles'] =  Db::name('auth_group')->field('id,title')->where('status',1)->select();
                $data['model'] =  Db::name('admin')->find(request()->get('id'));
                $data['model']['role'] = Db::name('auth_group_access')->where('uid',$data['model']['id'])->value('group_id');
                $data['model']['role'] = $data['model']['role']?:0;
                return $data;
            }
        }else{
            $this->assign('edit_id',request()->get('id'));
            return $this->fetch();
        }
    }

    /**
     *删除管理员
     */
    public function remove(){
        $ids = request()->post('ids');
        if(request()->isAjax()){
            if(!is_array($ids)){
                $data[] = $ids;
            }else{
                $data = $ids;
            }
            $result = Db::name('admin')->delete($data);
            $group = Db::name('auth_group_access')->where('uid','in',$data)->delete();
            if($result&&$group){
                return ['status'=>true,'message'=>'删除成功!'];
            }else{
                return ['status'=>false,'message'=>'删除失败!'];
            }
        }else{
            new HttpException(502,'请求不合法');
        }
    }


    /**
     * 更改管理员状态
     * @return array
     */
    public function status(){
        if(request()->isAjax()){
            $data = request()->post();
            $message = $data['status']==1?'启用':'停用';
            $result = Db::name('admin')->update($data);
            if($result){
                return ['status'=>true,'message'=>$message.'成功!'];
            }else{
                return ['status'=>false,'message'=>$message.'失败!'];
            }
        }else{
            new HttpException(502,'请求不合法');
        }
    }
}