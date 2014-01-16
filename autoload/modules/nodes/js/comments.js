define('nodes/js/comments', 'jquery/contextmenu', function(require, exports) {
    require('jquery/contextmenu');
    var grid = false;
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
                zIndex: 500,
                callback : function(key, options) {
                    var m = "clicked: " + key;
                    alert($(this).attr('data-id'));
                },
                items : {
                    'reply' : {
                        name : '回复',
                        disabled:function(){
                            return  $('#status').val() == 'trush' ;
                        }
                    },
                    "sep1": "---------",
                    'approve' : {
                        name : '审核',
                        disabled:function(){
                            return $('#status').val() != 'new';
                        }
                    },
                    'revoke' : {
                        name : '驳回',
                        disabled:function(){
                            return $('#status').val() != 'pass';
                        }
                    },
                    "sep2": "---------",
                    'spam' : {
                        name : '这是垃圾评论',
                        disabled:function(){
                            return $('#status').val() == 'spam' ||  $('#status').val() == 'trush' ;
                        }
                    },
                    'unspam' : {
                        name : '这不是垃圾评论',
                        disabled:function(){
                            return $('#status').val() != 'spam' ||  $('#status').val() == 'trush' ;
                        }
                    },
                    "sep3": "---------",
                    'trush' : {
                        name : '移到回收站',
                        disabled:function(){
                            return $('#status').val() == 'trush';
                        }
                    },
                    'untrush' : {
                        name : '还原',
                        disabled:function(){
                            return $('#status').val() != 'trush';
                        }
                    },
                    "sep4": "---------",
                    'delete' : {
                        name : '删除',
                        disabled:function(){
                            return $('#status').val() != 'deleted';
                        }
                    }
                }
            };
            $.contextMenu(contextMenu);
        }
    };
});
