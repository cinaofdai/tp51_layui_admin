<?php
/**
 * Created by dailinlin.
 * Date: 2017/10/10 11:41
 * for:
 */

namespace app\admin\model;


use think\Db;
use think\helper\Str;
use think\Model;

class Admin extends Model
{
    protected $pk = 'id';


    //时间自动写入
    protected $autoWriteTimestamp = true;


    /**
     * 获取角色名称
     * @param $uid
     * @return mixed|string
     */
    public function getRoleName($uid){
        $role =  Db::view('auth_group_access','uid,group_id')
            ->view('auth_group','title','auth_group_access.group_id=auth_group.id','left')
            ->where('uid',$uid)
            ->find();
        return $role?$role['title']:'';
    }

    /**
     * 添加管理员
     * @param $data
     * @return bool|mixed
     */
    public function insertAdmin($data){
        $auth = false;
        $data['inpassword'] = $data['password'];

        $data['token'] = Str::random(10);
        $data['password'] = md5($data['password'].$data['token']);

        $validate = new \app\common\validate\Admin();
        $result = $validate->scene('insert')->check($data);
        if (!$result) {
            $this->error = $validate->getError();
            return false;
        }

        $result = $this->insertGetId($validate->except('role,repassword,inpassword',$data));
        if ($result){
            $uid = $result;
            $auth_group = Db::name('auth_group_access')->where('uid',$uid)->find();
            if($auth_group){
                $auth = Db::name('auth_group_access')->where('uid', $uid)->update(['group_id'=>$data['role']]);
            }else{
                $auth = Db::name('auth_group_access')->insert(['uid'=>$uid,'group_id'=>$data['role']]);
            }
        }
        return ($result&&$auth);
    }

    /**
     *  更新管理员
     * @param $data
     * @return mixed
     */
    public function updateAdmin($data){

        //密码没有改变操作
        if(strlen($data['password'])==32){
            $scene = 'noChange';
            unset($data['password']);
        }else{
            $scene = 'update';
            $data['inpassword'] = $data['password'];
            $data['password'] = md5($data['password'].$data['token']);
        }

        $validate = new \app\common\validate\Admin();
        $result = $validate->scene($scene)->check($data);
        if (!$result) {
           $this->error = $validate->getError();
           return false;
        }

        $result = $this->isUpdate(true)->save($validate->except('role,inpassword',$data));

        $auth_group = Db::name('auth_group_access')->where('uid',$data['id'])->find();
        if($auth_group){
            $auth = Db::name('auth_group_access')->where('uid',$data['id'])->update(['group_id'=>$data['role']]);
        }else{
            $auth = Db::name('auth_group_access')->insert(['uid'=>$data['id'],'group_id'=>$data['role']]);
        }
        return  $result||$auth;
    }



}