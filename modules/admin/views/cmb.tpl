{extends file=$ksg_admincp_layout}
{block name="title"}{'Model Builder'|ts}{/block}
{block name="breadcrumb"}
	<li>{'Model Builder'|ts}</li>	
{/block}
{block name="admincp_css_block"}
<link rel="stylesheet" href="{'bootstrap/css/prettify.css'|static}"/>
{/block}
{block name="admincp_head_js_block"}
	<script type="text/javascript" src="{'bootstrap/prettify.js'|static}"></script>
{/block}
{block name="admincp_body"}
    <ul class="nav nav-tabs">
		<li>&nbsp;&nbsp;</li>	
		<li class="active"><a href="#cmb_design" class="cmb_tab" data-toggle="tab"><i class="icon-edit"></i> {'Design'|ts}</a></li>
		<li><a href="#cmb_source" id="view_source" class="cmb_tab" data-toggle="tab"><i class="icon-file"></i> {'Source'|ts}</a></li>			    
	</ul>
	
	<div class="tab-content">
	
    	<div class="tab-pane active" id="cmb_design">
    		<div class="row-fluid">
    			<div class="span3">
    				<div class="stuffbox">
    					<h3>Model</h3>
    					<div class="inside form-horizontal">
    						{$model_form|form}
    					</div>
    				</div>
    				<div class="stuffbox">
    					<h3>Fields</h3>
    					<div class="inside">
    						
    					</div>
    				</div>
    			</div>
    			<div class="span9">
    				<div class="stuffbox">
    					<h3>Definition <div class="handlediv"></div></h3>
    					<div class="inside">
    						
    					</div>
    				</div>
    				<div class="stuffbox">
    					<h3>Primary Key</h3>
    					<div class="inside">
    						
    					</div>
    				</div>
    				<div class="stuffbox">
    					<h3>Indexes</h3>
    					<div class="inside">
    						
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    	<div class="tab-pane" id="cmb_source">
    		
<pre class="prettyprint linenums">
&lt;?php
	var $a = 10; // what's this?
	var $b = array();
	$b[1] = 'hello world';
	/**
	 * print the second element of $b array to client.
	 */
	echo $b[1];
	exit(0);
?&gt;
</pre>
    	</div>
    	
    </div>
{/block}
{block name="admincp_foot_js_block"}
	<script type="text/javascript">
		$(function(){
			window.prettyPrint && prettyPrint();
		});
	</script>
{/block}