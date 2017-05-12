<?php
namespace Admin\Controller;
use Admin\Controller;
use Extend;
class MenuController extends BaseController {
    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    protected $menu_model;
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->menu_model = D("Menu");
    }

    /**
     *  显示菜单
     */
    public function index() {
        $_SESSION['admin_menu_index']="Menu/index";
        $result = $this->menu_model->order(array("listorder" => "ASC"))->select();
        $tree = new \Extend\Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $newmenus=array();
        foreach ($result as $m){
            $newmenus[$m['id']]=$m;

        }
        foreach ($result as $n=> $r) {
//onclick="system_category_add(\'添加栏目\',"'. U("Menu/add", array("parentid" => $r['id'], "menuid" => I("get.menuid")).'")
            $result[$n]['level'] = _get_level($r['id'], $newmenus);
            $result[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . '"' : '';

            $result[$n]['str_manage'] = '<a href="javascript:void(0);" onclick="system_category_add(\'添加栏目\',\''.
                U("Menu/add", array("parentid" => $r['id'], "menuid" => I("get.menuid"))).'\')">添加</a> | <a href="javascript:void(0);" onclick="system_category_add(\'编辑栏目\',\'' .
                U("Menu/edit", array("id" => $r['id'],"menuid" => I("get.menuid"))) .'\')">编辑</a> <!--| <a class="js-ajax-delete" href="' .
                U("Menu/delete", array("id" => $r['id'], "menuid" => I("get.menuid")) ). '">删除</a>--> ';
            $result[$n]['status'] = $r['status'] ? "显示" : "隐藏";
            if(APP_DEBUG){
                $result[$n]['app']=$r['app']."/".$r['model']."/".$r['action'];
            }
        }
        $tree->init($result);
        $str = "<tr id='node-\$id' \$parentid_node>
					<td style='text-align: center;width:100px;'>
					<input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input input-order'></td>
					<td>\$id</td>
        			<td>\$app</td>
					<td>\$spacer\$name</td>
				    <td>\$status</td>
					<td>\$str_manage</td>
				</tr>";
        $categorys = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorys);
        $this->display();
    }
    /**
     *  添加
     */
    public function add() {
        if(IS_POST){
            $app=I("post.app");
            $model=I("post.model");
            $action=I("post.action");
            if ($this->menu_model->create()) {
                $this->menu_model->rule_name = strtolower("$app/$model/$action");
                $check = $this->menu_model->where(array('rule_name'=>$this->menu_model->rule_name))->find();
                if($check) $this->error('请勿重复添加！');
                if ($this->menu_model->add()!==false) {
                    $this->setNewMenu();
                    write_system_log();
                    $this->success("添加成功！");
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->menu_model->getError());
            }
            exit;
        }
        $tree = new \Extend\Tree();
        $parentid = intval(I("get.parentid"));
        $result = $this->menu_model->order(array("listorder" => "ASC"))->select();
        foreach ($result as $r) {
            $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
            $array[] = $r;
        }
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign("select_categorys", $select_categorys);
        $this->display();
    }
    /**
     *  删除
     */
    public function delete() {
        $id = intval(I("get.id"));
        $count = $this->menu_model->where(array("parentid" => $id))->count();
        if ($count > 0) {
            $this->error("该菜单下还有子菜单，无法删除！");
        }
        if ($this->menu_model->delete($id)!==false) {
            $this->setNewMenu();
            write_system_log();
            $this->success("删除菜单成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     *  编辑
     */
    public function edit() {
        $id = intval(I("param.id"));
        if (IS_POST) {
            $app=I("post.app");
            $model=I("post.model");
            $action=I("post.action");
            $name=strtolower("$app/$model/$action");
            if ($this->menu_model->create()) {
                $this->menu_model->rule_name = strtolower("$app/$model/$action");
                if ($this->menu_model->where(array('id'=>$id))->save() !== false) {
                    $this->setNewMenu();
                    write_system_log();
                    $this->success("更新成功！", U("Menu/index"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($this->menu_model->getError());
            }
            exit;
        }
        $tree = new \Extend\Tree();
        $rs = $this->menu_model->where(array("id" => $id))->find();
        $result = $this->menu_model->order(array("listorder" => "ASC"))->select();
        foreach ($result as $r) {
            $r['selected'] = $r['id'] == $rs['parentid'] ? 'selected' : '';
            $array[] = $r;
        }
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign("data", $rs);
        $this->assign("select_categorys", $select_categorys);
        $this->display();
    }

    //排序
    public function listorders() {
        $status = parent::_listorders($this->menu_model);
        if ($status) {
           $this->setNewMenu();
            write_system_log();
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }
    //操作完菜单，菜单重新生成
    private function setNewMenu(){
        cookie('admin_menu',null);
        return true;
    }

}