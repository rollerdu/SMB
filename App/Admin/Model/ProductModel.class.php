<?php
namespace Admin\Model;
use Think\Model;
class ProductModel extends Model{
    protected $_validate = array(
        array('title','require','请填写商品标题！'),
        array('price','require','请填写商品价格！'),
        array('intro','require','请填写商品简介！'),
        array('cate_id','require','请选择商品分类！'),
        array('content','require','请填写商品详情！'),
//        array('inventory','require','请填写商品库存量！'),
    );



}