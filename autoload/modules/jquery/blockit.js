define('jquery/blockit', function() {
	(function($) {
		var loadingImg = seajs.assets('images/loading.gif');
		$.fn.blockit = function() {
			var $this = $(this);
			var id = $this.data('blocked');
			if (!id) {
				var date = new Date();
				id = 'bk-' + date.getTime();
				$this.data('blocked', id);
				var w = $this.outerWidth(),h = $this.outerHeight(),offset=$this.offset();
				var block = $('<div id="#'+id+'-overlay"></div>').css({position:'absolute',width:w,height:h,'z-index':29999,background:'#999',display:'none',top:0,left:0}).appendTo($this);
				var img     = $('<img id="'+id+'-img" src="'+loadingImg+'"/>').css({position:'absolute','z-index':30000,x:-100,top:-100}).appendTo($this);								
				block.fadeTo(100,0.15,function(){
					img.css({left:(w-32)/2,top:(h-32)/2});
				});
			}
		};
		$.fn.unblockit = function() {
			var id = $(this).data('blocked');
			if (id) {
				$('#' + id+'-overlay').fadeOut(100, function() {
					$('#' + id+'-img').remove();
					$(this).remove();
				});
			}
		};
	})(jQuery);
});