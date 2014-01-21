define('nodes/js/comments', [ 'jquery/contextmenu', 'jquery/form' ], function(require, exports) {
    require('jquery/contextmenu');
    var grid = false;
    function replayComment(id, nid) {
        $.Dialog({
            width : 600,
            overlay : true,
            shadow : true,
            flat : true,
            icon : '<span class="ico-reply"></span>',
            title : '回复评论',
            content : '',
            padding : 10,
            onShow : function(_dialog) {
                var content = '<div><div class="grid fluid">';
                content += '<div class="row" style="margin-top:0">';
                content += '<div class="input-control text span12"><input type="text" id="ipt-subject" placeholder="主题"/></div>';
                content += '</div></div>';
                content += '<div id="replay-wrapper"><textarea id="replay-content" rows="5" class="quicktags-editor"></textarea></div>';
                content += '<div class="form-actions">';
                content += '<button class="button primary">确定</button>&nbsp;';
                content += '<button class="button" type="button">取消</button> ';
                content += '</div></div>';
                content = $(content);
                content.find('#replay-wrapper').quicktags('replay-content');
                content.find('button').eq(0).click(function() {
                    var subject = $('#ipt-subject').val();
                    var content = $('#replay-content').val().trim();
                    if (!content || content.length < 15) {
                        alert('不要吝惜你的文笔，你就写点东西吧!');
                        return;
                    }
                    $('body').blockit();
                    $.ajax({
                        url : KsgApp.acturl('nodes/comment/reply/' + id),
                        method : 'POST',
                        data : {
                            nid : nid,
                            subject : subject,
                            content : content
                        },
                        success : function(data) {
                            $('body').unblockit();                            
                            if (data.success) {
                                grid.flexReload();
                                $('.window-overlay').click();
                            } else {
                                alert(data.msg);
                            }
                        }
                    });
                });
                content.find('button').eq(1).click(function() {
                    $('.window-overlay').click();
                });
                $.Dialog.content(content);
            }
        });
    }
    function editComment(id) {
        require('jquery/form');
        $.Dialog({
            width : 600,
            overlay : true,
            shadow : true,
            flat : true,
            icon : '<span class="ico-reply"></span>',
            title : '回复评论',
            content : '',
            padding : 10,
            onShow : function(_dialog) {
                $('body').blockit();
                $.get(KsgApp.acturl('nodes/comment/edit', id), function(data) {
                    var content = $(data);
                    content.find('#edit-cmt-wrapper').quicktags('edit-cmt-txtr');
                    content.submit(function() {
                        var author = $('#ipt-author').val().trim();
                        var content = $('#edit-cmt-txtr').val().trim();
                        if (!author) {
                            alert('请填写作者!');
                            return;
                        }
                        if (!content || content.length < 15) {
                            alert('不要吝惜你的文笔，你就写点东西吧!');
                            return;
                        }
                        $('body').blockit();
                        $(this).ajaxSubmit({
                            dataType : 'json',
                            success : function(data) {
                                $('body').unblockit();
                                if (data.success) {
                                    grid.flexReload();
                                    $('.window-overlay').click();
                                } else {
                                    alert('出错啦!' + data.msg);
                                }                                
                            },
                            error : function(data) {
                                $('body').unblockit();
                            }
                        });
                        return false;
                    });
                    content.find('button').eq(1).click(function() {
                        $('.window-overlay').click();
                    });
                    $.Dialog.content(content);
                    $('body').unblockit();
                }, 'html');
            }
        });
    }
    exports.main = function() {
        $('.datepicker').datepicker({
            format : 'yyyy-mm-dd'
        });
        $('.tabs a').click(function() {
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
        var preProcessData = function(data) {
            for ( var i = 0; i < data.rows.length; i++) {
                // author - name,home,email,ip
                var cell = data.rows[i].cell;
                var subject = cell[2];
                data.rows[i].cell[1] = '<strong>' + cell[1] + '</strong>';
                if (cell[8]) {
                    data.rows[i].cell[1] += '<br/><a href="http://' + cell[8] + '" target="_blank">' + cell[8] + '</a>';
                }
                if (cell[7]) {
                    data.rows[i].cell[1] += '<br/><a href="mailto:' + cell[7] + '" target="_blank">' + cell[7] + '</a>';
                }
                if (cell[9]) {
                    data.rows[i].cell[1] += '<br/><a class="search_ip" href="#' + cell[9] + '">' + cell[9] + '</a>';
                }
                // comment - create time,replay to,subject,content
                data.rows[i].cell[2] = '<div class="cmt-submiton">提交于<a href="' + KsgApp.base + cell[10] + '#comment-' + cell[0] + '">' + cell[11] + '</a>';
                if (cell[13]) {
                    data.rows[i].cell[2] += ' | 回复给<a href="' + KsgApp.base + cell[10] + '#comment-' + cell[6] + '">' + cell[13] + '</a>';
                }
                data.rows[i].cell[2] += '</div>';
                if (subject) {
                    data.rows[i].cell[2] += '<div class="cmt-subject">' + subject + '</div>';
                }
                data.rows[i].cell[2] += '<div class="cmt-content">' + cell[14] + '</div>';
                data.rows[i].cell[3] = '<a href="' + KsgApp.base + cell[10] + '" target="_blank">' + cell[3] + '</a>';
            }
            return data;
        }
        if (!grid) {
            var colModel = [ {
                display : 'ID',
                name : 'id',
                width : 50,
                sortable : true,
                align : 'center'
            }, {
                display : '作者',
                name : 'author',
                width : 300,
                sortable : true
            }, {
                display : '评论',
                name : 'create_time',
                width : 450
            }, {
                display : '页面',
                name : 'nid',
                width : 250,
                sortable : true
            }, {
                display : '状态',
                name : 'status',
                width : 80,
                hide : true,
                sortable : true
            } ];

            grid = $('#comments_grid').flexigrid({
                url : KsgApp.acturl('nodes/comment/data'),
                colModel : colModel,
                onError : function(r, t, e) {
                    alert('cannot load data');
                },
                preProcess : preProcessData
            });
            var contextMenu = {
                selector : '#comments_grid tr',
                zIndex : 500,
                callback : function(key, options) {
                    var cmts = $('#comments_grid').selectedRows();
                    var ids = [], id = $(this).attr('data-id');
                    if (key == 'reply') {// open a popup window
                        var cmt = $('#row' + id).data('rowData');
                        if (!cmt) {
                            alert('没有选中要回复的评论.');
                            return;
                        }
                        replayComment(id, cmt.cell[15]);
                    } else if (key == 'edit') {
                        editComment(id);
                    } else {
                        $('body').blockit();
                        ids.push(id);
                        if (cmts) {
                            $(cmts).each(function(i, cmt) {
                                if (cmt[0].RowIdentifier != id) {
                                    ids.push(cmt[0].RowIdentifier);
                                }
                            });
                        }
                        $.ajax({
                            url : KsgApp.acturl('nodes/comment', key),
                            data : {
                                ids : ids.join(',')
                            },
                            success : function(data) {
                                $('body').unblockit();
                                grid.flexReload();
                            }
                        });
                    }
                },
                items : {
                    'edit' : {
                        name : '编辑',
                        icon : 'edit',
                        disabled : function() {
                            return $('#status').val() == 'trush';
                        }
                    },
                    'reply' : {
                        name : '回复',
                        icon : 'reply',
                        disabled : function() {
                            return $('#status').val() == 'trush';
                        }
                    },
                    "sep1" : "---------",
                    'approve' : {
                        name : '审核',
                        icon : 'approve',
                        disabled : function() {
                            return $('#status').val() != 'new';
                        }
                    },
                    'revoke' : {
                        name : '驳回',
                        icon : 'reject',
                        disabled : function() {
                            return $('#status').val() != 'pass';
                        }
                    },
                    "sep2" : "---------",
                    'spam' : {
                        name : '这是垃圾评论',
                        icon : 'spam',
                        disabled : function() {
                            return $('#status').val() == 'spam' || $('#status').val() == 'trush';
                        }
                    },
                    'unspam' : {
                        name : '这不是垃圾评论',
                        icon : 'unspam',
                        disabled : function() {
                            return $('#status').val() != 'spam' || $('#status').val() == 'trush';
                        }
                    },
                    "sep3" : "---------",
                    'trash' : {
                        name : '移到回收站',
                        icon : 'trash',
                        disabled : function() {
                            return $('#status').val() == 'trush';
                        }
                    },
                    'restore' : {
                        name : '还原',
                        icon : 'restore',
                        disabled : function() {
                            return $('#status').val() != 'trush';
                        }
                    },
                    "sep4" : "---------",
                    'delete' : {
                        name : '删除',
                        icon : 'del',
                        disabled : function() {
                            return $('#status').val() != 'trush';
                        }
                    }
                }
            };
            $.contextMenu(contextMenu);
        }
    };
});
