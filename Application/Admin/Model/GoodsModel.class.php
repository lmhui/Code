<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 17/1/9
 * Time: 11:06
 */
namespace Admin\Model;
use Think\Model;
class GoodsModel extends Model{

    //在添加的时候调用create方法是允许接受的字段
    protected $insertFields = array('goods_name','price','goods_desc','is_on_sale');
    //定义表单验证的规则，控制器的create方法是调用
    protected $_validate = array(
        array('goods_name','require','商品名称不能为空！',1),
        array('goods_name','1,45','商品名称必须是1-45个字符',1,'length'),
        array('price','currency','价钱必须是货币格式',1),
        array('is_on_sale','0,1','是否上架只能是0，1两个值',1,'in'),
    );

    //tp在调用add方法之前会自动调用这个函数，我们可以把插入数据库之前要执行的代码写在这里
    //第一个参数：表单的数据，一个一维数组
    //第二个参数：额外的信息：当前模型对应实际的表名是什么
    //说明：在这个函数中药改变这个函数的外部的$data，需要按钮引用传递
    //说明：如果return false是指控制器中的add返回的false

    protected function _before_insert(&$data,$option){
        //获取当前时间
        $data['addtime'] = time();
        //上传logo
        if($_FILES['logo']['error'] == 0){
            $rootPath = C('IMG_rootPath');
            $upload = new \Think\Upload(array(
                'rootPath' => $rootPath,
            ));//实例化上传类
            $upload->maxSize = (int)C('IMG_maxSize')*1024*1024;  //设置附件上传大小
            $upload->exts = C('IMG_exts');                      //设置上传类型
            $upload->savePath = 'Goods/';                       //图片二级目录名称
            //上传文件
            $info = $upload->upload();
            if(!$info){
                //先把上传失败的错误信息存到模型中，再由控制器最终获取这个错误信息并显示
                $this->error = $upload->getError();
                return FALSE;   //返回控制器
            }else{
                $logoName = $info['logo']['savepath'].$info['logo']['savename'];
                //拼出缩略图的文件名
                $smLogoName = $info['logo']['savepath'].'thumb_'.$info['logo']['savename'];
                //生成缩略图
                $image = new \Think\Image();
                //打开要处理的表单放到表单中
                $image->open($rootPath.$logoName);
                $image->thumb(150,150)->save($rootPath.$smLogoName);
                //吧图片的表单放到表单中
                $data['logo'] = $logoName;
                $data['sm_logo'] = $smLogoName;
            }
        }
    }

    public function search(){
        //搜索
        $where = array();
        $goodsName = I('get.goods_name');
        //商品名称搜索
        if($goodsName)
            $where['goods_name'] = array('like',"%$goodsName%");
        //价格搜索
        $startPrice = I('get.start_price');
        $endPrice = I('get.end_price');
        if($startPrice&&$endPrice)
            $where['price'] = array('between',array($startPrice,$endPrice));
        elseif($startPrice)
            $where['price'] = array('egt',$startPrice);
        elseif($endPrice)
            $where['price'] = array('elt',$endPrice);
        //上架的搜索
        $isOnSale = I('get.is_on_sale',-1);
        if($isOnSale != -1)
            $where['is_on_sale'] = array('eq',$isOnSale);
        //是否删除的搜索
        $isDelete = I('get.is_delete',-1);
        if($isDelete != -1)
            $where['is_delete'] = array('eq',$isDelete);

        //排序
        $orderby = 'id';  //默认排序字段
        $orderway = 'asc';//默认排序方式
        $odby = I('get.odby');
        if($odby && in_array($odby,array('id_asc','id_desc','price_asc','price_desc'))){
            if($odby == 'id_desc')
                $orderway = 'desc';
            elseif($odby == 'price_asc')
                $orderway = 'price';
            elseif($odby == 'price_desc'){
                $orderway = 'price';
                $orderby = 'desc';
            }
        }
        //翻页
        //总的数页
        $count = $this->where($where)->count();
        //翻页的对象
        $page = new \Think\Page($count,2);
        //获取翻页的字符串
        $pageString = $page->show();
        //取出当前页的数据
        $data = $this->where($where)->limit($page->firstRow.','.$page->listRows)->order("$orderby $orderway")->select();

        return array(
            'page' => $pageString,
            'data' => $data,
        );
    }


}