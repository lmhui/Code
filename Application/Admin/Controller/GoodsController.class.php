<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 17/1/9
 * Time: 10:21
 */
namespace Admin\Controller;
use Think\Controller;

class GoodsController extends Controller {

    public function add(){
        //处理表单
        if(IS_POST){

            //先生成模型
            //注意D和M的不同，D是生成自己定义的模型，M是生成TP自带的模型对象

            $model = D('Goods');

            //1.接收表单中所有的数据并保存到模型中 2.使用I函数会过滤数据  3.根据模型中定义的规则验证表单
            if($model->create(I('post.'),1)){
                //I('post.')是指create方法要接收的数据过滤后的$_POST  1是指定当前是添加的表单使用的是insertFields属性

                //插入数据
                if($model->add()){
                    //提示信息
                    $this->success('操作成功！',U('lst'));
                    //停止后面的代码
                    exit;
                }
            }
            //如果失败，获取失败的原因
            $error = $model->getError();
            //设置提示信息，并跳回到上一个页面
            $this->error($error);
        }
        $this->display();
    }

    public function edit(){

    }

    public function delete(){

    }

    //列表
    public function lst(){

        $model = D('Goods');
        //获取带翻页的数据
        $data = $model->search();
        $this->assign(array(
            'data' => $data['data'],
            'page' => $data['page'],
        ));
        $this->display();

    }

}