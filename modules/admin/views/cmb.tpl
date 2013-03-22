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
    <ul class="nav nav-tabs" id="cmb-tabs">
		<li>&nbsp;&nbsp;</li>	
		<li class="active"><a href="#cmb_design" class="cmb_tab" data-toggle="tab"><i class="icon-edit"></i> {'Design'|ts}</a></li>
		<li><a href="#cmb_source" class="cmb_tab" data-toggle="tab"><i class="icon-file"></i> {'Source'|ts}</a></li>			    
	</ul>	
	<div class="tab-content">	
    	<div class="tab-pane active" id="cmb_design">
    		<div class="stuffbox">
    			<h3>Model</h3>
    			<div class="inside form-horizontal">{$model_form|form}</div>    			
    		</div>
    		<div class="stuffbox">
    			<a class="tools" id="cmb-definition-add"><i class="icon-plus"></i> Add</a>
        		<h3>Definition</h3>        		
        		<div class="inside">
        			
        			<div class="tabbable tabs-below">
                      <div class="tab-content">
                      		<div class="tab-pane active" id="cmb-fields">
                      			<table class="table table-striped">
                      			  <thead>
                      			  	<tr>                      			  		
                      			  		<th class="w150">Field</th>
                      			  		<th class="w80">Type</th>
                      			  		<th class="w50">Length</th>
                      			  		<th class="w50">NN</th>
                      			  		<th class="w50">UNSIGNED</th>
                      			  		<th class="w180">Default</th>                      			  		
                      			  		<th class="wa">Comment</th>
                      			  		<th class="w120"></th>
                      			  	</tr>
                      			  </thead>
                                  <tbody id="cmb-field-list">
                                  	<tr id="cmb-f-row-0" style="display:none;">                                  		
                                  		<td class="fe-field"></td>
                                  		<td class="fe-type"></td>
                                  		<td class="fe-length"></td>
                                  		<td class="fe-nn"></td>
                                  		<td class="fe-unsigned"></td>
                                  		<td class="fe-default"></td>
                                  		<td class="fe-comment"></td>
                                  		<td>
                                  			<a href="#" class="cmb-f-edit"><i class="icon-edit"></i> Edit</a>
                                  			<a href="#" class="cmb-f-delete"><i class="icon-remove"></i> Delete</a>
                                  		</td>
                                  	</tr>
                                  </tbody>
                                </table>
                      		</div>
                      		<div class="tab-pane" id="cmb-pk">
                      			primary keys
                      		</div>
                      		<div class="tab-pane" id="cmb-idx">
                      			indexes
                      		</div>
                      </div>
                      <ul class="nav nav-tabs" id="cmb-definition-tabs">
                        	<li>&nbsp;&nbsp;</li>
                        	<li class="active"><a href="#cmb-fields" data-toggle="tab">Field</a></li>
                        	<li><a href="#cmb-pk" data-toggle="tab">Primary Key</a></li>
                        	<li><a href="#cmb-idx" data-toggle="tab">Index</a></li>
                      </ul>
                    </div>
        			
    			</div>
      		</div>
    		
    	</div>
    	<div class="tab-pane" id="cmb_source">    		
<pre class="prettyprint linenums" id="cmb-php-source">
</pre>
    	</div>    	
    </div>
<div id="field-editor" class="modal hide fade" tabindex="-1" data-width="660" data-backdrop="static" data-keyboard="false">
	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Field Editor</h3>
    </div>
    <div class="modal-body">
    	<input type="hidden" id="fe_id" value=""/>
    	<div class="row-fluid">    		
    		<div class="span6">    			
    			<label for="fe_field">Field</label>
    			<input id="fe_field" type="text" class="span12"/>
    			<label for="fe_type">Type</label>
    			<select id="fe_type" class="span12">
    				{foreach from=$types item=type}
    				<option value="{$type@key}">{$type@key}</option>
    				{/foreach}
    			</select>
    			<label class="checkbox"><input id="fe_nn" type="checkbox"/> NN</label>    			
    		</div>
    		<div class="span6">
    			<label for="fe_default">Default</label>
    			<input id="fe_default" type="text" class="span12"/>
    			<label for="fe_length">Length</label>
    			<input id="fe_length" type="text" class="span12"/>
    			<label class="checkbox"><input id="fe_unsigned" type="checkbox"/> UNSIGNED</label> 
    		</div>
    		<div class="span12" style="margin-left:0px;">
    			<label for="fe_comment">Comment</label>
    			<textarea id="fe_comment" rows="3" class="span12"></textarea>
    		</div>
    	</div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn">Close</button>
        <button type="button" data-dismiss="modal" class="btn btn-primary" id="field-editor-done">Done</button>
	</div>
</div>
{/block}
{block name="admincp_foot_js_block"}
	<script type="text/javascript" src="{'js/cmb.js'|here}"></script>
{/block}