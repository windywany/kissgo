define('admin/js/menu', function(require, exports) {
    exports.main = function() {
        $('i.set-default').on('click', function() {
            var id = $(this).attr('data-value');
            window.location.href = KsgApp.acturl('admin/menu/setdefault', id);
            return false;
        });
    };

    exports.initAddForm = function(rules) {

        var validator = $('#menu_form').validate($.extend(true, {}, rules, {
            focusCleanup : true
        }));

        $('#menu_form').submit(function(e) {
            e.preventDefault();
            if (!$(this).valid()) {
                return false;
            }
            var sort = $('ol.sortable'), redirect = true;
            if (sort.length > 0) {
                redirect = false;
                var items = sort.nestedSortable('toArray', {
                    startDepthCount : 1
                }), len = items ? items.length : 0, id, pid, item;
                for ( var i = 0; i < len; i++) {
                    item = items[i];
                    id = item.item_id;
                    pid = !item.parent_id ? '0' : item.parent_id;
                    item = $('#menu-item-' + id + ' > div');
                    item.find('.parent').val(pid);
                    item.find('.sort').val(i);
                }
            }
            $(this).ajaxSubmit({
                dataType : 'json',
                beforeSerialize : function() {
                    $('body').blockit();
                },
                success : function(data) {
                    if (data.success) {
                        if (redirect) {
                            window.location.href = KsgApp.acturl('admin/menu');
                        } else {
                            alert('保存成功');
                            $('.item-title').css('color', '#000');
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
                    alert('出错啦!网络错误，请检查你的网络。');
                }
            });
            return false;
        });

    };
    exports.initEditForm = function(rules) {
        exports.initAddForm(rules);
        $('#autoc-id').select2({
            placeholder : '输入页面标题',
            multiple : true,
            ajax : {
                cache : true,
                url : KsgApp.acturl('nodes/select2'),
                data : function(term, page) {
                    return {
                        q : term,
                        p : page
                    };
                },
                results : function(data, page) {
                    return data;
                }
            }
        });
        $('#menuitem-list').nestedSortable({
            forcePlaceholderSize : true,
            handle : 'dt.menu-item-handle',
            helper : 'clone',
            items : 'li',
            maxLevels : 15,
            opacity : .6,
            placeholder : 'placeholder',
            revert : 250,
            tabSize : 25,
            tolerance : 'pointer',
            toleranceElement : '> div'
        });
        $('#add2menu').on('click', function(e) {
            e.preventDefault();
            var menu = $('#alias').val(), target = $('#target').val();
            var a = $('.frame:visible').attr('id');
            if (a == '_page_node') {
                var ids = $('#autoc-id').select2('val');
                if (ids.length == 0) {
                    alert('请选择页面!');
                    return;
                }
                var data = {
                    ids : ids.join(','),
                    target : target,
                    type : 'node',
                    menu : menu
                };
                addMenuItem(data);
            } else {
                var name = $('#n_item_name').val(), title = $('#n_title').val(), url = $('#n_url').val();
                url = $.trim(url);
                if (!/^https?:\/\/.+$/.test(url)) {
                    alert('请填写正确的链接地址.');
                    return;
                }
                name = $.trim(name);
                if (!name) {
                    alert('请填写名称');
                    return;
                }
                var data = {
                    name : name,
                    url : url,
                    title : title,
                    target : target,
                    type : 'url',
                    menu : menu
                };
                $('#n_item_name').val('');
                $('#n_title').val('');
                $('#n_url').val('');
                addMenuItem(data);
            }
            return false;
        });
        $('#menuitem-list').delegate('.item-del', 'click', function(e) {
            if (!confirm('你确定要移除这个菜单项?')) {
                return false;
            } else {
                e.preventDefault();
                window.location.href = KsgApp.acturl('admin/menu/delitem', $(this).attr('data-value')) + $('#menuid').val();
            }
        });
        $('#menuitem-list').delegate('.item-edit', 'click', function(e) {
            e.preventDefault();
            var me = $(this), wrap = me.parents('.menu-wrap');
            $.Dialog({
                width : 400,
                overlay : true,
                shadow : true,
                flat : true,
                icon : '<span class="icon-pencil"></span>',
                title : '编辑菜单项',
                content : '',
                padding : 10,
                onShow : function(_dialog) {
                    var content = '<div>';
                    content += '<label>菜单名称</label>';
                    content += '<div class="input-control text"><input type="text" id="ipt-menu-name"></div> ';
                    content += '<label>提示</label>';
                    content += '<div class="input-control text"><input type="text" id="ipt-menu-title"></div> ';
                    content += '<label class="for-url">URL</label>';
                    content += '<div class="input-control text for-url"><input type="text" id="ipt-menu-url"></div> ';
                    content += '<label>打开窗口</label>';
                    content += '<div class="input-control select"><select id="ipt-menu-target">';
                    content += '<option value="_blank">新窗口</option><option value="_self">原窗口</option></select></div>';
                    content += '<div class="form-actions">';
                    content += '<button class="button primary">确定</button>&nbsp;';
                    content += '<button class="button" type="button">取消</button> ';
                    content += '</div>';
                    content += '</div>';
                    content = $(content);
                    content.find('#ipt-menu-name').val(wrap.find('.item_name').val());
                    content.find('#ipt-menu-title').val(wrap.find('.title').val());
                    content.find('#ipt-menu-target').val(wrap.find('.target').val());

                    if (wrap.find('.url').length > 0) {
                        content.find('#ipt-menu-url').val(wrap.find('.url').val());
                    } else {
                        content.find('.for-url').remove();
                    }
                    content.find('button').eq(0).click(function() {
                        var item_name = $.trim($('#ipt-menu-name').val());
                        if (item_name.length == 0) {
                            alert('请填菜单项名称.');
                            return;
                        }
                        if ($('#ipt-menu-url').lenght > 0) {
                            var url = $('#ipt-menu-url').val();
                            if (!/^https?:\/\/.+/.test(url)) {
                                alert('请填写正确的URL.');
                                return;
                            }
                            wrap.find('.url').val(url);
                        }
                        wrap.find('.item_name').val(item_name);
                        wrap.find('.title').val($('#ipt-menu-title').val());
                        wrap.find('.target').val($('#ipt-menu-target').val());
                        wrap.find('.item-title').text($('#ipt-menu-name').val()).css('color', 'blue');
                        $('.window-overlay').click();
                    });
                    content.find('button').eq(1).click(function() {
                        $('.window-overlay').click();
                    });
                    $.Dialog.content(content);
                }
            });
        });
    };

    function addMenuItem(data) {
        $('body').blockit();
        $.ajax({
            url : KsgApp.acturl('admin/menu/additem'),
            dataType : 'text',
            type : 'POST',
            data : data,
            success : function(data) {
                if (typeof data == 'string') {
                    if (data.indexOf('error:') >= 0) {
                        alert(data.substring(6));
                    } else {
                        $('#menu-instructions').hide();
                        $('#menuitem-list').append($(data));
                    }
                } else if (!data.success) {
                    alert(data.msg);
                }
                $('body').unblockit();
            },
            error : function() {
                $('body').unblockit();
            }
        });
    }
});
