<?php
namespace Admin\Model;
use Think\Model;
// 配置类型模型
class MenuModel extends Model {

	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
		array('name', 'require', '菜单名称不能为空！', 1, 'regex', Model:: MODEL_BOTH ),
		array('app', 'require', '应用不能为空！', 1, 'regex', Model:: MODEL_BOTH ),
		array('model', 'require', '模块名称不能为空！', 1, 'regex', Model:: MODEL_BOTH ),
		array('action', 'require', '方法名称不能为空！', 1, 'regex', Model:: MODEL_BOTH ),
		array('app,model,action', 'checkAction', '同样的记录已经存在！', 1, 'callback', Model:: MODEL_INSERT   ),
		array('id,app,model,action', 'checkActionUpdate', '同样的记录已经存在！', 1, 'callback', Model:: MODEL_UPDATE   ),
		array('parentid', 'checkParentid', '菜单只支持四级！', 1, 'callback', 1),
	);
	//自动完成
	protected $_auto = array(
		//array(填充字段,填充内容,填充条件,附加规则)
	);

	//验证菜单是否超出三级
	public function checkParentid($parentid) {
		$find = $this->where(array("id" => $parentid))->getField("parentid");
		if ($find) {
			$find2 = $this->where(array("id" => $find))->getField("parentid");
			if ($find2) {
				$find3 = $this->where(array("id" => $find2))->getField("parentid");
				if ($find3) {
					return false;
				}
			}
		}
		return true;
	}

	//验证action是否重复添加
	public function checkAction($data) {
		//检查是否重复添加
		$find = $this->where($data)->find();
		if ($find) {
			return false;
		}
		return true;
	}
	//验证action是否重复添加
	public function checkActionUpdate($data) {
		//检查是否重复添加
		$id=$data['id'];
		unset($data['id']);
		$find = $this->field('id')->where($data)->find();
		if (isset($find['id']) && $find['id']!=$id) {
			return false;
		}
		return true;
	}
    /*
     *  获取所有该角色权限管理下的菜单
     * */
    public function all_admin_menu(){
        $role_id = get_admin_role_id();
        //角色ID 4 为超级管理员
        if($role_id != 4){
            $join = " inner join ".C('DB_PREFIX')."auth_access b ON b.rule_name=a.rule_name ";
            $where['b.role_id'] = $role_id;
        }
        $data = $this->alias('a')->join($join)->where($where)->order(array("a.listorder" => "ASC"))->field('a.*')->select();
        foreach($data as $v){
            $new_data[$v['id']] = $v;
        }
        return $new_data;
    }
	/**
	 *   按父ID查找该角色权限管理下的菜单子项
	 * @ param integer $parentid   父菜单ID
	 * @ param integer $with_self  是否包括他自己
	 */
	public function admin_menu($parentid, $with_self = false) {
		//父节点ID
		$parentid = (int) $parentid;
        //获取角色ID
        $role_id = get_admin_role_id();
        if($role_id != 1){
            $join = " inner join ".C('DB_PREFIX')."auth_access b ON b.rule_name=a.rule_name ";
            $where['b.role_id'] = $role_id;
        }
        $where['a.parentid'] = $parentid;
        $where['a.status']  = 1;
		$result = $this->alias('a')->join($join)->where($where)->order(array("a.listorder" => "ASC"))->select();
//		var_dump($result);
		if ($with_self) {
			$result2[] = $this->where(array('id' => $parentid))->find();
			$result = array_merge($result2, $result);
		}

		return $result;
	}

	/**
	 * 获取菜单 头部菜单导航
	 * @param $parentid 菜单id
	 */
	public function submenu($parentid = '', $big_menu = false) {
		$array = $this->admin_menu($parentid, 1);
		$numbers = count($array);
		if ($numbers == 1 && !$big_menu) {
			return '';
		}
		return $array;
	}

	/**
	 * 菜单树状结构集合
	 */
	public function menu_json() {
		$data = $this->get_tree(0);
		return $data;
	}

	//取得角色权限下的树形结构菜单
	public function get_tree($myid, $parent = "", $Level = 1) {
		$data = $this->all_admin_menu($myid);
//		$Level++;
		if (is_array($data)) {
			$ret = NULL;
			foreach ($data as $a) {
				//隐藏的菜单不显示
				if($a['status'] == 1){
					$id = $a['id'];
					$name = ucwords($a['app']);
					$model = ucwords($a['model']);
					$action = $a['action'];
					//附带参数
					$fu = "";
					if ($a['data']) {
						$fu = "?" . htmlspecialchars_decode($a['data']);
					}
					$array = array(
						"icon" => $a['icon'],
						"id" => $id,
						"model" => $model,
						"name" => $a['name'],
						"parentid" => $a['parentid'],
						"url" => U("{$name}/{$model}/{$action}{$fu}"),
						'lang'=> strtoupper($name.'_'.$model.'_'.$action),
						"level"=>_get_level($id,$data),
					);
					if($array['level'] == 0){
						$ret[$id] = $array;
					}elseif($array['level'] == 1){
						$ret2[$id] = $array;
					}
				}
			}
            foreach($ret2 as $re){
                $ret[$re['parentid']]['items'][] = $re;
            }
			return $ret;
		}

		return false;
	}

	/**
	 * 后台有更新/编辑则删除缓存
	 * @param type $data
	 */
	public function _before_write(&$data) {
		parent::_before_write($data);
		F("Menu", NULL);
	}

	//删除操作时删除缓存
	public function _after_delete($data, $options) {
		parent::_after_delete($data, $options);
		$this->_before_write($data);
	}

	public function menu($parentid, $with_self = false){
		//父节点ID
		$parentid = (int) $parentid;
		$result = $this->where(array('parentid' => $parentid))->select();
		if ($with_self) {
			$result2[] = $this->where(array('id' => $parentid))->find();
			$result = array_merge($result2, $result);
		}
		return $result;
	}
	/**
	 * 得到某父级菜单所有子菜单，包括自己
	 * @param number $parentid
	 */
	public function get_menu_tree($parentid=0){
		$menus=$this->where(array("parentid"=>$parentid))->order(array("listorder"=>"ASC"))->select();

		if($menus){
			foreach ($menus as $key=>$menu){
				$children=$this->get_menu_tree($menu['id']);
				if(!empty($children)){
					$menus[$key]['children']=$children;
				}
				unset($menus[$key]['id']);
				unset($menus[$key]['parentid']);
			}
			return $menus;
		}else{
			return $menus;
		}

	}
}
?>