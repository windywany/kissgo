define('media/js/media',function(require,exports){
    var grid = false;
    exports.main = function(){
        $('.datepicker').datepicker({
            format:'yyyy-mm-dd'
        });
        $('#media_search_form').submit(function() {
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
                display : '',
                name : 'url',
                width : 200,
                sortable : true
            }, {
                display : 'Display Name',
                name : 'display_name',
                width : 200,
                sortable : true
            }, {
                display : 'Group',
                name : 'gid',
                width : 180,
                sortable : true
            }, {
                display : 'Email',
                name : 'email',
                width : 250,
                sortable : true
            }, {
                display : 'Status',
                name : 'status',
                width : 80,
                sortable : true
            }, {
                display : 'Last Log-in IP',
                name : 'last_ip',
                hide : true,
                width : 180
            }, {
                display : 'Last Log-in Time',
                name : 'last_time',
                width : 120,
                sortable : true
            } ];
            grid = $('#medias_grid').flexigrid({
                url : KsgApp.acturl('media/data'),
                dataType : 'json',
                colModel : colModel,
                height : 260,
                sortname : "id",
                sortorder : "desc",
                usepager : true,
                useRp : true,
                rp : 15,
               // preProcess : preProcessData,
                showTableToggleBtn : false,
                onError : function(r, t, e) {
                    alert('cannot load data');
                },
                buttons : [ {
                    name : '新增',
                    bclass : 'ico-add',
                    onpress : function() {
                        window.location.href = KsgApp.acturl('admin/user/add');
                    }
                } ]
            });
        }
    
    };
});