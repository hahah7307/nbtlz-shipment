<?php
namespace app\Manage\controller;

use app\Manage\model\AccountModel;
use app\Manage\model\AdminUserRoleModel;
use think\Controller;
use think\Config;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Session;
use think\Request;

class BaseController extends Controller
{
    /**
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws DataNotFoundException
     */
    public function _initialize()
    {
		parent::_initialize();

		// 验证登录态
		if (empty(Session::get(Config::get('USER_LOGIN_FLAG')))) {
			$this->redirect('Login/index');
		} else {
            $account = new AccountModel();
			$user = $account->where(['id'=>Session::get(Config::get('USER_LOGIN_FLAG')), 'status' => AccountModel::STATUS_ACTIVE])->find();
			$this->assign('user', $user);

            $userRole = new AdminUserRoleModel();
            $role = $userRole->where(['user_id' => Session::get(Config::get('USER_LOGIN_FLAG'))])->column('role_id');
            $this->assign('role', $role);
		}

		// 加载菜单
		$this->assign('userMenu', Session::get(Config::get('USER_MENU')));

		// 加载请求类
		$this->request = Request::instance();

		// 验证权限
		$access = Session::get('access', 'access');
		$controller = strtolower($this->request->controller());
		$action = strtolower($this->request->action());
		if (!AccountModel::action_access($controller, $action, $access, $user)) {
			$this->error('您没有操作权限！');
		}

		// 记录当前模块名
		session('module', $this->request->module());

		// 编辑器插件、模块
		$this->assign('tinymce', ['plugins' => Config::get('TINYMCE_PLUGINS'), 'toolbar' => Config::get('TINYMCE_TOOLBAR')]);
    }
}
