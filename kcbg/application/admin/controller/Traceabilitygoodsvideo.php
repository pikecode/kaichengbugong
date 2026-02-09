<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 溯源商品视频管理
 *
 * @icon fa fa-circle-o
 */
class Traceabilitygoodsvideo extends Backend
{

    /**
     * Traceabilitygoodsvideo模型对象
     * @var \app\admin\model\Traceabilitygoodsvideo
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Traceabilitygoodsvideo;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());

        $name = 'shop';
        
        $str = get_addon_fullconfig($name)['51']['value'];

        $str = str_replace("，",",",$str);
        // echo $str;
        $this->view->assign("classList", explode(",",$str));
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            // 添加溯源商品ID筛选
            $traceabilitygoods_id = $this->request->get('traceabilitygoods_id');
            if ($traceabilitygoods_id !== '' && $traceabilitygoods_id !== null) {
                $where['traceabilitygoods_id'] = $traceabilitygoods_id;
            }

            // 添加类型筛选
            $type = $this->request->get('type');
            if ($type !== '' && $type !== null) {
                $where['type'] = ['like', "%{$type}%"];
            }

            $list = $this->model
                    ->with(['traceabilitygoods'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {


            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}
