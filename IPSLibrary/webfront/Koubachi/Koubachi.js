function Koubachi() {
	var self = this;
	
	//console.log("Creating Koubachi");
	
	warning = $(".kb-tooltip");
	
	$.each(warning, function(idx, el) {
		self._hookupWarning($(el));
	});
}

Koubachi.prototype._hookupWarning = function ($el) {
	var self = this;
	
	var eventName = Koubachi.iPad ? 'touchstart' : 'mouseover';
	$el.bind(eventName, {}, $.proxy(function() {
		var that = this;
		//console.log("this: ", this);
		var parent = $(this).parent();
		var element = parent.next();
		if(!element.hasClass('message')) {
			var div = $(document.createElement("div"));
			div.attr("class", "message");
			div.html(that.attr('kb-tooltip'));
			parent.after(div);
		}
		//console.log("adding class");
		element.addClass('active');
	}, $el));
	
	var eventName2 =  Koubachi.iPad ? 'touchend' : 'mouseout';
	$el.bind(eventName2, {}, $.proxy(function() {
		var element = $(this).parent().next();
		element.removeClass('active');
	}, $el));
}

Koubachi.iPad = false;
try {
    if (navigator.userAgent.match(/iPad/i)) {
        Koubachi.iPad = true;
    }
} catch (e) {
}