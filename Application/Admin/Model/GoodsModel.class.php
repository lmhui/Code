<?php
namespace Admin\Model;
use Think\Model;
class GoodsModel extends Model 
{
	protected $insertFields = array('goods_name','price','goods_desc','is_on_sale','is_delete');
	protected $updateFields = array('id','goods_name','price','goods_desc','is_on_sale','is_delete');
	protected $_validate = array(
		array('goods_name', 'require', '商品名称不能为空！', 1, 'regex', 3),
		array('goods_name', '1,45', '商品名称的值最长不能超过 45 个字符！', 1, 'length', 3),
		array('price', 'currency', '商品价格必须是货币格式！', 2, 'regex', 3),
		array('is_on_sale', 'number', '是否上架：1：上架，0：下架必须是一个整数！', 2, 'regex', 3),
		array('is_delete', 'number', '是否已经删除，1：已经删除 0：未删除必须是一个整数！', 2, 'regex', 3),
	);
	public function search($pageSize = 20)
	{
		/**************************************** 搜索 ****************************************/
		$where = array();
		if($goods_name = I('get.goods_name'))
			$where['goods_name'] = array('like', "%$goods_name%");
		$pricefrom = I('get.pricefrom');
		$priceto = I('get.priceto');
		if($pricefrom && $priceto)
			$where['price'] = array('between', array($pricefrom, $priceto));
		elseif($pricefrom)
			$where['price'] = array('egt', $pricefrom);
		elseif($priceto)
			$where['price'] = array('elt', $priceto);
		$sa = I('get.sa');
		$ea = I('get.ea');
		if($sa && $ea)
			$where['addtime'] = array('between', array(strtotime("$sa 00:00:00"), strtotime("$ea 23:59:59")));
		elseif($sa)
			$where['addtime'] = array('egt', strtotime("$sa 00:00:00"));
		elseif($ea)
			$where['addtime'] = array('elt', strtotime("$ea 23:59:59"));
		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this->alias('a')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
		return $data;
	}
	// 添加前
	protected function _before_insert(&$data, $option)
	{
		if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0)
		{
			$ret = uploadOne('logo', 'Admin', array(
				array(150, 150, 2),
			));
			if($ret['ok'] == 1)
			{
				$data['logo'] = $ret['images'][0];
				$data['sm_logo'] = $ret['images'][1];
			}
			else 
			{
				$this->error = $ret['error'];
				return FALSE;
			}
		}
	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
		if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0)
		{
			$ret = uploadOne('logo', 'Admin', array(
				array(150, 150, 2),
			));
			if($ret['ok'] == 1)
			{
				$data['logo'] = $ret['images'][0];
				$data['sm_logo'] = $ret['images'][1];
			}
			else 
			{
				$this->error = $ret['error'];
				return FALSE;
			}
			deleteImage(array(
				I('post.old_logo'),
				I('post.old_sm_logo'),
	
			));
		}
	}
	// 删除前
	protected function _before_delete($option)
	{
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
		$images = $this->field('logo,sm_logo')->find($option['where']['id']);
		deleteImage($images);
	}
	/************************************ 其他方法 ********************************************/
}