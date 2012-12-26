{include file="kissgo/head.tpl"}
<!-- container -->
<div id="container">
<div class="container-fluid">
    <div id="container-wrap">
        <div id="sidebar">
            <ul class="nav nav-tabs nav-stacked nav-kissgo affix">
                {block name="sidemenu"}{/block}
            </ul>
        </div>
        <div id="body">
            {block name="admincp_body"}{/block}
        </div>
    </div>
</div>
</div>
{include file="kissgo/foot.tpl"}