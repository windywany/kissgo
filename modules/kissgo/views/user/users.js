$(function() {
    $('#btn-selectall').click(function() {
        $('#user-list').uiTable('selectAll');
    });
    $('#menu-del-user').click(function() {
        var ids = $('#user-list').uiTable('selected');
        if(ids.length == 0) {
            alert('请选择要删除的用户');
            return false;
        }
        if(!confirm('确定删除所选的用户?')) {
            return false;
        }
        ids = ids.join(',');
        window.location.href = $(this).attr('href') + '&uids=' + ids;
        return false;
    });
    $('.menu-active-user').click(function() {
        var ids = $('#user-list').uiTable('selected');
        if(ids.length == 0) {
            alert('请选择要处理的用户');
            return false;
        }
        if(!confirm('确定处理所选的用户?')) {
            return false;
        }
        ids = ids.join(',');
        window.location.href = $(this).attr('href') + '&uids=' + ids;
        return false;
    });

    $('.add-to-group').click(function() {
        window.btnAddGroup = $(this);
        window.uid = btnAddGroup.attr('href').replace('#', '');
        $('#group-form input:checkbox').removeAttr('checked');
        $('#group-form').modal({
            backdrop : false,
            show : true
        });
    });

    $('#btn-done').click(function() {
        var gids = [], names = [], j = 0;
        $('#group-form input:checked').each(function(i, e) {
            gids[i] = $(e).val();
            names[i] = $(e).attr('rel');
            j ++;
        });
        if(gids.length) {
            btnAddGroup.find('i').removeClass('icon-remove-sign').addClass('icon-loading-14');
            $.eajax({
                url : './?Ctlr=AddToGroup&uid=' + uid + '&gids=' + gids,
                success : function(data) {
                    if(data.success) {
                        for(var i = 0; i < j; i++) {
                            var ge = $('<span class="label label-info mg-r5">' + names[i] + '<a class="delete-from-group" href="#' + uid + '/' + gids[i] + '"><i class="icon-remove-sign icon-white"></i></a></span>');
                            btnAddGroup.before(ge);
                        }
                    } else {
                        alert(data.msg);
                    }
                    btnAddGroup.find('i').removeClass('icon-loading-14').addClass('icon-remove-sign');
                }
            });
        }
        $('#group-form').modal('hide');
    });
    $('#btn-close-form').click(function() {
        $('#group-form input:checkbox').removeAttr('checked');
        $('#group-form').modal('hide');
    });
    $('.delete-from-group').live('click', function() {
        if(confirm('你确定将用户从组中删除?')) {
            var g = $(this), ids = g.attr('href').replace('#', '').split('/');
            var uid = ids[0], gid = ids[1];
            $(this).find('i').removeClass('icon-remove-sign').addClass('icon-loading-14');
            $.eajax({
                url : './?Ctlr=RemoveFromGroup&uid=' + uid + '&gid=' + gid,
                success : function(data) {
                    if(data.success) {
                        g.parents('span').fadeOut(500, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(data.msg);
                        g.find('i').removeClass('icon-loading-14').addClass('icon-remove-sign');
                    }
                }
            });
        }
    });
    $('.grant-user').click(function(){
    	var me = $(this),url = me.attr('href'),id=me.attr('id'),name = me.attr('data-content');
    	openTab(url,id,'授权【'+name+'】','icon_grant');
    	return false;
    });
}); 