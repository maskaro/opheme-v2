jQuery.validator.addMethod("selectcheck", function (value) {
	return (value !== "----------");
}, "Please select a valid option.");

jQuery.validator.addMethod("containsPercentR", function(value, element, param) {
	return this.optional(element) || value.toLowerCase().indexOf("%r") !== -1;
}, "You must enter %r");

jQuery.validator.addMethod("containsPercentC", function(value, element, param) {
	return this.optional(element) || value.toLowerCase().indexOf("%c") !== -1;
}, "You must enter %c");

$.each(['show', 'hide'], function (i, ev) {
	var el = $.fn[ev];
	$.fn[ev] = function () {
		this.trigger(ev);
		return el.apply(this, arguments);
	};
});

function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}

function hideAllBut(top_cls, cls, id) {
	$("." + cls + id).modal("toggle");
	/*if ($("." + cls + id).is(":visible")) {
		$("." + cls + id).modal("hide");
	} else {
		$("[class^=" + top_cls + "]").each(function() {
			$(this).modal("hide");
		});
		$("." + cls + id).modal("show");
	}*/
}

function hide(cls, id) {
	$("." + cls + id).fadeOut(300);
}

function capitalize (text) {
    return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
}

function timeConverter(UNIX_timestamp){
	var a = new Date(UNIX_timestamp*1000);
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var year = a.getFullYear();
	var month = months[a.getMonth()];
	var date = a.getDate();
	var hour = a.getHours(); if (hour < 10) { hour = '0' + hour; }
	var min = a.getMinutes(); if (min < 10) { min = '0' + min; }
	var sec = a.getSeconds(); if (sec < 10) { sec = '0' + sec; }
	var time = date + ', ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
	return time;
}

(function($){
    $.fn.focusTextToEnd = function(){
        this.focus();
        var $thisVal = this.val();
        this.val('').val($thisVal);
        return this;
    }
}(jQuery));

function getInteractionUpdateCount() {
	
	var time = lastActionTime,
		data = { userId: _oUserId, since: time };
			
	$.ajax({
		async: true,
		type: "POST",
		cache: false,
		dataType: "json",
		url: "/ajax/socialMedia/getInteraction",
		data: data
	}).done(function(msg) {
		if (msg instanceof Array && msg.length > 0) {
			var cnt = 0;
			for (var i in msg) { var inter = msg[i]; if (inter.type.indexOf("_out") === -1) { cnt++; } }
			if (cnt) {
				$("#wizard-menu-smInteraction .badge").html((parseInt($("#wizard-menu-smInteraction .badge").html()) + cnt));
				//$("#wizard-menu-smInteraction .badge").html(cnt);
			}
			if (parseInt($("#wizard-menu-smInteraction .badge").html()) > 0) {
				$("#wizard-menu-smInteraction .badge").addClass("alert-danger");
			} else {
				$("#wizard-menu-smInteraction .badge").removeClass("alert-danger");
			}
			lastActionTime = Math.round(new Date().getTime() / 1000);// + localTimeOffSet;
		}
	});
	
}