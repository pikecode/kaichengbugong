<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\shop\Area;
use app\admin\model\shop\Freight;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class FreightItems extends Backend
{

    /**
     * FreightItems模型对象
     * @var \app\admin\model\shop\FreightItems
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\FreightItems;
        $this->view->assign("typeList", $this->model->getTypeList());
    }

    public function index()
    {
        //设置过滤方法
        $freight_id = $this->request->param('freight_id');
        $this->assignconfig('freight_id', $freight_id);
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['Freight'])
                ->where($where)
                ->where(function ($query) use ($freight_id) {
                    if ($freight_id) {
                        $query->where('freight_id', $freight_id);
                    }
                })
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


    public function add()
    {
        $freight_id = $this->request->param('freight_id');
        $list = Area::field('id,pid parent,name text')->select();
        $state = ['opened' => false];
        foreach ($list as $item) {
            $item->state = $state;
            $item->icon = 'fa fa-map-marker';
            $item->parent = $item->parent ? $item->parent : '#';
        }
        $row = (new Freight())->get($freight_id);
        $this->assignconfig('areas', $list);
        $this->view->assign('freight_id', $freight_id);
        $this->view->assign('freight', $row);
        return parent::add();
    }


    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
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
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $list = Area::field('id,pid parent,name text')->select();
        $state = ['opened' => false];
        foreach ($list as $item) {
            $item->state = $state;
            $item->icon = 'fa fa-map-marker';
            $item->parent = $item->parent ? $item->parent : '#';
        }
        $this->view->assign('freight_id', $this->request->param('freight_id'));
        $this->assignconfig('areas', $list);
        $this->assignconfig('area_ids', $row['area_ids']);
        $this->assignconfig('postage_area_ids', $row['postage_area_ids']);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
