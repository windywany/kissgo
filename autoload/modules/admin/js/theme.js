define('admin/js/theme', function(require, exports) {
    require('jquery/ztree/core');
    exports.selectTemplate = function(theme, callback) {
        var setting = {
            treeId : 'tpls-tree',
            async : {
                enable : true,
                url : KsgApp.acturl('admin/theme/templates'),
                autoParam : [ "id" ],
                otherParam : {
                    theme : theme
                }
            }
        };
        $
                .Dialog({
                    width : 500,
                    height : 300,
                    shadow : true,
                    draggable : true,
                    icon : '<span class="icon-newspaper"></span>',
                    title : '选择模板',
                    content : '',
                    onShow : function(_dialog) {
                        var content = _dialog.children('.content').css({
                            position : 'absolute',
                            padding : 0,
                            left : 0,
                            top : 32,
                            right : 0,
                            bottom : 40,
                            overflow : 'auto'
                        });
                        var tpl_tree = $('<ul class="ztree" id="tpls-tree"></ul>').appendTo(content);
                        var footer = $('<div class="text-right" style="border-top:1px solid #999"><button class="buttom primary" style="margin-right:20px">确定</button><button  class="buttom warning" style="margin-right:20px">取消</button></div>')
                                .css({
                                    position : 'absolute',
                                    left : 0,
                                    right : 0,
                                    bottom : 0,
                                    height : 40,
                                    'line-height' : '40px'
                                });
                        _dialog.append(footer);
                        footer.find('button').eq(0).on('click', function() {
                            var treeObj = $.fn.zTree.getZTreeObj("tpls-tree");
                            var nodes = treeObj.getSelectedNodes(), tpl = null;
                            if (nodes.length == 1 && !nodes[0].isParent) {
                                tpl = nodes[0].id.substring(1);
                                if ($.isFunction(callback)) {
                                    callback(theme, tpl);
                                }
                                $('.window-overlay').click();
                            }
                        });
                        footer.find('button').eq(1).on('click', function() {
                            $('.window-overlay').click();
                        });
                        $.fn.zTree.init(tpl_tree, setting);
                    }
                });
    };

    exports.main = function() {
        $('button.reset-all-tpl').click(function() {
            var me = $(this), theme = me.attr('data-theme');
            if (confirm('你确定要重置所有模板吗?')) {
                $('body').blockit();
                $.ajax({
                    type : 'POST',
                    url : KsgApp.acturl('admin/theme/reset'),
                    data : {
                        theme : theme
                    },
                    success : function(data) {
                        if (data.success) {
                            setTimeout(function() {
                                window.location.reload(true);
                            }, 3000);
                        } else {
                            alert(data.msg);
                            $('body').unblockit();
                        }
                    }
                });
            }
        });

        $('button.use-this-tpl').click(function() {
            var me = $(this), theme = me.attr('data-theme');
            if (confirm('你确定要使用这个主题吗?')) {
                $('body').blockit();
                $.ajax({
                    type : 'POST',
                    url : KsgApp.acturl('admin/theme/usetheme'),
                    data : {
                        theme : theme
                    },
                    success : function(data) {
                        if (data.success) {
                            setTimeout(function() {
                                window.location.reload(true);
                            }, 3000);
                        } else {
                            alert(data.msg);
                            $('body').unblockit();
                        }
                    }
                });
            }
        });

        $('a.edit-tpl').click(function() {
            var me = $(this), theme = me.attr('data-theme'), type = me.attr('data-type');
            exports.selectTemplate(theme, function(theme, tpl) {
                $('body').blockit();
                $.ajax({
                    type : 'POST',
                    url : KsgApp.acturl('admin/theme/settpl'),
                    data : {
                        theme : theme,
                        type : type,
                        tpl : tpl
                    },
                    success : function(data) {
                        if (data.success) {
                            $('#' + theme + '-' + type + '-tpl').html(tpl);
                            alert('设置模板成功.');
                        } else {
                            alert(data.msg);
                        }
                        $('body').unblockit();
                    }
                });
            });
            return false;
        });
    };
});
