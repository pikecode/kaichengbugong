<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 评论管理
 *
 * @icon fa fa-circle-o
 */
class Comment extends Backend
{

    /**
     * Comment模型对象
     * @var \app\admin\model\shop\Comment
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Comment;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $this->view->assign('type', $this->request->param('type'));
        return parent::edit($ids);
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['User', 'Goods'])
                ->where($where)
                ->where('pid', 0)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $index => $item) {
                if ($item->user) {
                    $item->user->visible(['id', 'nickname', 'avatar']);
                }
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function reply()
    {

        $pid = $this->request->param('pid');
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $row = $this->model->where('id', $pid)->find();
            if (!$row) {
                $this->error('未找到记录');
            }
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $params['pid'] = $pid;
                    $params['user_id'] = $this->auth->id;
                    $params['ip'] = request()->ip();
                    $params['useragent'] = substr(request()->server('HTTP_USER_AGENT'), 0, 255);
                    $row->setInc('comments');
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $list = $this->model->where('pid', 'eq', $pid)->select();
        $this->assign('reply_list', $list);

        return $this->view->fetch();
    }
}
