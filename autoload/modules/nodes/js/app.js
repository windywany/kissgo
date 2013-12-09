define('nodes/js/app', function(require, exports) {
	require('jquery/flexigrid');
	var grid;
	exports.main = function(desktop) {
	   if(!grid){
		   grid = $('#nodes_grid').flexigrid({
               url : KsgApp.acturl('nodes','data'),
               dataType : 'json',
               colModel : [ {
                   display : 'ID',
                   name : 'id',
                   width : 50,
                   sortable : true,
                   align : 'center'
                   }, {
                       display : 'Name',
                       name : 'name',
                       width : 240,
                       sortable : true,
                       align : 'left'
                   }, {
                       display : 'Primary Language',
                       name : 'primary_language',
                       width : 120,
                       sortable : true,
                       align : 'left'
                   }, {
                       display : 'Favorite Color',
                       name : 'favorite_color',
                       width : 180,
                       sortable : true,
                       align : 'left',
                       hide : true
                   }, {
                       display : 'Favorite Animal',
                       name : 'favorite_pet',
                       width : 180,
                       sortable : true,
                       align : 'right'
               } ],
               sortname : "iso",
               sortorder : "asc",
               usepager : true,               
               useRp : true,
               rp : 15,
               showTableToggleBtn : false
           });		   
	   }
	   
	};
});