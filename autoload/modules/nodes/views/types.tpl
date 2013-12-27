{extends file=$layout}
{block name="subtitle"}页面类型{/block}
{block name="workbench"}
<h3>本系统共有{$totalType}种页面</h3>
<table class="table">
  <tr>
    <th class="text-left">类型</th>
    <th class="text-left">名称</th>
   	<th class="text-left">可创建</th>
    <th class="text-left">默认模板</th>
    <th class="text-left">说明</th>
  </tr>
  {foreach $types as $key=>$type}
  <tr>
    <td>{$key}</td>
    <td>{$type[0]}</td>
    <td>{if $type[2]}是{else}否{/if}</td>
    <td>{$type[1]}</td>
    <td>{$type[3]}</td>
  </tr>
  {/foreach}
</table>
<dl>
	<dt>说明:</dt>
	<dd>如需修改页面的默认模板请到“主题模板”中设置。</dd>
</dl>
{/block}