<?php
namespace app\admin\controller;


use dh2y\login\login;
use think\auth\Auth;
use think\auth\AuthManager;
use think\facade\Config;
use think\Controller;
use think\facade\Session;

class Index extends Controller
{
    public function index(){
        $config = Config::get('admin_login');

        //获取权限菜单
        $authManager = AuthManager::getInstance();
        $menus = $authManager->getAuthMenu(session($config['auth_uid']));

        $this->assign('menus_lists',$menus);

        //角色名称
        $auth = Auth::instance();
        $user_group = $auth->getGroups(session($config['auth_uid']));
        if(count($user_group)>0&&isset($user_group[0]['title'])){
            $this->assign('user_group',$user_group[0]['title']);
        }
        return $this->fetch();
    }



    /**
     * 后台总控页面（欢迎页面）
     */
    public function console(){
        return $this->fetch();
    }

    /**
     * 登录页面
     */
    public function login(){
        if(request()->post()){
            $login = new login('admin');
            $data = request()->post();
            return $login->doLogin($data);
        }
        return $this->fetch();
    }

    /**
     *记住用户名和密码
     */
    public function member(){
        $member = new login('admin');
        return $member->remember();
    }

    /**
     * 退出
     */
    public function logout(){
        $member = new login();
        $result = $member->logout();
        if($result['status']==true){
            $this->redirect(url($member->user_auth_gateway));
        }
    }

}
