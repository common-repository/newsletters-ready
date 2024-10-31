(function( $ ) {
	$.fn.slideInput = function(checked) {
		return this.each(function(){
			var animationDuration = 100
			,	$this = $(this)
			,	turnOn = function(immidiate) {
				var animateTime = immidiate ? 0 : animationDuration;
				$this.find('.toeSlideButtSub').animate({
					'left': '30px'
				}, animateTime);
				$this.find('.toeSlideOnSub').animate({
					'width': '40px'
				}, animateTime);
				$this.find('input[type=hidden]').val( 1 );
			}
			,	turnOff = function(immidiate) {
				var animateTime = immidiate ? 0 : animationDuration;
				$this.find('.toeSlideButtSub').animate({
					'left': '0px'
				}, animateTime);
				$this.find('.toeSlideOnSub').animate({
					'width': '0px'
				}, animateTime);
				$this.find('input[type=hidden]').val( 0 );
			};
			$this.click(function(e) {
				e.preventDefault();
				parseInt($this.find('input[type=hidden]').val()) ? turnOff() : turnOn();
				return false;
			});
			checked ? turnOn(1) : turnOff(1);
		});
	};
})(jQuery);