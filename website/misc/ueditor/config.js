(function () {    
    var URL;    
    var tmp = location.protocol.indexOf("file")==-1 ? location.pathname : location.href;
    URL = window.UEDITOR_HOME_URL||tmp.substr(0,tmp.lastIndexOf("\/")+1).replace("_examples/","").replace("website/","");//这里你可以配置成ueditor目录在您网站的相对路径或者绝对路径（指以http开头的绝对路径）
   
    window.UEDITOR_CONFIG = {

        
        UEDITOR_HOME_URL : URL
        //图片上传配置区
        ,imageUrl:URL+"php/imageUp.php"             //图片上传提交地址
        ,imagePath:URL + "php/"                     //图片修正地址，引用了fixedImagePath,如有特殊需求，可自行配置
       //,imageFieldName:"upfile"                   //图片数据的key,若此处修改，需要在后台对应文件修改对应参数
        //,compressSide:0                            //等比压缩的基准，确定maxImageSideLength参数的参照对象。0为按照最长边，1为按照宽度，2为按照高度
        //,maxImageSideLength:900                    //上传图片最大允许的边长，超过会自动等比缩放,不缩放就设置一个比较大的值，更多设置在image.html中

        //涂鸦图片配置区
        ,scrawlUrl:URL+"php/scrawlUp.php"           //涂鸦上传地址
        ,scrawlPath:URL+"php/"                            //图片修正地址，同imagePath

        //附件上传配置区
        ,fileUrl:URL+"php/fileUp.php"               //附件上传提交地址
        ,filePath:URL + "php/"                   //附件修正地址，同imagePath
        //,fileFieldName:"upfile"                    //附件提交的表单名，若此处修改，需要在后台对应文件修改对应参数

        //图片在线管理配置区
        ,imageManagerUrl:URL + "php/imageManager.php"       //图片在线管理的处理地址
        ,imageManagerPath:URL + "php/"                                    //图片修正地址，同imagePath

        //屏幕截图配置区
        ,snapscreenHost: '127.0.0.1'                                  //屏幕截图的server端文件所在的网站地址或者ip，请不要加http://
        ,snapscreenServerUrl: URL +"php/imageUp.php" //屏幕截图的server端保存程序，UEditor的范例代码为“URL +"server/upload/php/snapImgUp.php"”
        ,snapscreenPath: URL + "php/"
        //,snapscreenServerPort: 80                                    //屏幕截图的server端端口
        //,snapscreenImgAlign: 'center'                                //截图的图片默认的排版方式

        //word转存配置区
        ,wordImageUrl:URL + "php/imageUp.php"             //word转存提交地址
        ,wordImagePath:URL + "php/"                       //
        //,wordImageFieldName:"upfile"                     //word转存表单名若此处修改，需要在后台对应文件修改对应参数

        //获取视频数据的地址
        ,getMovieUrl:URL+"php/getMovie.php"                   //视频数据获取地址

        
        ,toolbars:[['fullscreen', 'source', '|', 'ClearDoc','Undo', 'Redo','SelectAll', '|',
                   'Bold', 'Italic', 'Underline', 'StrikeThrough', 'Superscript', 'Subscript', 'RemoveFormat', 'FormatMatch','AutoTypeSet', '|',
                   'BlockQuote', '|', 'PastePlain', '|', 'ForeColor', 'BackColor', 'InsertOrderedList', 'InsertUnorderedList',  '|', 'CustomStyle',
                   'Paragraph', '|','RowSpacingTop', 'RowSpacingBottom','LineHeight', '|','FontFamily', 'FontSize', '|', '', 'Indent', '|',
                   'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyJustify', '|',
                   'Link', 'Unlink', 'Anchor', '|', 'ImageNone', 'ImageLeft', 'ImageRight', 'ImageCenter', '|', 'InsertImage', 'Emotion', 'InsertVideo', 'Map', 'GMap','highlightcode', '|',
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
