define('jquery/blockit', function() {
	(function($) {
		var loadingImg = KsgApp.assets('images/loading.gif');
		$.fn.blockit = function() {
			var $this = $(this);
			var id = $this.data('blocked');
			if (!id) {
				var date = new Date();
				id = 'bk-' + date.getTime();
				$this.data('blocked', id);
				var w = $this.outerWidth(),h = $this.outerHeight(),offset=$this.offset();
				var block = $('<div id="'+id+'-overlay"></div>').css({cursor:'wait',position:'fixed','z-index':29999,background:'#FFF',display:'none',top:0,left:0,bottom:0,right:0}).appendTo($this);
				var img     = $('<img id="'+id+'-img" src="'+loadingImg+'"/>').css({position:'absolute','z-index':30000,x:-10000,top:-10000}).appendTo($this);								
				block.fadeTo(100,0.25,function(){
					img.css({left:(w-32)/2,top:(h-32)/2});
				});
			}
		};
		$.fn.unblockit = function() {
			var id = $(this).data('blocked');
			if (id) {
				$('#' + id+'-img').remove();
				$('#' + id+'-overlay').fadeOut(100, function() {					
					$(this).remove();
				});
			}
		};
	})(jQuery);
});