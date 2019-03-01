<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2018/2/11 0011 20:07
 * For: 验证器类
 */

namespace app\common\validate;


use think\Db;

class Admin extends BaseValidate
{
    //验证规则
    protected $rule = [
        'username'    => 'require|length:3,16|alphaDash',
        'password'    => 'require',
        'inpassword'  => 'require|length:6,16|alphaNum',
        'repassword'  => 'require|confirm:inpassword',
        'email'       => 'email',
        'status'      => 'require|in:1,0',
        'role'        => 'require|checkRole:1'
    ];

    //字段信息
    protected $field = [
        'username'      => '管理员名称',
        'password'      => '密文密码',
        'inpassword'    => '明文密码',
        'repassword'    => '确认密码',
        'email'         => '邮箱',
        'status'        => '管理员状态',
        'role'          => '管理员角色'
    ];

    //验证提示信息
    protected $message = [
        'role.require' => '请选择管理员角色',
        'role.checkRole' => '请选择管理员角色',
    ];

    //验证场景
    protected $scene = [
        'insert' => ['username','password','inpassword', 'repassword', 'email', 'status','role'],
        'update' => ['username','password','inpassword', 'email', 'status','role'],
        'noChange' => ['username','email', 'status','role']
    ];

    //自定义验证规则
    public function checkRole($value, $rule){
        $role = Db::name('auth_group')->find($value);
        return  $role?true:false;
    }

}