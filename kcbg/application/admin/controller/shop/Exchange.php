<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use app\admin\model\shop\Goods;

/**
 * 积分兑换
 *
 * @icon fa fa-circle-o
 */
class Exchange extends Backend
{

    /**
     * Exchange模型对象
     * @var \app\admin\model\shop\Exchange
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Exchange;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    //商城商品生成
    public function creategoods()
    {
        $goods_ids = $this->request->post('goods_ids/a');
        $score = $this->request->post('score/d');
        if (empty($goods_ids)) {
            $this->error('缺少参数');
        }
        if (!$score) {
            $this->error('请输入积分');
        }
        $goods = Goods::where('id', 'IN', $goods_ids)->select();
        $data = [];
        foreach ($goods as $item) {
            $data[] = [
                'type'        => 'reality',
                'title'       => $item->title,
                'content'     => $item->content,
                'description' => $item->description,
                'image'       => $item->image,
                'score'       => $score,
                'stocks'      => $item->stocks
            ];
        }
        $this->model->saveAll($data);
        $this->success('复制成功');
    }
}
