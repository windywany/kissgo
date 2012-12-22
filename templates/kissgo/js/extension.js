/**
 * 
 */

$(function(){
	//alert('ok');
	$('a.install_ext').click(function(){
		alert($(this).attr('data-pid'));
		return false;
	});
});