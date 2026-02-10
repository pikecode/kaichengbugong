<?php

namespace app\admin\controller\shop;

use addons\shop\model\TemplateMsg;
use app\common\controller\Backend;
use app\admin\model\shop\OrderGoods;
use app\admin\model\shop\OrderAftersales;
use app\admin\model\shop\OrderAction;
use app\admin\model\shop\OrderElectronics;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\shop\Order
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'id,order_sn';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Order;
        $this->view->assign("orderstateList", $this->model->getOrderstateList());
        $this->view->assign("shippingstateList", $this->model->getShippingstateList());
        $this->view->assign("paystateList", $this->model->getPaystateList());
        $this->view->assign("statusList", $this->model->getStatusList());
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
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            // dump($this->request->request());exit;
            if(!$this->request->request('is_dc')){
                list($where, $sort, $order, $offset, $limit,, $alias) = $this->buildparams();
                $a = reset($alias);
                $list = $this->model
                    ->field($a . '.*,oe.print_template,oe.id oe_id')
                    ->alias($alias)
                    ->where($where)
                    ->join('shop_order_electronics oe', $a . '.order_sn=oe.order_sn and oe.status=0', 'LEFT')
                    ->order($sort, $order)
                    ->paginate($limit);
    
                $result = array("total" => $list->total(), "rows" => $list->items());
    
                return json($result);
            }else{

                list($where, $sort, $order, $offset, $limit,, $alias) = $this->buildparams();
                $a = reset($alias);
                $list = $this->model
                ->field("'小程序商城' as name,og.order_sn,$a.receiver,'' as sheng,'' as shi,'' as qu,$a.mobile,'' as guhua,'' as youbian,$a.user_id as wangming, $a.address, '款到发货' as kdfh,$a.saleamount,
                $a.payamount,$a.shippingfee,$a.discount,'' as codmaiija,'' as ckmc,'' as wlgx,'' as xdsj,'' as youbianfksj,'' as mjbz,'' as kfbz,'' as fxs,'' as fptt,'' as fpnr,'' as zffs,'' as ywy,'' as youbian
                ,og.goods_sn,og.nums,'' as hpjg,og.realprice,'' as hpyh,'' as uzdd,'' as sfzp,'' as yjjz,'' as beizhu,'' as ddlb,'' as zjhm,'' as pch,'' as bj")
                ->alias($alias)
                ->where($where)
                // ->join('shop_order_electronics oe', $a . '.order_sn=oe.order_sn and oe.status=0', 'LEFT')
                ->join('shop_order_goods og', $a . '.order_sn = og.order_sn', 'LEFT')
                ->order($sort, $order)
                ->select();

                $this->daochu($list);
            }
            
        }
        $config = get_addon_config('shop');
        $this->assignconfig('shop', $config);
        return $this->view->fetch();
    }

    
    /**
     * 查看
     */
    public function ddd()
    {

        list($where, $sort, $order, $offset, $limit,, $alias) = $this->buildparams();

        $a = reset($alias);

        $list = $this->model
        ->field("'小程序商城' as name,og.order_sn,$a.receiver,'' as sheng,'' as shi,'' as qu,$a.mobile,'' as guhua,'' as youbian,$a.user_id as wangming, $a.address, '款到发货' as kdfh,$a.saleamount,
        $a.payamount,$a.shippingfee,$a.discount,'' as codmaiija,'' as ckmc,'' as wlgx,'' as xdsj,'' as youbianfksj,'' as mjbz,'' as kfbz,'' as fxs,'' as fptt,'' as fpnr,'' as zffs,'' as ywy,'' as youbian
        ,og.goods_sn,og.nums,'' as hpjg,og.realprice,'' as hpyh,'' as uzdd,'' as sfzp,'' as yjjz,'' as beizhu,'' as ddlb,'' as zjhm,'' as pch,'' as bj")
        ->alias($alias)
        ->where($where)
        // ->join('shop_order_electronics oe', $a . '.order_sn=oe.order_sn and oe.status=0', 'LEFT')
        ->join('shop_order_goods og', $a . '.order_sn = og.order_sn', 'LEFT')
        ->order($sort, $order)
        ->select();

        $this->daochu($list);
    }

    //发货单列表
    public function orderList()
    {
        $ids = $this->request->post('ids/a');
        if (empty($ids)) {
            $this->error('参数错误');
        }
        $orderList = $this->model->with(['User', 'OrderGoods'])->order('createtime desc')->where('id', 'IN', $ids)->select();

        foreach ($orderList as $index => $item) {
            if ($item->user) {
                $item->user->visible(['id', 'nickname', 'avatar']);
            }
        }

        $orderList = collection($orderList)->toArray();
        foreach ($orderList as $index => &$item) {
            $nums = 0;
            foreach ($item['order_goods'] as $key => $goods) {
                if ($goods['salestate'] == 4 || $goods['salestate'] == 5) {
                    unset($item['order_goods'][$key]);
                }
                $nums += $goods['nums'];
            }
            $item['nums'] = $nums;
        }
        $this->success('获取成功', '', ['orderList' => $orderList]);
    }

    //订单详情
    public function detail()
    {
        $id = $this->request->param('ids');
        if (!$id) {
            $this->error('参数错误');
        }
        $row = $this->model->field('o.*,sum(s.refund) refund')->with(['User', 'OrderGoods', 'OrderAction'])
            ->where('o.id', $id)
            ->alias('o')
            ->join('shop_order_aftersales s', 'o.id=s.order_id and s.status=2 and s.type <> 3', 'LEFT')
            ->find();

        if (!$row) {
            $this->error('记录未找到');
        }

        //计算单个商品折扣后的价格
        $saleamount = bcsub($row['saleamount'], $row['shippingfee'], 2);
        $saleratio = $row['goodsprice'] > 0 ? bcdiv($saleamount, $row['goodsprice'], 10) : 1;
        $saleremains = $saleamount;
        $orderItem = $row->order_goods;
        foreach ($orderItem as $index => $item) {
            if (!isset($orderItem[$index + 1])) {
                $saleprice = $saleremains;
            } else {
                $saleprice = $row['discount'] == 0 ? bcmul($item['price'], $item['nums'], 2) : bcmul(bcmul($item['price'], $item['nums'], 2), $saleratio, 2);
            }
            $saleremains = bcsub($saleremains, $saleprice, 2);
            $item['saleprice'] = $saleprice;
        }

        $config = get_addon_config('shop');
        $this->assignconfig('shop', $config);

        $this->view->assign('row', $row);
        return $this->view->fetch();
    }

    //订单状态操作
    public function edit_status()
    {
        $orderstate = $this->request->post('orderstate');
        $paystate = $this->request->post('paystate');
        $order_id = $this->request->post('order_id');

        $order = $this->model->where('id', $order_id)->find();
        if (!$order) {
            $this->error('未找到订单记录');
        }
        //取消
        if ($orderstate == 1 && $order->orderstate == 0 && $order->paystate == 0) {
            $order->orderstate = 1;
            $order->canceltime = time();
            $order->save();
            $this->success('取消成功');
        }
        // 支付
        if ($paystate == 1 && $order->orderstate == 0 && $order->paystate == 0) {
            $order->paystate = 1;
            $order->paytype = 'system';
            $order->method = 'system';
            $order->payamount = $order->saleamount;
            $order->paytime = time();
            $order->save();

            //发送通知
            TemplateMsg::sendTempMsg(0, $order->order_sn);
            $this->success('操作成功');
        }
        //已完成
        if ($orderstate == 3 && $order->orderstate == 0 && $order->paystate == 1) {
            $order->orderstate = 3;
            $order->save();
            OrderAction::push($order->order_sn, '更改订单为已完成', '管理员');
            $this->success('操作成功');
        } elseif ($order->orderstate == 4) {
            $this->error("请完成售后后再进行操作");
        }

        $this->error('没有权限操作');
    }

    //同意退款(退货)
    public function refund()
    {
        $id = $this->request->post('order_goods_id');
        if (!$id) {
            $this->error('参数错误');
        }
        $orderGoods = OrderGoods::get($id, ['Order']);
        if (empty($orderGoods)) {
            $this->error('未找到记录');
        }
        $order = $orderGoods->order;
        if (empty($order)) {
            $this->error('订单不存在');
        }

        if ($orderGoods->salestate != 3) {
            $this->error('不支持的售后状态');
        }
        $aftersales = (new OrderAftersales())
            ->where('order_goods_id', $id)
            ->where('user_id', $order->user_id)
            ->where('status', 2)
            ->order('id desc')
            ->find();

        if (!$aftersales) {
            $this->error('售后单未找到');
        }
        if ($aftersales['type'] == 2) {
            //已退货退款
            $orderGoods->salestate = 5;
            $orderGoods->save();
            //已通过，是否同步退款
            $config = get_addon_config('shop');
            if ($config['order_refund_sync']) {
                //执行同步退款
                try {
                    \app\admin\model\shop\Order::refund($order->order_sn, $order->paytype, $aftersales->refund);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            //记录操作
            OrderAction::push($order->order_sn, '确认售后商品已收到', '管理员');

            $count = \app\admin\model\shop\OrderGoods::where('order_sn', $order['order_sn'])->where('salestate', 'not in', [4, 5])->count();
            if (!$count) {
                $order->refundtime = time();
                $order->orderstate = 3;
                $order->save();
            } else {
                $order->orderstate = 0;
                $order->save();
            }
        }
        $this->success('确认收货成功！');
    }


    //编辑订单信息【备注等】
    public function edit_info()
    {
        $id = $this->request->post('id');
        $field = $this->request->post('field');
        $value = $this->request->post('value');
        if (!in_array($field, ['memo'])) {
            $this->error('没有权限编辑');
        }
        $row = $this->model->with(['User'])->where('id', $id)->find();
        if (!$row) {
            $this->error('记录未找到');
        }
        $row->$field = $value;
        $row->save();
        $this->success('保存成功');
    }

    //发货
    public function deliver()
    {
        $expressname = $this->request->post('expressname');
        $expressno = $this->request->post('expressno');
        $order_id = $this->request->post('order_id');
        $type = $this->request->post('type');
        $order = $this->model->where('id', $order_id)->find();
        if (!$order) {
            $this->error('未找到订单记录');
        }
        //发货 / 修改快递信息
        if ($order->orderstate == 0 && $order->shippingstate == 0 && $order->paystate == 1 && $type == 0) {
            $order->expressname = $expressname;
            $order->expressno = $expressno;
            $order->shippingstate = 1;
            $order->shippingtime = time();
            $order->save();
            $this->success('发货成功');
        } elseif ($type == 1) {
            $order->expressname = $expressname;
            $order->expressno = $expressno;
            $order->save();
            $this->success('修改成功');
        } elseif ($order->orderstate == 4) {
            $this->error("请完成售后后再进行操作");
        }
        $this->error('没有权限操作');
    }

    //电子面单【单独】
    public function electronics()
    {
        $order_id = $this->request->param('order_id');
        $row = $this->model->field('o.id,oe.print_template')
            ->where('o.id', $order_id)
            ->alias('o')
            ->join('shop_order_electronics oe', 'oe.order_sn=o.order_sn and oe.status=0', 'LEFT')
            ->find();

        if ($row && !empty($row['print_template'])) {
            $this->success('获取成功', '', ['PrintTemplate' => $row->print_template]);
        }
        $res = [];
        $electronics_id = $this->request->param('electronics_id');
        try {
            $res = \addons\shop\library\KdApiExpOrder::create($order_id, $electronics_id);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        if (!isset($res['Success']) || !$res['Success']) {
            $msg = isset($res['Reason']) ? $res['Reason'] : '请求失败';
            $this->error($msg);
        }
        //成功
        $this->success('开始打印', '', $res);
    }

    //批量打印
    public function prints()
    {
        $ids = $this->request->param('ids');
        if (empty($ids)) {
            $this->error('打印参数错误');
        }
        $order_ids = explode('_', $ids);
        if ($this->request->isPost()) {
            $electronics_id = $this->request->post('electronics_id');
            $param = [];
            //是否已获取
            $list = $this->model->field('o.id,oe.print_template')
                ->where('o.id', 'IN', $order_ids)
                ->alias('o')
                ->join('shop_order_electronics oe', 'oe.order_sn=o.order_sn and oe.status=0', 'LEFT')
                ->select();
            $has_ids = [];
            //获取过的
            if ($list) {
                foreach ($list as $item) {
                    if (!empty($item['print_template'])) {
                        $param[] = [
                            'PrintTemplate' => $item['print_template'],
                            'Success'       => true
                        ];
                        $has_ids[] = $item['id'];
                    }
                }
            }
            //剩余未获取的
            try {
                foreach (array_diff($order_ids, $has_ids) as $order_id) {
                    $res = \addons\shop\library\KdApiExpOrder::create($order_id, $electronics_id);
                    if ($res) {
                        $param[] = $res;
                    }
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success('操作成功', '', $param);
        }
        $config = get_addon_config('shop');
        $this->assignconfig('shop', $config);
        $this->assignconfig('order_ids', $order_ids);
        $order = $this->model->with(['OrderGoods'])->where('id', 'IN', $order_ids)->select();
        $this->view->assign('order', $order);
        return $this->view->fetch();
    }

    //取消电子面单
    public function cancel_electronics()
    {
        $oe_id = $this->request->param('oe_id');
        $row = OrderElectronics::get($oe_id);
        if ($row['status'] == 1) {
            $this->error('电子面单已取消');
        }
        $res = \addons\shop\library\KdApiExpOrder::cancel($row);
        if (isset($res['Success']) && $res['Success']) {
            $row->status = 1;
            $row->save();
            $this->success('取消成功', '', $res);
        }
        $msg = isset($res['Reason']) ? $res['Reason'] : '取消失败';
        $this->error($msg);
    }

    //退款查询
    protected function refunded($order)
    {
        $config = \addons\epay\library\Service::getConfig($order['paytype']);
        $response = null;
        try {
            if ($order['paytype'] == 'wechat') {
                $response = \Yansongda\Pay\Pay::wechat($config)->find([
                    'type'         => in_array($order['method'], ['miniapp', 'app']) ? $order['method'] : '',
                    'out_trade_no' => $order['order_sn']
                ], 'refund');
            } elseif ($order['paytype'] == 'alipay') {
                $response = \Yansongda\Pay\Pay::alipay($config)->find([
                    'out_trade_no'   => $order['order_sn'],
                    'out_request_no' => $order['order_sn']
                ], 'refund');
            }
        } catch (\Exception $e) {
            $this->assign('refunded_info', '查询退款失败，' . $e->getMessage());
        }
        if ($order['paytype'] == 'wechat') {
            if ($response && $response['return_msg'] == 'OK') {
                $this->assign('refunded_info', '已成功退款：' . $response->refund_fee / 100 . '元。');
            }
        } elseif ($order['paytype'] == 'alipay') {
            if ($response && $response['msg'] == 'Success') {
                $this->assign('refunded_info', '已成功退款：' . $response->refund_amount . '元。');
            }
        } else {
            $this->assign('refunded_info', "未知支付类型");
        }
    }

    public function daochu($data)
    {

        $adrinfo = array();

        // if (!empty($adrinfo)) {

            //实例化
            $spreadsheet = new Spreadsheet();

            $filename = "订单导出".date("Y-m-d H:i:s");//$adrinfo['name'] . '_ADR报告' . date('YmdHis') . rand(10000, 99999);       //导出文件名

            $sheet = $spreadsheet->getActiveSheet();

            // 设置行高
            // $sheet->getRowDimension(1)->setRowHeight(30);

            //设置列宽
            $sheet->getDefaultColumnDimension()->setWidth('15');

            $str = "店铺名称	原始单号	收件人	省	市	区	手机	固话	邮编	网名	地址	发货条件	应收合计	已付金额	邮费	优惠金额	COD买家费用	仓库名称	物流公司	下单时间	付款时间	买家备注	客服备注	分销商	发票抬头	发票内容	支付方式	业务员	商家编码	货品数量	货品价格	货品总价	货品优惠	源子订单号	是否赠品	预计结账时间	备注	订单类别	证件号码	批次号	标记";

            $head = explode("	",$str);
            
            $letter_all=[];

            $letter=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
            // 通过标题字段生成excel字母 例如3个字段，生成A-C

            $item=0;

            foreach($head as $k => $v){
                if($item==26){
                    $item=0;
                }
                $n=ceil(($k+1)/26);
                if($n==1){
                    array_push($letter_all,$letter[$item]);
                }else{
                    $n=$n-2;
                    array_push($letter_all,$letter[$n].$letter[$item]);
                }
                $item++;
            }
            // echo count($head);
            foreach ($head as $k => $v){
                $sheet->setCellValue($letter_all[$k].'1', $v);
            }


            // foreach ($head as $k => $v){

                foreach($data as $kk => $vv){

                    $row = $vv->toArray();
                    // dump($row);exit;
                    $i = 0;
                    // echo count($row);
                    foreach($row as $kkk => $vvv){
                        
                        if($i < 41){
                            $sheet->setCellValue($letter_all[$i].($kk+2), $vvv);
                        }

                        $i++;

                    }
                 

                }

            // }

            // exit;

            // 第4行
            // $sheet->setCellValue('A1', '患者姓名');
            // $sheet->setCellValue('B4', 111);
            // $sheet->setCellValue('C4', '性别');
            // $sheet->setCellValue('D4', 222);
            // $sheet->setCellValue('E4', '出生日期');
            // $sheet->setCellValue('F4', 3333);
            // $sheet->setCellValue('G4', '或年龄');
            // $sheet->setCellValue('H4', 4444);
            // $sheet->setCellValue('I4', '岁');
        

            ob_end_clean();     //清除缓冲区,避免乱码

            header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // MIME 协议的扩展
            header('Content-Disposition: attachment;filename="' . iconv("utf-8", "GB2312", $filename) . '.xlsx');
            // 缓存控制
            header('Cache-Control:max-age=0');
            $write = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $write->save('php://output');
    }
    
}
