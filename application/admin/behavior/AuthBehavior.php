<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2018/4/27 0027 22:36
 * For: 权限检测行为
 */

namespace app\admin\behavior;


use think\auth\Auth;
use think\facade\Config;
use think\facade\Request;
use think\helper\Str;
use think\facade\View;

class AuthBehavior
{


    public function run($params){
        //获取当前模块
        $request   = Request::instance();
        $module    = $request->module();
        $controller= $request->controller();
        $action    = $request->action();
        $url = Str::lower($module.DIRECTORY_SEPARATOR.$controller.DIRECTORY_SEPARATOR.$action);


        $auth = Auth::instance();
        $config = Config::get('admin_login');

        //获取无需认证模块配置
        $modules = explode(',',$config['not_auth_module']);
        if(!in_array(Str::lower($controller),$modules)&&!$auth->check($url, session($config['auth_uid']))){
            if($request->isAjax()){
                die(json_encode(['status'=>false,'message'=>'对不起您没有该操作权限!']));
            }else{
                $view   = View::instance(Config::get('template'), Config::get('view_replace_str'));
                echo $view->fetch('index/access_denied');die;
            }
        }
    }
}