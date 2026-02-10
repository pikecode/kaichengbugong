<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 电子面单
 *
 * @icon fa fa-circle-o
 */
class ElectronicsOrder extends Backend
{

    /**
     * ElectronicsOrder模型对象
     * @var \app\admin\model\shop\ElectronicsOrder
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\ElectronicsOrder;
        $this->view->assign("paytypeList", $this->model->getPaytypeList());
        $this->view->assign("isNoticeList", $this->model->getIsNoticeList());
        $this->view->assign("isReturnTempList", $this->model->getIsReturnTempList());
        $this->view->assign("isSendMessageList", $this->model->getIsSendMessageList());
        $this->view->assign("isReturnSignBillList", $this->model->getIsReturnSignBillList());
        $this->view->assign("expTypeList", $this->model->getExpTypeList());
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
                ->with(['Shipper'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


}
