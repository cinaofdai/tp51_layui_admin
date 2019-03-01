<?php
/**
 * Created by pengxingyuan.
 * Date: 2018/8/8
 * Time: 15:23
 */

namespace app\admin\controller;


use app\common\controller\BaseAdmin;
use think\Db;
use think\exception\HttpException;

/**文章分类管理控制器
 * Class Categroy
 * @package app\admin\controller
 */
class ArticleCate extends BaseAdmin
{
    /**
     * 文章分类列表
     */
    public function index(){
        if (request()->isAjax()){
            $data = $this->search('article_cate',false,$cond=[],$desc='asc');   //获取查询的数据
            $cates =  \Data::tree( $data['lists'],'name');                  //获取树形分类结构
            $lists = [];
            foreach ($cates as $value){
                $lists[] = $value;
            }
            $data['lists'] = $lists;
            return $data;
        }else{
            return $this->fetch();
        }
    }
    /**
     * 添加分类
     */
    public function add(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $model = new \app\common\model\ArticleCate();
                if($model->insertCate($data)){
                    return ['status'=>true,'message'=>'分类添加成功'];
                }else{
                    return ['status'=>false,'message'=>$model->getError()];
                }
            }else{
                $cate = Db::name('article_cate')->select();
                $cate =  \Data::tree($cate,'name');                  //获取树形分类结构
                $lists = [];
                foreach ($cate as $value){
                    $lists[] = $value;
                }
                $cate = $lists;
                return $cate;
            }
        }else{
            return $this->fetch();
        }
    }
    /**
     * 编辑分类
     */
    public function edit(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $model = new \app\common\model\ArticleCate();
                if($model->updateCate($data)){
                    return ['status'=>true,'message'=>'分类编辑成功'];
                }else{
                    return ['status'=>false,'message'=>$model->getError()];
                }
            }else{
                $data['model']=Db::name('article_cate')->find(request()->get('id'));
                $cate = Db::name('article_cate')->select();
                $cate =  \Data::tree($cate,'name');                  //获取树形分类结构
                $lists = [];
                foreach ($cate as $value){
                    $lists[] = $value;
                }
                $data['cate']=$lists;
                return $data;
            }
        }else{
            $this->assign('edit_id',request()->get('id'));
            return $this->fetch();
        }
    }
    /**
     * 删除分类
     */
    public function remove(){
        $ids = request()->post('ids');
        if(request()->isAjax()){
            $child = Db::name('article_cate')->where('pid',$ids)->find();
            if($child){
                return ['status'=>false,'message'=>'该分类下存在子分类!'];
            }
            $article=Db::name('article')->where('cid',$ids)->select();
            if($article){
                return ['status'=>false,'message'=>'该分类下存在文章!'];
            }
            $result = Db::name('article_cate')->delete($ids);
            if($result){
                return ['status'=>true,'message'=>'删除成功!'];
            }else{
                return ['status'=>false,'message'=>'删除失败!'];
            }
        }else{
            throw new HttpException(502,'请求不合法');
        }
    }
}