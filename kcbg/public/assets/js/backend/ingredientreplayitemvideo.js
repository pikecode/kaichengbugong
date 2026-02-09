define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ingredientreplayitemvideo/index' + location.search,
                    add_url: 'ingredientreplayitemvideo/add',
                    edit_url: 'ingredientreplayitemvideo/edit',
                    del_url: 'ingredientreplayitemvideo/del',
                    multi_url: 'ingredientreplayitemvideo/multi',
                    import_url: 'ingredientreplayitemvideo/import',
                    table: 'ingredientreplayitemvideo',
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
                        {field: 'ingredientreplayitem.title', title: __('Ingredientreplayitem_id'), operate: 'LIKE'},
                        {field: 'video_url', title: __('Video_url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'type', title: __('Type'), searchList: {"0":__('配料全程回放'),"1":__('精彩瞬间'),"2":__('有封条，不掉包')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'tag', title: __('Tag'), operate: 'LIKE', formatter: Table.api.formatter.flag},
                        {field: 'riqi', title: __('Riqi'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'shichang', title: __('Shichang'), operate: 'LIKE'},
                        {field: 'liangdian', title: __('Liangdian'), operate: 'LIKE'},
                        {field: 's_time', title: __('S_time'), operate: 'LIKE'},
                        {field: 'e_time', title: __('E_time'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            // 初始化selectpage组件
            $('.selectpage').selectPage({
                searchField: 'title',
                showField: 'title',
                keyField: 'id'
            });

            // 筛选按钮事件
            $(document).on('click', '.btn-filter', function() {
                var ingredientreplayitem_id = $('input[name="ingredientreplayitem_id"]').val();
                var type = $('select[name="type"]').val();

                var queryString = '';
                if (ingredientreplayitem_id) {
                    queryString += '&ingredientreplayitem_id=' + ingredientreplayitem_id;
                }
                if (type !== '') {
                    queryString += '&type=' + type;
                }

                table.bootstrapTable('refresh', {
                    url: 'ingredientreplayitemvideo/index' + location.search + queryString
                });
            });

            // 重置按钮事件
            $(document).on('click', '.btn-reset', function() {
                $('input[name="ingredientreplayitem_id"]').val('').trigger('change');
                $('select[name="type"]').val('');
                table.bootstrapTable('refresh', {
                    url: 'ingredientreplayitemvideo/index' + location.search
                });
            });
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
