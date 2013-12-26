define('admin/js/groups', function(require, exports) {
    var grid = false;
    var preProcessData = function(data) {
        var edit_url = KsgApp.acturl('admin/usergroup', 'edit');
        for ( var i = 0; i < data.rows.length; i++) {
            data.rows[i].cell[1] = '<a href="' + edit_url + data.rows[i].id + '">' + data.rows[i].cell[1] + '</a>';
        }
        return data;
    };
    exports.main = function() {
        $('#group_search_form').submit(function() {
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
                display : 'Group Name',
                name : 'name',
                width : 200,
                sortable : true
            }, {
                display : 'Note',
                name : 'note',
                width : 300,
                sortable : true
            } ];
            grid = $('#groups_grid').flexigrid({
                url : KsgApp.acturl('admin/usergroup/data'),
                dataType : 'json',
                colModel : colModel,
                height : 260,
                sortname : "gid",
                sortorder : "desc",
                usepager : true,
                useRp : true,
                rp : 15,
                preProcess : preProcessData,
                showTableToggleBtn : false,
                onError : function(r, t, e) {
                    alert('cannot load data');
                },
                buttons : [ {
                    name : '新增',
                    bclass : 'ico-add',
                    onpress : function() {
                        window.location.href = KsgApp.acturl('admin/usergroup/add');
                    }
                } ]
            });
        }
    };
    exports.form = function(rules) {
        var validator = $('#group_form').validate($.extend(true, {}, rules, {
            focusCleanup : true
        }));

        $('#group_form').submit(function(e) {
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
                        $('#groupid').val(data.id);
                        KsgApp.successmsg('恭喜!用户组信息保存成功.');
                        if (rules.rules.name.remote.indexOf('?') <= 0) {
                            $('#name').rules('add', {
                                remote : rules.rules.name.remote + '?gid=' + data.id
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
