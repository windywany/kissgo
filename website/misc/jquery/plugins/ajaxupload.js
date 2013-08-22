(function($){
	var ajaxuploadIndex = 0;
	$.fn.ajaxupload = function(options){
		var tpl = '<div class="ajaxupload ajaxupload-new"><input type="hidden" class="fileupload-id"/><input type="hidden" class="fileupload-path"/>\
                    	<div class="input-append">\
                        	<div class="uneditable-input w300">\
                            	<i class="icon-file"></i>\
								<input type="text" class="fileupload-name"/>\
								<span class="fileupload-preview"></span>\
                            </div><a class="btn btn-browser">浏览...</a><a class="btn file-upload">上传</a>\
                        </div>\
                   </div>'; 
			
		var opts = $.extend({},options || {},{
			multi_selection:false,
			runtimes : 'html5,flash,html4',					
			max_file_count: 1,
			chunk_size : '256kb',
			url : Kissgo.murl('admin','media/plupload/1'),
			flash_swf_url : Kissgo.misc('jquery/plugins/plupload/plupload.flash.swf')
		});
		if(!opts.max_file_size){
			opts.max_file_size = '100mb';
		}
		$(this).each(function(i,elt){
			var $elt = $(elt),upload_done = false,id = 'ajaxupload-' + (ajaxuploadIndex++),wrapper = $(tpl),btnup=wrapper.find('.file-upload'),name = opts.param || 'file';			
			wrapper.find('.btn-browser').attr('id',id);
			wrapper.find('.fileupload-id').attr('name',name+'[][id]');
			wrapper.find('.fileupload-path').attr('name',name+'[][path]');
			wrapper.find('.fileupload-name').attr('name',name+'[][name]');
			wrapper.appendTo($elt);	
			opts.browse_button = id;	
			opts.multipart_params = opts.multipart_params || {};
			var uploader = new plupload.Uploader(opts),btnb = $('#'+id);			
			uploader.init();
			btnup.click(function(){
				btnup.attr('disabled','disabled');	
				uploader.start();
				return false;
			});
			uploader.bind('BeforeUpload', function(up, file) {
				uploader.settings.multipart_params.filename = wrapper.find('.fileupload-name').val();
				uploader.settings.multipart_params.filesize = file.size;
			});
			uploader.bind('FilesAdded', function(up, files) {
				var fs = up.files;				
				upload_done = false;		
				for(var j in fs){
					if(fs[j].id != files[0].id){
						up.removeFile(fs[j]);
					}
				}				
				wrapper.removeClass('ajaxupload-new');
				btnup.removeAttr('disabled','disabled').html('上传');
				wrapper.find('.fileupload-name').val(files[0].name);
				wrapper.find('.fileupload-preview').html(' (' + plupload.formatSize(files[0].size) + ')');
			});
			
			uploader.bind('UploadProgress', function(up, file) {
				if(upload_done){
					return;
				}
				btnup.html(file.percent+'%');				
			});
			
			uploader.bind('FileUploaded',function(up,file,resp){
				upload_done = true;	
				btnb.show();
				if(file.status == plupload.DONE){
					var result = Kissgo.parseJson(resp.response);
					var rst = result.result;
					if(rst){
						btnup.html('DONE!');
						wrapper.find('.fileupload-id').val(rst.id);
						wrapper.find('.fileupload-path').val(rst.url);
						if($.isFunction(opts.onUpladed)){
							opts.onUpladed.call(wrapper,rst,wrapper);
						}
					}else{
						btnup.html('failed');
					}
				}else{
					btnup.html('failed');
				}				
			});
			
			uploader.bind('UploadFile',function(up,file){
				btnb.hide();
				btnup.html('0%');
			});	
			
			uploader.bind('Error',function(up,error){
				upload_done = true;	
				btnup.html('failed');
				btnb.show();
				alert(error.message);		
			});
		});
		return $(this);
	};	
})(jQuery);