<div id="sideTools">
    <a href="#top" id="btn_goto_top"></a>	
</div>
<!-- foot -->
<div id="foot" class="navbar navbar-fixed-bottom hidden-phone">
    <div class="btn-toolbar">
        <div class="btn-group"><a href="{$_SITE_URL}" target="_blank"><i class="icon-globe"></i>{'View'|ts}</a></div>
    	{$_foot_toolbar_btns->render()}
        <div class="btn-group"><a href="{'passport'|murl:'logout'}"><i class="icon-off"></i>{'Logout'|ts}</a></div>
        <div class="btn-group pull-right">
            <p>&copy; <a href="http://www.kissgo.org/" target="_blank">KissGO! {$_KISSGO_R_VERSION}</a> 2012</p>
        </div>
    </div>
</div>
{'kissgo_dashboard_footer'|fire}
{block name="foot_js_block"}{/block}
</body>
</html>