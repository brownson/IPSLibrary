function Koubachi() {
	//console.log("Creating Koubachi");
	
	this._tooltipsHookup();
}

Koubachi.prototype._tooltipsHookup = function () {
	var self = this;
	
	tooltips = $("*.row > *[kb-tooltip]");
	$.each(tooltips, function(idx, el) {
		self._tooltipHookup($(el));
	});
}

Koubachi.prototype._tooltipHookup = function ($el) {
	var self = this;
	
	if(Koubachi.iPad) {
		// display tooltips by clicking on them
		openEvent = "touchstart";
		closeEvent = "";
		openEventHandler = self._tooltipSingleEventHandler;
		closeEventHandler = false;
	} else {
		// display tooltips by hovering with the mouse
		openEvent = "mouseover";
		closeEvent = "mouseout";
		openEventHandler = self._tooltipOpenEventHandler;
		closeEventHandler = self._tooltipCloseEventHandler;
	}
	$el.bind(openEvent, {}, $.proxy(openEventHandler, self));
	if(closeEventHandler) {
		$el.bind(closeEvent, {}, $.proxy(closeEventHandler, self));
	}
}

Koubachi.prototype._getTooltipNode = function(target) {
	var parent = target.parent();
	var tooltip = parent.next();
	if(!tooltip.hasClass('message')) {
		var div = $(document.createElement("div"));
		div.attr("class", "message");
		div.html(target.attr('kb-tooltip'));
		parent.after(div);
		tooltip = div;
		tooltip.bind("click touchend", {}, $.proxy(function() { this._tooltipClose(tooltip);}, this));
	}
	return tooltip;
}

Koubachi.prototype._tooltipSingleEventHandler = function(evt) {
	var target = $(evt.currentTarget);
	
	var tooltip = this._getTooltipNode(target);
	if(this._isTooltipOpen(tooltip)) {
		this._tooltipClose(tooltip);
	} else {
		this._tooltipOpen(tooltip);
	}
}

Koubachi.prototype._tooltipOpenEventHandler = function(evt) {
	var target = $(evt.currentTarget);
	
	var tooltip = this._getTooltipNode(target);
	// check if the element is already visible in order to hide it now
	/*if(this._isTooltipOpen(tooltip)) {
		this._tooltipClose(tooltip);
	} else {
		this._tooltipOpen(tooltip);
	}*/
	this._tooltipOpen(tooltip);
}

Koubachi.prototype._isTooltipOpen = function (tooltip) {
	return tooltip.hasClass("active");
}

Koubachi.prototype._tooltipOpen = function (tooltip) {
	tooltip.addClass("active");
}

Koubachi.prototype._tooltipClose = function (tooltip) {
	tooltip.removeClass("active");
}

Koubachi.prototype._tooltipCloseEventHandler = function(evt) {
	var target = $(evt.currentTarget);
	var tooltip = this._getTooltipNode(target);
	this._tooltipClose(tooltip);
}

Koubachi.iPad = false;
try {
    if (navigator.userAgent.match(/iPad/i)) {
        Koubachi.iPad = true;
    }
} catch (e) {
}