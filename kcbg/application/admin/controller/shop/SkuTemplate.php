<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 规格模板
 *
 * @icon fa fa-circle-o
 */
class SkuTemplate extends Backend
{

    /**
     * SkuTemplate模型对象
     * @var \app\admin\model\shop\SkuTemplate
     */
    protected $model = null;

    protected $selectpageFields = "id,name,spec_names,spec_values";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\SkuTemplate;
    }

    protected function resetSpec()
    {
        $spec = $this->request->post("spec/a");
        $name = $this->request->post("name");
        if ($spec) {
            $spec_names = [];
            $spec_values = [];
            foreach ($spec as $item) {
                if (empty($item['name'])) {
                    $this->error('规格名称不能为空');
                }
                $spec_names[] = $item['name'];
                if (empty($item['value']) || !is_array($item['value'])) {
                    $this->error('规格属性不能为空');
                }
                foreach ($item['value'] as $value) {
                    if (empty($value)) {
                        $this->error('请填写' . $item['name'] . '的属性值');
                    }
                    if (stripos($value, ',') !== false) {
                        $this->error('属性值中不能包含,');
                    }
                }
                if (count($item['value']) != count(array_unique($item['value']))) {
                    $this->error($item['name'] . '的属性值不允许重复');
                }
                $spec_values[] = implode(',', $item['value']);
            }
            return [
                'name'        => $name,
                'spec_names'  => implode(';', $spec_names),
                'spec_values' => implode(';', $spec_values)
            ];
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->resetSpec();
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
                    $this->success('添加成功');
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
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
            $params = $this->resetSpec();
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
                    $this->success('编辑成功');
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->assignconfig('row', $row);
        return $this->view->fetch();
    }
}
