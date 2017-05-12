<?php
namespace Admin\Controller;
//namespace Think\Cache\Driver;
use Think\Controller;
//use Think\Cache\Driver;

/**
 * 缓存管理
 */
class CacheController extends BaseController {

    public $voList = array(
                array('val'=>'Data','name'=>'更新数据缓存'),
                array('val'=>'Cache/Admin','name'=>'更新后台模板缓存'),
            );



    // 更新缓存
    public function index() {
        $cacheArr = $this->voList;
        foreach ($cacheArr as $val) {
            $path = RUNTIME_PATH.''.$val['val'];
            $this->_updateTpl($path);
        }
        write_system_log();
        $this->display();
    }



    //删除模板文件
    private  function _updateTpl($path) {
        $tpl = dir($path);
        while($entry = $tpl->read()) {
            if(preg_match("/\.php$/", $entry)) {
                @unlink($path.'/'.$entry);
            }
        }
        $tpl->close();
    }
    //清除指定彩信。文章。视频缓存页
    public function delete_one_cache($id=0,$type=''){
        if(I('param.id') && I('param.type')){
            $id   = intval(I('param.id'));
            $type = I('param.type');
        }else{
            $where = true;
        }
        if($type == 'mms'){
            $path = HTML_PATH.'Mms/'.$id.'.html';
        }elseif($type == 'hot'){
            $path = HTML_PATH.'Hot/'.$id.'.html';
        }elseif($type == 'video'){
            $path = HTML_PATH.'Video/'.$id.'.html';
        }else{
            if($where){
                return false;
            }else{
                $this->error('数据有误');
            }
        }
        if(file_exists($path)){
            @unlink($path);
        }
        if($where){
            return true;
        }else{
            $this->success('清除成功！');
        }
    }

    public function setKeyWord(){
        $result = '';
        $key = "keyword_uuid";
        $value = json_encode(array('name'=>'18863334441'));
        $redis = new \Think\Cache\Driver\Redis();
        $result = $redis->rpush( $key,$value);
        $get = $redis->lrange($key);
        print_r($result)."\r\n";
        print_r($get);
    }

}
