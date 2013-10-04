(function () {    
    var URL = window.UEDITOR_HOME_URL;
   
    window.UEDITOR_CONFIG = {
        UEDITOR_HOME_URL : URL
        //图片上传配置区
        ,imageUrl:Kissgo.murl('admin','media/imageup/')
        ,imagePath:Kissgo.WEBSITE
      //涂鸦图片配置区
        ,scrawlUrl:Kissgo.murl('admin','media/scrawup/')
        ,scrawlPath:Kissgo.WEBSITE
        //附件上传配置区
        ,fileUrl:Kissgo.murl('admin','media/fileup/')      
        ,filePath:Kissgo.WEBSITE
        //图片在线管理配置区
        ,imageManagerUrl:Kissgo.murl('admin','media/images/')
        ,imageManagerPath:Kissgo.WEBSITE        
        //word转存配置区
        ,wordImageUrl:Kissgo.murl('admin','media/imageup/')
        ,wordImagePath:Kissgo.WEBSITE
        //获取视频数据的地址
        ,getMovieUrl:URL+"php/getMovie.php"                   //视频数据获取地址        
        ,toolbars:[['fullscreen', 'source', '|', 'ClearDoc','Undo', 'Redo','SelectAll', '|',
                   'Bold', 'Italic', 'Underline', 'StrikeThrough', 'Superscript', 'Subscript', 'RemoveFormat', 'FormatMatch','AutoTypeSet', '|',
                   'BlockQuote', '|', 'PastePlain', '|', 'ForeColor', 'BackColor', 'InsertOrderedList', 'InsertUnorderedList',  '|', 'CustomStyle',
                   'Paragraph', '|','RowSpacingTop', 'RowSpacingBottom','LineHeight', '|','FontFamily', 'FontSize', '|', '', 'Indent', '|',
                   'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyJustify', '|',
                   'Link', 'Unlink', 'Anchor', '|', 'ImageNone', 'ImageLeft', 'ImageRight', 'ImageCenter', '|','scrawl', 'InsertImage', 'Emotion', 'InsertVideo', 'Map', 'GMap','attachment','insertcode', '|',
                   'Horizontal', 'Date', 'Time', 'Spechars','WordImage', '|',
                   'InsertTable', 'DeleteTable', 'InsertParagraphBeforeTable', 'InsertRow', 'DeleteRow', 'InsertCol', 'DeleteCol', 'MergeCells', 'MergeRight', 'MergeDown', 'SplittoCells', 'SplittoRows', 'SplittoCols', '|',
                    'SearchReplace','pagebreak']
           ]
        ,labelMap:{
            'anchor':'', 'undo':''
        }        
        ,webAppKey:""
        ,charset:"utf-8"
        ,initialContent:''
        ,initialFrameWidth:'100%'  
        ,initialFrameHeight:350
        ,zIndex : 1031
        ,pageBreakTag:'_ksg_page_break_tag_'
        ,autotypeset:{
         mergeEmptyline : true,         
         removeClass : true,           
        removeEmptyline : false,      
              textAlign : "left" ,           
              imageBlockLine : 'center',      
              pasteFilter : false,            
              clearFontSize : false,          
              clearFontFamily : false,        
              removeEmptyNode : false ,       
              removeTagNames : {script:1,style:1},
              indent : false,                 
              indentValue : '2em'             
          }
    };
})();
