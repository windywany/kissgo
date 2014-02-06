define('media/js/media', [ 'jquery/contextmenu' ], function(require, exports) {
    require('jquery/contextmenu');
    var grid = false;
    var initUploader = function() {
        plupload.addI18n({
            'Filename' : '文件名',
            'Status' : '进度',
            'Size' : '大小',
            'Add files' : '添加文件',
            'Stop current upload' : '停止上传',
            'Start uploading queue' : '上传',
            'Uploaded %d/%d files' : '已经上传 %d/%d 个文件',
            'N/A' : 'N/A',
            'Drag files here.' : '直接将文件拖动到这里',
            'File extension error.' : '文件扩展名错误.',
            'File size error.' : '文件尺寸错误.',
            'Init error.' : '初始化失败.',
            'HTTP Error.' : 'HTTP错误.',
            'Security error.' : '安全错误.',
            'Generic error.' : '一般错误.',
            'IO error.' : 'IO错误.',
            'Stop Upload' : '停止上传.',
            'Add Files' : '添加文件',
            'Start Upload' : '上传',
            '%d files queued' : '%d 个文件已排队'
        });
        $("#uploader").pluploadQueue({
            runtimes : 'gears,html5',
            url : KsgApp.acturl('media', 'plupload'),
            max_file_size : '100mb',
            max_file_count : 20,
            chunk_size : '1mb',
            unique_names : true,
            multiple_queues : true,
            rename : true,
            sortable : true,
            filters : [ {
                title : "图片",
                extensions : "jpg,gif,png,jpeg,bmp"
            }, {
                title : "归档",
                extensions : "zip,rar,7z,tar,gz,bz2"
            }, {
                title : "办公",
                extensions : "doc,docx,txt,ppt,pptx,xls,xlsx,pdf"
            }, {
                title : "多媒体",
                extensions : "mp3,avi,mp4,flv,swf"
            } ]
        });
        var uploader = $('#uploader').pluploadQueue();
        uploader.bind('UploadComplete', function(uper, fs) {
            if (fs.length == 0)
                return;
            $('#attach-info').show();
            var fn = $('.plupload_filelist_header > .plupload_file_name');
            fn.after('<div class="plupload_filt_name">文件名称</div><div class="plupload_filt_alt">文件描述</div>');
            $.each(fs, function(i, f) {
                if (f.status == plupload.DONE) {
                    var id = f.id, fdiv = $('#' + id + ' > .plupload_file_name'), nm = f.name.substring(0, f.name.indexOf('.'));
                    var editor = '<div class="plupload_filt_name">';
                    editor += '<input placeholder="描述" type="text" name="uploader_' + i + '_title" value="' + nm + '"/></div>';
                    editor += '<div class="plupload_filt_alt">';
                    editor += '<input type="hidden" name="uploader_' + i + '_size" value="' + f.size + '"/>';
                    editor += '<input placeholder="描述" type="text" name="uploader_' + i + '_alt" value="' + nm + '"/></div>';
                    fdiv.after($(editor));
                }
            });
        });
        $('#upload-from').submit(function() {
            $('body').blockit();
            $.ajax({
                url : KsgApp.acturl('media', 'upload'),
                method : 'POST',
                dataType : 'json',
                data : $(this).serializeArray(),
                success : function(data) {
                    if (data.success) {
                        $('#tab-media-grid').click();
                        grid.flexReload();
                        $('#uploader_filelist').empty();
                        uploader.splice(0, uploader.files.length);
                        $('#attach-info').hide();
                    } else {
                        KsgApp.errormsg('出错啦!' + data.msg);
                    }
                    $('body').unblockit();
                },
                error : function() {
                    $('body').unblockit();
                }
            });
            return false;
        });
    };
    exports.main = function() {
        $('.tabs li a').on('click', function(){
            if($(this).attr('id') == 'tab-media-grid'){
                $('.flexigrid').show();
            }else{
                $('.flexigrid').hide();
            }
        });
        var preProcessData = function(data) {
            var w = 0,h = 0;
            for ( var i = 0; i < data.rows.length; i++) {
                if(data.rows[i].cell[7] == 'image'){
                    w = 260;
                    h = 180;
                }else{
                    w = 46;
                    h = 60;
                }
                data.rows[i].cell[1] = '<a href="#"><img class="shadow" src="' + data.rows[i].cell[1] + '" width="'+w+'px" height="'+h+'px"/></a>';
                data.rows[i].cell[2] = '<a href="' + KsgApp.base + data.rows[i].cell[9] + '" target="_blank">' + data.rows[i].cell[2] + '</a>';
                data.rows[i].cell[3] += '<br/><strong>' + data.rows[i].cell[8] + '</strong>';
            }
            return data;
        };
        $('.datepicker').datepicker({
            format : 'yyyy-mm-dd'
        });
        initUploader();
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
                display : '缩略图',
                name : 'url',
                width : 270
            }, {
                display : '文件',
                name : 'filename',
                width : 300,
                sortable : true
            }, {
                display : '类型',
                name : 'mime_type',
                width : 100,
                sortable : true
            }, {
                display : '用户',
                name : 'gid',
                width : 150,
                sortable : true
            }, {
                display : '用户组',
                name : 'email',
                hide : true,
                width : 120,
                sortable : true
            }, {
                display : '日期',
                name : 'update_time',
                width : 120,
                sortable : true
            } ];
            grid = $('#medias_grid').flexigrid({
                url : KsgApp.acturl('media/data'),
                colModel : colModel,
                preProcess : preProcessData,
                onError : function(r, t, e) {
                    alert('cannot load data');
                }
            });

            var contextMenu = {
                selector : '#medias_grid tr',
                zIndex : 500,
                callback : function(key, options) {
                    var cmts = $('#medias_grid').selectedRows();
                    var ids = [], id = $(this).attr('data-id');
                   if (key == 'edit') {
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
                            url : KsgApp.acturl('media', key),
                            data : {
                                ids : ids.join(',')
                            },
                            success : function(data) {
                                if(data.success){
                                    $('body').unblockit();  
                                    grid.flexReload();
                                }else{
                                    alert(data.msg);
                                }                                                            
                            }
                        });
                    }
                },
                items : {
                    'edit' : {
                        name : '编辑',
                        icon : 'edit'
                    },
                    "sep1" : "---------",
                    'thumb' : {
                        name : '生成缩略图',
                        icon : 'thumb'
                    },
                    'watermark' : {
                        name : '添加水印',
                        icon : 'mark'
                    },
                    "sep2" : "---------",
                    'delete' : {
                        name : '删除',
                        icon : 'del'
                    }
                }
            };
            $.contextMenu(contextMenu);
        }
    };
});
