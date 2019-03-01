<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2019/3/1 12:04
 * For:admin模块配置
 */

return [
    'admin_login'=>[
        'crypt' => 'dh2y',      //Crypt加密秘钥
        'auth_uid' => 'authId',      //用户认证识别号(必配)
        'not_auth_module' => 'index', // 无需认证模块
        'user_auth_gateway' => 'index/login', // 默认网关
    ]
];