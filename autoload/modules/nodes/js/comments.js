define('nodes/js/comments', function(require, exports) {
    var grid = false;
    exports.main = function() {
        $('.datepicker').datepicker({
            format : 'yyyy-mm-dd'
        });
        $('.tabs a').click(function(){
            $('#status').val($(this).attr('data-status'));
            $('#comment_search_form').submit();
        });
        $('#comment_search_form').submit(function() {
            var search = $(this).serializeArray();
            grid.flexOptions({
                params : search
            });
            grid.flexReload();
            return false;
        });
        if (!grid) {

            var colModel = [ {
                display : 'ID',
                name : 'id',
                width : 50,
                sortable : true,
                align : 'center'
            }, {
                display : '主题',
                name : 'subject',
                width : 250
            }, {
                display : '相关页面',
                name : 'nid',
                width : 250,
                sortable : true
            }, {
                display : '回复评论',
                name : 'parent',
                width : 250,
                hide : true,
                sortable : false
            }, {
                display : '状态',
                name : 'status',
                width : 80,
                hide : true,
                sortable : true
            }, {
                display : '用户',
                name : 'user_id',
                width : 120,
                sortable : true
            }, {
                display : '作者',
                name : 'author',
                width : 120,
                sortable : true
            }, {
                display : '邮件',
                name : 'author_email',
                width : 200,
                sortable : false
            }, {
                display : '主页',
                name : 'author_url',
                width : 250,
                hide : true,
                sortable : false
            }, {
                display : 'IP',
                name : 'author_IP',
                hide : true,
                width : 120,
                sortable : false
            } ];
            grid = $('#comments_grid').flexigrid({
                url : KsgApp.acturl('nodes/comment/data'),
                colModel : colModel,
                onError : function(r, t, e) {
                    alert('cannot load data');
                },
                buttons : [ {
                    name : '编辑',
                    bclass : 'ico-add',
                    onpress : function() {
                       
                    }
                } ]
             
            });

        }
    };
});
