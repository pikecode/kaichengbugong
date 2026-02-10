<?php

namespace app\admin\controller\shop;

use app\admin\model\shop\Area;
use app\common\controller\Backend;
use app\admin\model\User;
use app\admin\model\shop\SearchLog;
use app\admin\model\shop\Goods;
use app\admin\model\shop\Order;
use app\admin\model\shop\OrderGoods;
use app\admin\model\shop\OrderAftersales;
use think\Db;

/**
 *
 */
class Report extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {
        }

        //今日订单和会员
        $totalOrderAmount = round(Order::where('orderstate', 'IN', [0, 3])->where('paystate', 1)->sum('payamount'), 2);
        $totalRefundAmount = round(OrderAftersales::where('status', 2)->where('type', '<>', 3)->sum('refund'), 2); //退款的

        $yesterdayOrderAmount = round(Order::where('orderstate', 'IN', [0, 3])->whereTime('paytime', 'yesterday')->sum('payamount'), 2);
        $yesterdayRefundAmount = round(OrderAftersales::where('status', 2)->where('type', '<>', 3)->whereTime('createtime', 'yesterday')->sum('refund'), 2);

        $todayOrderAmount = round(Order::where('orderstate', 'IN', [0, 3])->whereTime('paytime', 'today')->sum('payamount'), 2);
        $todayRefundAmount = round(OrderAftersales::where('status', 2)->where('type', '<>', 3)->whereTime('createtime', 'today')->sum('refund'), 2);

        $todayOrderRatio = $yesterdayOrderAmount > 0 ? ceil((($todayOrderAmount - $yesterdayOrderAmount) / $yesterdayOrderAmount) * 100) : ($todayOrderAmount > 0 ? 100 : 0);

        if ($this->request->isPost()) {

            $date = $this->request->post('date', '');

            list($orderSaleCategory, $orderSaleAmount, $orderSaleNums) = $this->getSaleOrderData($date);

            list($afterSaleCategory, $afterSaleAmount, $afterSaleNums) = $this->getSaleAfterData($date);

            $mapOrderList = $this->getMapSaleOrder($date);

            list($legendData, $seriesData) = $this->getCategoryOrder($date);

            $statistics = [
                'orderSaleCategory' => $orderSaleCategory,
                'orderSaleAmount'   => $orderSaleAmount,
                'orderSaleNums'     => $orderSaleNums,

                'afterSaleCategory' => $afterSaleCategory,
                'afterSaleAmount'   => $afterSaleAmount,
                'afterSaleNums'     => $afterSaleNums,

                'totalOrderAmount'     => $totalOrderAmount,
                'todayOrderAmount'     => $todayOrderAmount,
                'yesterdayOrderAmount' => $yesterdayOrderAmount,
                'todayOrderRatio'      => $todayOrderRatio,

                "totalRefundAmount"     => $totalRefundAmount,
                "yesterdayRefundAmount" => $yesterdayRefundAmount,
                "todayRefundAmount"     => $todayRefundAmount,
                "totalProfitAmount"     => round($totalOrderAmount - $totalRefundAmount, 2),

                "mapOrderList" => $mapOrderList,
                'legendData'   => $legendData,
                'seriesData'   => $seriesData
            ];
            $this->success('', '', $statistics);
        }


        $totalUser = User::count();
        $yesterdayUser = User::whereTime('jointime', 'yesterday')->count();
        $todayUser = User::whereTime('jointime', 'today')->count();
        $todayUserRatio = $yesterdayUser > 0 ? ceil((($todayUser - $yesterdayUser) / $yesterdayUser) * 100) : ($todayUser > 0 ? 100 : 0);


        //销售排行榜
        $todayPaidList = OrderGoods::alias('og')
            ->join('shop_order o', 'og.order_sn=o.order_sn')
            ->whereTime('o.paytime', 'today')
            ->where('o.orderstate', 'IN', [0, 3])
            ->where('o.paystate', 1)
            ->group('og.goods_id')
            ->field("COUNT(*) as nums,SUM(o.payamount) as amount,og.goods_id,og.title")
            ->order("amount", "desc")
            ->limit(10)
            ->select();
        foreach ($todayPaidList as $index => $item) {
            $item->percent = $totalOrderAmount > 0 ? round(($item['amount'] / $totalOrderAmount) * 100, 2) : 0;
        }

        $weekPaidTotal = Order::where('orderstate', 'IN', [0, 3])->where('paystate', 1)->whereTime('paytime', 'week')->sum("payamount");
        $weekPaidList = OrderGoods::alias('og')
            ->join('shop_order o', 'og.order_sn=o.order_sn')
            ->whereTime('o.paytime', 'week')
            ->where('o.orderstate', 'IN', [0, 3])
            ->where('o.paystate', 1)
            ->group('og.goods_id')
            ->field("COUNT(*) as nums,SUM(o.payamount) as amount,og.goods_id,og.title")
            ->order("amount", "desc")
            ->limit(10)
            ->select();

        foreach ($weekPaidList as $index => $item) {
            $item->percent = $weekPaidTotal > 0 ? round(($item['amount'] / $weekPaidTotal) * 100, 2) : 0;
        }

        $monthPaidTotal = Order::where('orderstate', 'IN', [0, 3])->where('paystate', 1)->whereTime('paytime', 'month')->sum("payamount");
        $monthPaidList = OrderGoods::alias('og')
            ->join('shop_order o', 'og.order_sn=o.order_sn')
            ->whereTime('o.paytime', 'month')
            ->where('o.orderstate', 'IN', [0, 3])
            ->where('o.paystate', 1)
            ->group('og.goods_id')
            ->field("COUNT(*) as nums,SUM(o.payamount) as amount,og.goods_id,og.title")
            ->order("amount", "desc")
            ->limit(10)
            ->select();

        foreach ($monthPaidList as $index => $item) {
            $item->percent = $monthPaidTotal > 0 ? round(($item['amount'] / $monthPaidTotal) * 100, 2) : 0;
        }

        $this->view->assign("todayPaidList", $todayPaidList);
        $this->view->assign("weekPaidList", $weekPaidList);
        $this->view->assign("monthPaidList", $monthPaidList);


        $hotSearchList = SearchLog::order('nums desc')->limit(10)->select();
        $hotGoodsList = Goods::order('sales desc')->field('id,title,sales')->limit(10)->select();
        $comGoodsList = Goods::order('comments desc')->field('id,title,comments')->limit(10)->select();


        $this->view->assign("hotSearchList", $hotSearchList);
        $this->view->assign("hotGoodsList", $hotGoodsList);
        $this->view->assign("comGoodsList", $comGoodsList);

        $this->view->assign("totalOrderAmount", $totalOrderAmount);
        $this->view->assign("yesterdayOrderAmount", $yesterdayOrderAmount);
        $this->view->assign("todayOrderAmount", $todayOrderAmount);
        $this->view->assign("todayOrderRatio", $todayOrderRatio);

        $this->view->assign("totalRefundAmount", $totalRefundAmount);
        $this->view->assign("yesterdayRefundAmount", $yesterdayRefundAmount);
        $this->view->assign("todayRefundAmount", $todayRefundAmount);
        $this->view->assign("totalProfitAmount", $totalOrderAmount - $totalRefundAmount);

        $this->view->assign("totalUser", $totalUser);
        $this->view->assign("yesterdayUser", $yesterdayUser);
        $this->view->assign("todayUser", $todayUser);
        $this->view->assign("todayUserRatio", $todayUserRatio);

        //订单数和订单额统计
        list($orderSaleCategory, $orderSaleAmount, $orderSaleNums) = $this->getSaleOrderData();
        $this->assignconfig('orderSaleCategory', $orderSaleCategory);
        $this->assignconfig('orderSaleAmount', $orderSaleAmount);
        $this->assignconfig('orderSaleNums', $orderSaleNums);

        //退款数和订单额统计
        list($afterSaleCategory, $afterSaleAmount, $afterSaleNums) = $this->getSaleAfterData();
        $this->assignconfig('afterSaleCategory', $afterSaleCategory);
        $this->assignconfig('afterSaleAmount', $afterSaleAmount);
        $this->assignconfig('afterSaleNums', $afterSaleNums);

        $mapOrderList = $this->getMapSaleOrder();
        $this->assignconfig('mapOrderList', $mapOrderList);

        list($legendData, $seriesData) = $this->getCategoryOrder();
        $this->assignconfig('legendData', $legendData);
        $this->assignconfig('seriesData', $seriesData);

        return $this->view->fetch();
    }


    //按地区展示订单明细
    public function areas()
    {
        $id = $this->request->param('id');
        $name = $this->request->param('name');
        if ($this->request->isAjax()) {
            if (!$id && !$name) {
                $this->error('参数错误');
            }
            $date = $this->request->param('date');
            if ($date) {
                list($start, $end) = explode(' - ', $date);
                $starttime = strtotime($start);
                $endtime = strtotime($end);
            } else {
                $starttime = \fast\Date::unixtime('day', 0, 'begin');
                $endtime = \fast\Date::unixtime('day', 0, 'end');
            }
            $row = Area::get($id);
            if (empty($row)) {
                $row = Area::where('name', 'like', "{$name}%")->find();
            }
            if (empty($row)) {
                $this->error('地区记录未找到');
            }
            if (!in_array($row->level, [1, 2])) {
                $this->error('不支持查询地区范围');
            }
            $where = [];
            $group = 'city_id';
            if ($row->level == 1) {
                $where['o.province_id'] = ['eq', $row->id];
            } else {
                $group = 'area_id';
                $where['o.city_id'] = ['eq', $row->id];
            }
            $sql = Order::field('o.id,o.createtime,SUM(o.payamount) as amount,SUM(og.nums) as goods_nums,o.city_id,o.province_id,o.area_id')
                ->where($where)
                ->where('o.createtime', 'between time', [$starttime, $endtime])
                ->where('o.orderstate', 'IN', [0, 3])
                ->where('o.paystate', 1)
                ->alias('o')
                ->join('shop_order_goods og', 'o.order_sn=og.order_sn', 'LEFT')
                ->group('o.id')
                ->fetchSql(true)
                ->select();

            $list = Db::query("SELECT d.*,count(*) nums,SUM(amount) AS amount,SUM(goods_nums) AS goods_nums FROM ({$sql}) AS d GROUP BY {$group}");

            $newList = [];
            $total = 0;
            foreach ($list as $res) {
                $newList[$res[$group]] = $res;
                $total = bcadd($total, $res['amount'], 2);
            }
            //组合
            $area = Area::field('id,name')->where('pid', $row->id)->select();
            $orderNums = $goodsNums = $orderMoney = $xAxis = [];
            $is_oblique = false;
            foreach ($area as $item) {
                if (isset($newList[$item['id']])) {
                    $orderNums[] = $newList[$item['id']]['nums'];
                    $goodsNums[] = $newList[$item['id']]['goods_nums'];
                    $orderMoney[] = $newList[$item['id']]['amount'];
                    $item->rate = $total == 0 ? 0 : round(($newList[$item['id']]['amount'] / $total) * 100, 2);
                } else {
                    $orderNums[] = 0;
                    $goodsNums[] = 0;
                    $orderMoney[] = 0;
                    $item->rate = 0;
                }
                $xAxis[] = $item['name'];
                if (mb_strlen($item['name']) > 4) {
                    $is_oblique = true;
                };
            }
            if (!$is_oblique) {
                $xAxis = [];
                foreach ($area as $item) {
                    $name = explode('市', $item['name']);
                    $xAxis[] = $name[0];
                }
            }
            $this->success('获取成功', '', [
                'xAxis'      => $xAxis,
                'is_oblique' => $is_oblique,
                'orderNums'  => $orderNums,
                'goodsNums'  => $goodsNums,
                'orderMoney' => $orderMoney,
                'rate'       => array_column($area, 'rate'),
                'name'       => $row['name']
            ]);
        }
        $this->assignconfig('area', $id);
        return $this->view->fetch();
    }

    /**
     * 获取订单销量销售额统计数据
     */
    protected function getSaleOrderData($date = '')
    {

        if ($date) {
            list($start, $end) = explode(' - ', $date);
            $starttime = strtotime($start);
            $endtime = strtotime($end);
        } else {
            $starttime = \fast\Date::unixtime('day', 0, 'begin');
            $endtime = \fast\Date::unixtime('day', 0, 'end');
        }
        $totalseconds = $endtime - $starttime;
        $format = '%Y-%m-%d';
        if ($totalseconds > 86400 * 30 * 2) {
            $format = '%Y-%m';
        } else {
            if ($totalseconds > 86400) {
                $format = '%Y-%m-%d';
            } else {
                $format = '%H:00';
            }
        }
        $column = [];
        $orderList = Order::where('paytime', 'between time', [$starttime, $endtime])
            ->where('orderstate', 'IN', [0, 3])
            ->where('paystate', 1)
            ->field('paytime, status, COUNT(*) AS nums, SUM(payamount) AS amount, MIN(paytime) AS min_paytime, MAX(paytime) AS max_paytime, 
            DATE_FORMAT(FROM_UNIXTIME(paytime), "' . $format . '") AS paydate')
            ->group('paydate')
            ->select();

        if ($totalseconds > 84600 * 30 * 2) {
            $starttime = strtotime('last month', $starttime);
            while (($starttime = strtotime('next month', $starttime)) <= $endtime) {
                $column[] = date('Y-m', $starttime);
            }
        } else {
            if ($totalseconds > 86400) {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("Y-m-d", $time);
                    $time += 86400;
                }
            } else {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("H:00", $time);
                    $time += 3600;
                }
            }
        }

        $orderSaleNums = $orderSaleAmount = array_fill_keys($column, 0);
        foreach ($orderList as $k => $v) {
            $orderSaleNums[$v['paydate']] = $v['nums'];
            $orderSaleAmount[$v['paydate']] = round($v['amount'], 2);
        }
        $orderSaleCategory = array_keys($orderSaleAmount);
        $orderSaleAmount = array_values($orderSaleAmount);
        $orderSaleNums = array_values($orderSaleNums);
        return [$orderSaleCategory, $orderSaleAmount, $orderSaleNums];
    }

    /**
     * 获取售后退款售额统计数据
     */
    protected function getSaleAfterData($date = '')
    {

        if ($date) {
            list($start, $end) = explode(' - ', $date);
            $starttime = strtotime($start);
            $endtime = strtotime($end);
        } else {
            $starttime = \fast\Date::unixtime('day', 0, 'begin');
            $endtime = \fast\Date::unixtime('day', 0, 'end');
        }
        $totalseconds = $endtime - $starttime;
        $format = '%Y-%m-%d';
        if ($totalseconds > 86400 * 30 * 2) {
            $format = '%Y-%m';
        } else {
            if ($totalseconds > 86400) {
                $format = '%Y-%m-%d';
            } else {
                $format = '%H:00';
            }
        }

        $afterList = OrderAftersales::where('createtime', 'between time', [$starttime, $endtime])
            ->where('status', 2)
            ->where('type', '<>', 3)
            ->field('createtime, status, COUNT(*) AS nums, SUM(refund) AS amount, MIN(createtime) AS min_paytime, MAX(createtime) AS max_paytime, 
            DATE_FORMAT(FROM_UNIXTIME(createtime), "' . $format . '") AS paydate')
            ->group('paydate')
            ->select();

        if ($totalseconds > 84600 * 30 * 2) {
            $starttime = strtotime('last month', $starttime);
            while (($starttime = strtotime('next month', $starttime)) <= $endtime) {
                $column[] = date('Y-m', $starttime);
            }
        } else {
            if ($totalseconds > 86400) {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("Y-m-d", $time);
                    $time += 86400;
                }
            } else {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("H:00", $time);
                    $time += 3600;
                }
            }
        }

        $afterSaleNums = $afterSaleAmount = array_fill_keys($column, 0);
        foreach ($afterList as $k => $v) {
            $afterSaleNums[$v['paydate']] = $v['nums'];
            $afterSaleAmount[$v['paydate']] = round($v['amount'], 2);
        }
        $afterSaleCategory = array_keys($afterSaleAmount);
        $afterSaleAmount = array_values($afterSaleAmount);
        $afterSaleNums = array_values($afterSaleNums);
        return [$afterSaleCategory, $afterSaleAmount, $afterSaleNums];
    }

    //订单按省分布
    protected function getMapSaleOrder($date = '')
    {
        if ($date) {
            list($start, $end) = explode(' - ', $date);
            $starttime = strtotime($start);
            $endtime = strtotime($end);
        } else {
            $starttime = \fast\Date::unixtime('day', 0, 'begin');
            $endtime = \fast\Date::unixtime('day', 0, 'end');
        }
        $sql = Order::alias('o')
            ->where('o.createtime', 'between time', [$starttime, $endtime])
            ->where('o.orderstate', 'IN', [0, 3])
            ->where('o.paystate', 1)
            ->join('shop_order_goods og', 'og.order_sn=o.order_sn', 'LEFT')
            ->field('o.createtime,SUM(o.payamount) as amount,SUM(og.nums) as goods_nums,o.province_id')
            ->group('o.id')
            ->fetchSql(true)
            ->select();

        $mapOrderList = Db::query("SELECT d.*,count(*) nums,SUM(amount) AS amount,SUM(goods_nums) AS goods_nums FROM ({$sql}) AS d GROUP BY province_id");

        $newList = [];
        $total = 0;
        foreach ($mapOrderList as $item) {
            $newList[$item['province_id']] = $item;
            $total = bcadd($total, $item['amount'], 2);
        }

        $area = Area::field('id,name')->where('pid', 0)->select();

        $list = [];
        foreach ($area as $item) {
            $name = iconv_substr($item['name'], 0, 2);
            if (in_array($name, ['内蒙', '黑龙'])) {
                $name = iconv_substr($item['name'], 0, 3);
            }
            if (isset($newList[$item['id']])) {
                $list[] = [
                    'name'   => $name,
                    'id'     => $item['id'],
                    'value'  => $newList[$item['id']]['nums'],
                    'amount' => $newList[$item['id']]['amount'],
                    'nums'   => $newList[$item['id']]['goods_nums'],
                    'rate'   => $total == 0 ? 0 : round(($newList[$item['id']]['amount'] / $total) * 100, 2)
                ];
            } else {
                $list[] = [
                    'name'   => $name,
                    'id'     => $item['id'],
                    'value'  => 0,
                    'amount' => 0,
                    'rate'   => 0
                ];
            }
        }
        $list[] = [
            'name'   => '南海诸岛',
            'id'     => 0,
            'value'  => 0,
            'amount' => 0,
            'rate'   => 0
        ];
        return $list;
    }

    //订单按分类分布
    protected function getCategoryOrder($date = '')
    {
        if ($date) {
            list($start, $end) = explode(' - ', $date);
            $starttime = strtotime($start);
            $endtime = strtotime($end);
        } else {
            $starttime = \fast\Date::unixtime('day', 0, 'begin');
            $endtime = \fast\Date::unixtime('day', 0, 'end');
        }
        $orderList = Order::where('o.createtime', 'between time', [$starttime, $endtime])
            ->where('o.orderstate', 'IN', [0, 3])
            ->where('o.paystate', 1)
            ->alias('o')
            ->join('shop_order_goods og', 'o.order_sn=og.order_sn', 'LEFT')
            ->join('shop_goods g', 'og.goods_id=g.id', 'LEFT')
            ->field('SUM(og.nums) nums,g.category_id,SUM(o.payamount) amount')
            ->group('g.category_id')
            ->select();
        $list = [];
        foreach ($orderList as $item) {
            $list[$item['category_id']] = $item;
        }
        $category = Db::name('shop_category')->field('id,name')->select();
        $legendData = [];
        $seriesData = [];
        foreach ($category as $item) {
            if (isset($list[$item['id']])) {
                $seriesData[] = [
                    'name'  => $item['name'],
                    'value' => $list[$item['id']]['nums']
                ];
                $legendData[] = $item['name'];
            }
        }
        //为空全部
        if (empty($legendData)) {
            $legendData = array_column($category, 'name');
            foreach ($legendData as $item) {
                $seriesData[] = [
                    'name'  => $item,
                    'value' => 0
                ];
            }
        }

        return [
            $legendData,
            $seriesData
        ];
    }
}
