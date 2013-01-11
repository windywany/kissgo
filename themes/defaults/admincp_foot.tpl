<div id="sideTools">
    <a href="#top" id="btn_goto_top"></a>	
</div>
<!-- foot -->
<div id="foot" class="navbar navbar-fixed-bottom hidden-phone">
    <div class="btn-toolbar">
        <div class="btn-group"><a href="{$ksg_site_url}" target="_blank"><i class="icon-globe"></i>{'View'|ts}</a></div>
    	{$ksg_foot_toolbar_btns->render()}
        <div class="btn-group"><a href="{'admin'|murl:'logout'}"><i class="icon-off"></i>{'Logout'|ts}</a></div>
        <div class="btn-group pull-right">
            <p>&copy; <a href="http://www.kissgo.org/" target="_blank">KissGO! {$ksg_version}</a> 2012</p>
        </div>
    </div>
</div>
{'admincp_footer'|fire}
{block name="admincp_foot_js_block"}{/block}
</body>
</html>