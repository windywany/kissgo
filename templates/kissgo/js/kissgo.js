$(function () {
    $('#btn_goto_top').click(function () {
        $('body').scrollTop(0);
        return false;
    });
    $('#sideTools').hover(function(){    	
    	$(this).css('right',0);    	
    },function(){    	
    	if(!$('#sideTools').find('p.miniNav a').hasClass('on')){
    		$(this).css('right',-52);   
    	}    		
    }).find('p.miniNav').click(function(){
    	$(this).find('a').addClass('on');
    	$('#sideTools').find('div.stMore').slideDown(function(){
    		$('#sideTools').find('em.stMoreClose').css('display','block');
    	});
    });    
    $('#sideTools').find('em.stMoreClose').click(function(){
    	$('#sideTools').find('p.miniNav a').removeClass('on');
    	$('#sideTools').find('div.stMore').slideUp(function(){
    		$('#sideTools').find('em.stMoreClose').hide();
    	});
    });
});