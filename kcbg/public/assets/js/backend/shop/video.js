define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shop/video/index' + location.search,
                    add_url: 'shop/video/add',
                    edit_url: 'shop/video/edit',
                    del_url: 'shop/video/del',
                    multi_url: 'shop/video/multi',
                    import_url: 'shop/video/import',
                    table: 'shop_video',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'shop_goods_id', title: __('Shop_goods_id')},
                        {field: 'goods.title', title: __('Goods.title'), operate: 'LIKE'},
                        {field: 'video_url', title: __('Video_url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'type', title: __('Type'), searchList: {"0":__('商品溯源'),"1":__('精彩瞬间'),"2":__('多视角回放')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'tag', title: __('Tag'), operate: 'LIKE', formatter: Table.api.formatter.flag},
                        {field: 's_time', title: __('视频开始时间'), operate: false},
                        {field: 'e_time', title: __('视频结束时间'), operate: false},
                        {field: 'riqi', title: __('Riqi'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'shichang', title: __('Shichang'), operate: 'LIKE'},
                        {field: 'liangdian', title: __('Liangdian'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        // {field: 'admin_id', title: __('Admin_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
