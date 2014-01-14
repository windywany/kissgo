define('admin/js/users', function(require, exports) {  
    var grid = false;
    var preProcessData = function(data) {
        var edit_url = KsgApp.acturl('admin/user', 'edit');
        for ( var i = 0; i < data.rows.length; i++) {
            data.rows[i].cell[1] = '<a href="' + edit_url + data.rows[i].id + '">' + data.rows[i].cell[1] + '</a>';
        }
        return data;
    };
    exports.main = function() {
        $('#user_search_form').submit(function() {
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
                display : 'User Name',
                name : 'username',
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
            grid = $('#users_grid').flexigrid({
                url : KsgApp.acturl('admin/user/data'),                
                colModel : colModel,                
                preProcess : preProcessData,                
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
    exports.form = function(rules) {
        var validator = $('#user_form').validate($.extend(true, {}, rules, {
            focusCleanup : true
        }));

        $('#user_form').submit(function() {
            if (!$(this).valid()) {
                return false;
            }
            $(this).ajaxSubmit({
                dataType : 'json',
                beforeSerialize : function() {
                    $('body').blockit();
                },
                success : function(data) {
                    if (data.success) {
                        $('#userid').val(data.id);
                        KsgApp.successmsg('恭喜!用户信息保存成功.');
                        if (rules.rules.username.remote.indexOf('?') <= 0) {
                            $('#username').rules('add', {
                                remote : rules.rules.username.remote + '?id=' + data.id
                            });
                        }
                    } else if (data.formerr) {
                        validator.showErrors(data.formerr);
                    } else {
                        KsgApp.errormsg('出错啦!' + data.msg);
                    }
                    $('body').unblockit();
                },
                error : function(data) {
                    $('body').unblockit();
                }
            });
            return false;
        });
    };
});
