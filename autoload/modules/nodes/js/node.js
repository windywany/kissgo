define('nodes/js/node', [ 'jquery/flexigrid', 'jquery/contextmenu' ], function(require, exports) {
    require('jquery/flexigrid');
    require('jquery/contextmenu');
    var grid = null;
    exports.main = function(desktop) {
        $('.datepicker').datepicker({
            format : 'yyyy-mm-dd'
        });
        $('.tabs a').click(function() {
            if ($(this).attr('data-status') == 'explorer') {
                $('#status').val('');
            } else {
                $('#status').val($(this).attr('data-status'));
            }
            $('#node_search_form').submit();
        });

        $('#node_search_form').submit(function() {
            var search = $(this).serializeArray();
            grid.flexOptions({
                params : search
            });
            grid.flexReload();
            return false;
        });
        var preProcessData = function(data) {
            var url = KsgApp.acturl('nodes', 'edit');
            for ( var i = 0; i < data.rows.length; i++) {
                var cell = data.rows[i].cell;
                data.rows[i].cell[1] = '<a href="' + url + cell[0] + '">' + cell[1] + '</a>';
                if (!cell[4]) {
                    data.rows[i].cell[4] = '未置顶';
                }
                if (cell[5]) {
                    data.rows[i].cell[5] = '<i class="icon-earth fg-green"></i>';
                } else {
                    data.rows[i].cell[5] = '<i class="icon-earth fg-orange"></i>';
                }
                if (cell[6]) {
                    data.rows[i].cell[6] = '<i class="icon-checkmark fg-green"></i>';
                } else {
                    data.rows[i].cell[6] = '<i class="icon-blocked fg-red"></i>';
                }
                if (cell[7]) {
                    var day = parseInt(cell[7] / 86400, 10);
                    _h = cell[7] % 86400;
                    var hour = parseInt(_h / 3600, 10), _m = _h % 3600;
                    var m = parseInt(_m / 60, 10), s = _m % 60;
                    data.rows[i].cell[7] = day + '天' + hour + '时' + m + '分' + s + '秒';
                } else {
                    data.rows[i].cell[7] = '未缓存';
                }
            }
            return data;
        };
        if (!grid) {
            var colModel = [ {
                display : 'ID',
                name : 'id',
                width : 80,
                sortable : true,
                align : 'center'
            }, {
                display : '标题',
                name : 'title',
                width : 300,
                sortable : true
            }, {
                display : '类型',
                name : 'content_type',
                width : 100,
                sortable : true
            }, {
                display : '所有者',
                name : 'uid',
                width : 120,
                sortable : true
            }, {
                display : '置顶到',
                name : 'ontop',
                width : 120,
                sortable : true
            }, {
                display : '状态',
                name : 'status',
                width : 60,
                sortable : true,
                align : 'center'
            }, {
                display : '可评论',
                name : 'commentable',
                width : 60,
                sortable : true,
                align : 'center'
            }, {
                display : '缓存时间',
                name : 'cache_time',
                width : 120,
                sortable : true,
                align : 'center'
            }, {
                display : '修改时间',
                name : 'update_time',
                width : 125,
                sortable : true
            }, {
                display : '修改用户',
                name : 'update_uid',
                width : 100,
                sortable : true,
                hide : true
            }, {
                display : '创建用户',
                name : 'create_uid',
                width : 100,
                sortable : true,
                hide : true
            }, {
                display : '创建时间',
                name : 'create_time',
                width : 125,
                sortable : true,
                hide : true
            }, {
                display : '作者',
                name : 'author',
                width : 100,
                sortable : true,
                hide : true
            }, {
                display : '来源',
                name : 'source',
                width : 100,
                sortable : true,
                hide : true
            }, {
                display : '插图',
                name : 'figure',
                width : 100,
                sortable : true,
                hide : true
            } ];
            grid = $('#nodes_grid').flexigrid({
                url : KsgApp.acturl('nodes', 'data'),
                colModel : colModel,
                onError : function(r, t, e) {
                    alert('cannot load data');
                },
                preProcess : preProcessData
            });
        }

    };
});
