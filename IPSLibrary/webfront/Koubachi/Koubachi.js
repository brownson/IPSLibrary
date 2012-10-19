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
		openEvent = "click mouseover";
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
	} else {
		tooltip.html(target.attr('kb-tooltip'));
	}
	return tooltip;
}

Koubachi.prototype._tooltipSingleEventHandler = function(evt) {
	var target = $(evt.currentTarget);
	
	var tooltip = this._getTooltipNode(target);
	if(this._isTooltipOpen(tooltip)) {
		this._tooltipClose(tooltip);
	} else {
		tooltip.addClass("stayOpen");
		this._tooltipOpen(tooltip);
	}
}

Koubachi.prototype._tooltipOpenEventHandler = function(evt) {
	var target = $(evt.currentTarget);
	//console.log(evt);
	var tooltip = this._getTooltipNode(target);
	
	if(this._isTooltipOpen(tooltip)) {
		if(evt.type == 'click') {
			stayOpen = tooltip.data("stayOpen");
			if(stayOpen) {
				tooltip.removeClass("stayOpen");
			} else {
				tooltip.addClass("stayOpen");
			}
			tooltip.data("stayOpen", !stayOpen);			
		}
	} else {
		this._tooltipOpen(tooltip);
	}
}

Koubachi.prototype._tooltipCloseEventHandler = function(evt) {
	var target = $(evt.currentTarget);
	var tooltip = this._getTooltipNode(target);
	if(tooltip.data("stayOpen")) return;
	this._tooltipClose(tooltip);
}

Koubachi.prototype._isTooltipOpen = function (tooltip) {
	return tooltip.hasClass("active");
}

Koubachi.prototype._tooltipOpen = function (tooltip) {
	tooltip.addClass("active");
}

Koubachi.prototype._tooltipClose = function (tooltip) {
	this._tooltipResetStayOpen(tooltip);
	tooltip.removeClass("active");
}

Koubachi.prototype._tooltipResetStayOpen = function (tooltip) {
	if(tooltip.data("stayOpen")) {
		tooltip.data("stayOpen", false);
		tooltip.removeClass("stayOpen");
	}
}

Koubachi.iPad = false;
try {
    if (navigator.userAgent.match(/iPad/i)) {
        Koubachi.iPad = true;
    }
} catch (e) {
}