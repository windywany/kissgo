<?php /* Smarty version Smarty-3.1.12, created on 2012-11-21 17:25:35
         compiled from "C:\java sources\kissgo\templates\admincp\foot.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1915350ac9e0f018ae0-69822977%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '90032037df8a81aa8c68a6fb3f385639207d9920' => 
    array (
      0 => 'C:\\java sources\\kissgo\\templates\\admincp\\foot.tpl',
      1 => 1353488219,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1915350ac9e0f018ae0-69822977',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_SITE_URL' => 0,
    '_foot_toolbar_btns' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50ac9e0f02f409_87805816',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ac9e0f02f409_87805816')) {function content_50ac9e0f02f409_87805816($_smarty_tpl) {?><div id="sideTools">
	<p class="miniNav"><a><s></s><span>快速导航</span></a></p>	
	<div class="stMore">
		<dl class="side01">
			<dt><s></s>购物</dt>
			<dd>			
				<a href="http://www.suning.com/emall/SNProductCatgroupView?storeId=10052&amp;catalogId=10051&amp;flag=1" name="dac_index_ycdhgw0101" target="_blank">商品分类</a>
				
				<a href="http://www.suning.com/emall/guang_10052_10051_.html" name="dac_index_ycdhgw0102" target="_blank">随便逛逛</a>
				
				<a href="http://www.suning.com/emall/gift_10052_10051_.html" name="dac_index_ycdhgw0103" target="_blank">礼品汇</a>
				
				<a href="http://www.suning.com/emall/LegousMainView?catalogId=10051&amp;storeId=10052" name="dac_index_ycdhgw0104" target="_blank">乐购仕</a>
			</dd>
		</dl>
		<dl class="side02">
			<dt><s></s>生活</dt>
			<dd>			
				<a href="https://life.suning.com/epp-ppp/mo/mobile!input.action" name="dac_index_ycdhsh0201" target="_blank">手机充值</a>
				
				<a href="https://life.suning.com/epp-ppp/ch/charge/charge!frontInput.action" name="dac_index_ycdhsh0202" target="_blank">水电煤</a>
				
				<a href="http://baoxian.suning.com" name="dac_index_ycdhsh0203" target="_blank">保险</a>
				
				<a href="http://jipiao.suning.com/" name="dac_index_ycdhsh0204" target="_blank">机票</a>
				
				<a href="http://jiudian.suning.com/" name="dac_index_ycdhsh0205" target="_blank">酒店</a>
				
				<a href="http://caipiao.suning.com/" name="dac_index_ycdhsh0206" target="_blank">彩票</a>
			
			</dd>
		</dl>
	</div>	
	<em class="stMoreClose"><a title="关闭导航"><i></i></a></em>
	<p class="iToTop"><a href="#" id="btn_goto_top" title="回顶部"><s></s><span>回顶部</span></a></p>
</div>
<!-- foot -->
<div id="foot" class="navbar navbar-fixed-bottom hidden-phone">
    <div class="btn-toolbar">
        <div class="btn-group"><a href="<?php echo $_smarty_tpl->tpl_vars['_SITE_URL']->value;?>
" target="_blank"><i class="icon-globe"></i>查看</a></div>
    	<?php echo $_smarty_tpl->tpl_vars['_foot_toolbar_btns']->value->render();?>

        <div class="btn-group"><a href="#"><i class="icon-off"></i>退出</a></div>        
        <div class="btn-group pull-right">
            <p>&copy; KissGO! 1.0 BETA Demo 2012</p>
        </div>
    </div>
</div>

</body>
</html><?php }} ?>