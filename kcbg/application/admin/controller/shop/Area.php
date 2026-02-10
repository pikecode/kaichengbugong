<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use fast\Http;
use think\Db;

/**
 * 地区管理
 *
 * @icon fa fa-circle-o
 */
class Area extends Backend
{

    /**
     * Area模型对象
     * @var \app\admin\model\shop\Area
     */
    protected $model = null;
    protected $searchFields = 'id,name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Area;
        $this->view->assign("statusList", $this->model->getStatusList());
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
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items(), 'initialized' => \app\admin\model\shop\Area::where('id', '>', 0)->find() ? true : false);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 导入
     */
    public function import()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        $total = \app\admin\model\shop\Area::where('id', '>', 0)->count();
        if ($total > 0) {
            $this->error("无需执行导入");
        }
        Db::startTrans();
        try {
            $this->importDefaultData();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success("导入成功");
    }

    /**
     * 导入默认地区数据
     */
    protected function importDefaultData()
    {
        $sqlFile = ADDON_PATH . 'shop' . DS . 'data' . DS . 'area.sql';
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $templine = '';
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*') {
                    continue;
                }

                $templine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    $templine = str_ireplace('__PREFIX__', config('database.prefix'), $templine);
                    $templine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $templine);
                    try {
                        Db::getPdo()->exec($templine);
                    } catch (\Exception $e) {
                        //$e->getMessage();
                    }
                    $templine = '';
                }
            }
        }
        return true;
    }

    /**
     * 更新地区表
     */
    public function refresh()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        $config = get_addon_config('shop');
        $amapKey = $config['amap_webapi_key'];
        $url = "http://restapi.amap.com/v3/config/district?key={$amapKey}&keywords=&subdistrict=3&extensions=base";
        $result = Http::get($url);
        $resultArr = json_decode($result, true);
        if (isset($resultArr['status']) && $resultArr['status'] == 1) {
            Db::startTrans();
            try {
                $newArr = $oldArr = [];
                array_multisort(array_column($resultArr['districts'][0]['districts'], 'adcode'), SORT_ASC, array_column($resultArr['districts'][0]['districts'], 'adcode'), SORT_ASC, $resultArr['districts'][0]['districts']);
                foreach ($resultArr['districts'][0]['districts'] as $i => $province) {
                    list($lng, $lat) = stripos($province['center'], ',') !== false ? explode(',', $province['center']) : ['', ''];
                    $level1 = [
                        'adcode'  => $province['adcode'],
                        'lng'     => $lng,
                        'lat'     => $lat,
                        'level'   => 1,
                        'label'   => '1_' . $province['adcode'] . '_' . $province['name'],
                        'name'    => $province['name'],
                        'pid'     => 0,
                        'sublist' => []
                    ];

                    array_multisort(array_column($province['districts'], 'adcode'), SORT_ASC, array_column($province['districts'], 'adcode'), SORT_ASC, $province['districts']);
                    foreach ($province['districts'] as $j => $city) {
                        list($lng, $lat) = stripos($city['center'], ',') !== false ? explode(',', $city['center']) : ['', ''];
                        $level2 = [
                            'adcode'  => $city['adcode'],
                            'lng'     => $lng,
                            'lat'     => $lat,
                            'level'   => 2,
                            'label'   => '2_' . $city['adcode'] . '_' . $city['name'],
                            'name'    => $city['name'],
                            'sublist' => []
                        ];
                        array_multisort(array_column($city['districts'], 'adcode'), SORT_ASC, array_column($city['districts'], 'adcode'), SORT_ASC, $city['districts']);
                        foreach ($city['districts'] as $k => $area) {
                            list($lng, $lat) = stripos($area['center'], ',') !== false ? explode(',', $area['center']) : ['', ''];
                            $level3 = [
                                'adcode' => $area['adcode'],
                                'lng'    => $lng,
                                'lat'    => $lat,
                                'level'  => 3,
                                'label'  => '3_' . $area['adcode'] . '_' . $area['name'],
                                'name'   => $area['name'],
                            ];
                            $level2['sublist'][$k] = $level3;
                        }
                        $level1['sublist'][$j] = $level2;
                    }
                    $newArr[$i] = $level1;
                }

                $areaList = \think\Db::name('shop_area')->select();
                foreach ($areaList as $index => $item) {
                    $label = $item['level'] . '_' . $item['adcode'] . '_' . $item['name'];
                    $item['label'] = $label;
                    $oldArr[$label] = $item;
                }
                $this->areaUpdate($newArr, $oldArr);
                $ids = [];
                foreach ($oldArr as $index => $item) {
                    if (!isset($item['keep'])) {
                        $ids[] = $item['id'];
                    }
                }
                if ($ids) {
                    //旧的地区需要做删除处理
                    \app\admin\model\shop\Area::where(['id' => ['in', $ids]])->where('')->delete();
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error("更新失败:" . $e->getMessage());
            }
            $this->success("更新成功");
        } else {
            if (isset($resultArr['infocode']) && $resultArr['infocode'] == 10001) {
                $this->error('请检查配置中高德地图Web服务API密钥是否正确');
            } else {
                $this->error($resultArr['info'] ?? '发生错误:' . $result);
            }
        }
        return;
    }

    protected function areaUpdate($newArea, &$oldArea, $parent = 0)
    {
        static $pinyin;
        if (!$pinyin) {
            $pinyin = new \Overtrue\Pinyin\Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
        }
        $pid = $parent;
        $allow = array_flip(['pid', 'level', 'name', 'adcode', 'zipcode', 'lng', 'lat']);
        foreach ($newArea as $k => $v) {
            $label = $v['label'];
            $hasChild = isset($v['sublist']) && $v['sublist'] ? true : false;
            $data = array_intersect_key($v, $allow);
            $data['pid'] = $pid;
            $data['status'] = 'normal';
            if (!isset($oldArea[$label])) {
                $data['pinyin'] = $pinyin->permalink($data['name'], '');
                $data['py'] = $pinyin->abbr($data['name']);
                $area = \app\admin\model\shop\Area::create($data, true);
            } else {
                $area = $oldArea[$label];
                if (array_diff_key($data, $area)) {
                    $data['pinyin'] = $pinyin->permalink($data['name'], '');
                    $data['py'] = $pinyin->abbr($data['name']);
                    //更新旧菜单
                    \app\admin\model\shop\Area::update($data, ['id' => $area['id']]);
                }
                $oldArea[$label]['keep'] = true;
            }
            if ($hasChild) {
                $this->areaUpdate($v['sublist'], $oldArea, $area['id']);
            }
        }
    }
}
