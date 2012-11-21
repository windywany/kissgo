<?php /* Smarty version Smarty-3.1.12, created on 2012-11-21 17:25:34
         compiled from "C:\java sources\kissgo\templates\admincp\admin\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:511250ac9e0ee5f906-50280760%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '81e902e609979b5df1524845caca6721a07d8671' => 
    array (
      0 => 'C:\\java sources\\kissgo\\templates\\admincp\\admin\\index.tpl',
      1 => 1352851504,
      2 => 'file',
    ),
    '6e4b0f05d49191a8ec3b619bb2f1e2770eb16faf' => 
    array (
      0 => 'C:\\java sources\\kissgo\\templates\\admincp\\admincp_with_sidemenu.tpl',
      1 => 1353485706,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '511250ac9e0ee5f906-50280760',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50ac9e0ee98349_02724301',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ac9e0ee98349_02724301')) {function content_50ac9e0ee98349_02724301($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("admincp/head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- container -->
<div id="container" class="container-fluid">
    <div id="container-wrap">
        <div id="sidebar">
            <ul class="nav nav-tabs nav-stacked nav-kissgo affix">
                
            </ul>
        </div>
        <div id="body">
            admin control panel
        </div>
    </div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("admincp/foot.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>