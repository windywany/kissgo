<div id="sideTools">
	<p class="miniNav"><a><s></s><span>快速导航</span></a></p>	
	<div class="stMore">
		<dl class="side01">			
			<dd>
                <a href="#">商品分类</a>
                <a href="#">随便逛逛</a>
                <a href="#">礼品汇</a>
				<a href="#">
				    <i class="icon-off"></i>乐购仕
				</a>
			</dd>
		</dl>		
	</div>
	<em class="stMoreClose"><a title="关闭导航"><i></i></a></em>
	<p class="iToTop"><a href="#" id="btn_goto_top" title="回顶部"><s></s><span>回顶部</span></a></p>
</div>
<!-- foot -->
<div id="foot" class="navbar navbar-fixed-bottom hidden-phone">
    <div class="btn-toolbar">
        <div class="btn-group"><a href="{$_SITE_URL}test.php" target="_blank"><i class="icon-globe"></i>{'View'|ts}</a></div>
    	{$_foot_toolbar_btns->render()}
        <div class="btn-group"><a href="{'passport'|murl:'logout'}"><i class="icon-off"></i>{'Logout'|ts}</a></div>
        <div class="btn-group pull-right">
            <p>&copy; <a href="http://www.kissgo.org/" target="_blank">KissGO! {$_KISSGO_VERSION}</a> Demo 2012</p>
        </div>
    </div>
</div>
{'kissgo_dashboard_footer'|fire}
{block name="foot_js_block"}{/block}
</body>
</html>