{extends file=$ksg_admincp_layout}

{block name="title"}{'Attachments'|ts}{/block}
{block name="admincp_css_block"}
<link href="{'jquery/ui/smoothness/jquery-ui.css'|static}" rel="stylesheet"/>
<link href="{'jquery/plugins/plupload/css/jquery.plupload.queue.css'|static}" rel="stylesheet"/>
<style type="text/css">
    #uploader {
    	width: 100%;    	
    }    
    .plupload_wrapper {
    	font: 12px Verdana, sans-serif;
    }
    .plupload_scroll .plupload_filelist {
    	background:none;
    	min-height: 300px;
        overflow-y:auto;
        border-color: #AAAAAA;
        border-style: dashed;
        border-width: 0 1px;
    }
    .plupload_filelist_header .plupload_file_name {
    	width: 205px;
    }
    #attach-info {
    	clear: both;
    	display: none;
    	text-align: center;
    }
    .plupload_filt_alt,.plupload_filt_name {
    	float: left;
    	margin-left: 10px;
    	width: 200px;
    }
    .plupload_filt_alt{
	    width: 300px;
    }
    .plupload_filt_alt input ,.plupload_filt_name input{
    	margin-bottom: 0;
    	font-size: 12px;
        width:90%;
    }    
    .plupload_header{
	    background:none;
    }    
    .plupload_header_content {    
	     background:none;
        color:#000;
        padding-left:10px;
    }
    .plupload_filelist_header, .plupload_filelist_footer{
	    background-color:#F5F5F5;
        border:none;
    }
    .plupload_filelist li{
    	background:none;
    }
</style>
{/block}
{block name="breadcrumb"}
	<li><a href="{$_CUR_URL}">{'Attachments'|ts}</a><span class="divider">/</span></li>
	<li>Upload</li>
{/block}

{block name="admincp_body"}
    <ul class="nav nav-tabs">
				<li>&nbsp;&nbsp;</li>	
			    <li><a href="{$_CUR_URL}"><i class="icon-picture"></i>文件列表</a></li>
			    <li class="active"><a href="#"><i class="icon-upload"></i>上传</a></li>			    
		    </ul>
		    <form id="upload-from" method="post" action="{$_CUR_URL}/upload">
			    <div id="uploader"></div>
				<div id="attach-info">
					<button type="submit" class="btn btn-success"><i class="icon-ok"></i>完成上传</button>
				</div>
    </form>

{/block}
{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'jquery/jquery-ui.js'|static}"></script>
<script type="text/javascript" src="{'jquery/plugins/plupload/plupload.js'|static}"></script>
<script type="text/javascript" src="{'jquery/plugins/plupload/jquery.plupload.queue.js'|static}"></script>
<script type="text/javascript">
$(function() {
	// Spanish
	plupload.addI18n({
		'Select files' : '文件上传',
		'Add files to the upload queue and click the start button.' : '请点击“添加文件”按钮选择文件或将文件拖到队列.然后点击“上传”按钮。',
		'Filename' : '文件名',
		'Status' : '进度',
		'Size' : '大小',
		'Add files' : '添加文件',
		'Stop current upload' : '停止上传',
		'Start uploading queue' : '上传',
		'Uploaded %d/%d files': '已经上传 %d/%d 个文件',
		'N/A' : 'N/A',
		'Drag files here.' : '直接将文件拖动到这里',
		'File extension error.': '文件扩展名错误.',
		'File size error.': '文件尺寸错误.',
		'Init error.': '初始化失败.',
		'HTTP Error.': 'HTTP错误.',
		'Security error.': '安全错误.',
		'Generic error.': '一般错误.',
		'IO error.': 'IO错误.',
		'Stop Upload': '停止上传.',
		'Add Files': '添加文件',
		'Start upload': '上传',
		'%d files queued': '%d 个文件已排队'
	});
	
	$("#uploader").pluploadQueue({		
		runtimes : 'html5,flash,silverlight,html4',		
		url : '{$_CUR_URL}/plupload',
		max_file_size : '100mb',
		max_file_count: 100, // user can add no more then 20 files at a time
		chunk_size : '1mb',
		unique_names : true,
		multiple_queues : true,
		// Rename files by clicking on their titles
		rename: true,
		// Sort files
		sortable: true,
		// Specify what files to browse for
		filters : [
			{ title : "图片", extensions : "jpg,gif,png,jpeg,bmp" },
			{ title : "归档", extensions : "zip,rar,7z,tar,gz,bz2" },
			{ title : "办公", extensions : "doc,docx,txt,ppt,pptx,xls,xlsx,pdf" },
			{ title : "多媒体", extensions : "mp3,avi,mp4,flv,swf" }
		],
		// Flash settings
		flash_swf_url : "{'jquery/plugins/plupload/plupload.flash.swf'|static}",
		silverlight_xap_url : "{'jquery/plugins/plupload/plupload.silverlight.xap'|static}"
	});
	var uploader = $('#uploader').pluploadQueue();	
	uploader.bind('UploadComplete',function(uper,fs){
		if(fs.length==0) return;		
		$('#attach-info').show();
		var fn = $('.plupload_filelist_header > .plupload_file_name');
		fn.after('<div class="plupload_filt_name">文件名称</div><div class="plupload_filt_alt">文件描述</div>');
		$.each(fs,function(i,f){
			if(f.status == plupload.DONE){
				var id = f.id,fdiv = $('#'+id + ' > .plupload_file_name'),nm = f.name.substring(0,f.name.indexOf('.'));				
				fdiv.after($('<div class="plupload_filt_name"><input placeholder="描述" type="text" name="uploader_'+i+'_title" value="'+nm+'"/></div><div class="plupload_filt_alt"><input type="hidden" name="uploader_'+i+'_size" value="'+f.size+'"/><input placeholder="描述" type="text" name="uploader_'+i+'_alt" value="'+nm+'"/></div>'));
			}
		});
	});
	$('#upload-from').submit(function(){
		showWaitMask('因为要生成缩略图和添加水印，这可能需要几分钟时间，请耐心等待...');
	});
});

</script>
{/block}