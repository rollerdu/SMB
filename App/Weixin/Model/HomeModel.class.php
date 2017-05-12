<?php
namespace Weixin\Model;
use Think\Model;
class HomeModel extends Model {
    protected $domainUrl = '';
    public function __construct() {
        $this->domainUrl = C('APP_PATH');
    }
	function getone($sql){
        $result = M()->query($sql);
    	return $result[0];
	}

	function getlist($sql){
		$Home = M();
    	return $Home->query($sql);
	}

	//例：select count(*) as count from `my_home`
	function getcount($sql){
		$Home = M(); 
    	$result = $Home->query($sql);
    	return $result[0]['count'];
	}

	function del($sql){
		$Home = M();
		if($Home->query($sql) === false){
			return false;
		}else{
			return true;
		}
	}

	function update($table,$arr,$id){
		$Home = M($table); 
	    return $Home->where('id='.$id)->save($arr); 
	}

    //对某字段更新内容
    function updateField($table,$arr,$whereField,$wherefieldVal){
        $Home = M($table);
        return $Home->where(" `$whereField` = '$wherefieldVal'")->save($arr);
    }

	function add($table,$arr){
		$Home = M($table);
	    return $Home->add($arr);
	}


	function qeury($sql){
	  $Home = M(); 
      return $Home->query($sql);
	}

}