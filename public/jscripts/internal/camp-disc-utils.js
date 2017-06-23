var smTypeForReply;

//view control
if (screen.width >= 320 && screen.width <= 568) { // iPhone
	
	$("i.fa-cloud-download").remove();
	
} else if (screen.width >= 768 && screen.width <= 1024) { // iPad
	
	$("i.fa-cloud-download").remove();
	
} else if (screen.width > 568) { // Desktop
	
	$("#job-table tbody").on("click", "tr.job i.fa-cloud-download", function() {
	
		var $preparingFileModal = $("#preparing-file-modal");
		$preparingFileModal.dialog({ modal: true });
		$(".ui-dialog button.ui-dialog-titlebar-close").html("X"); //hack, X not appearing correctly

		var id = $(this).closest("tr").attr("id");
		$.fileDownload("/" + moduleId + "/download/" + id + "/csv", {
			successCallback: function (url) {
				$preparingFileModal.dialog('close');
			},
			failCallback: function (responseHtml, url) {
				$preparingFileModal.dialog('close');
				$("#error-modal").dialog({ modal: true });
			}
		});

	});
	
} else { // Screen Width lower than 320px
}

$("#job-table tbody").on("click", "tr.job i.fa-pause", function() {

	if (window["oph_" + $(this).closest("tr").attr("id")].pause() === true) {
		$(this).hide(); //hide current icon
		$(this).siblings(".fa-play").show(); //show next icon
	}

});

$("#job-table tbody").on("click", "tr.job i.fa-play", function() {

	if (window["oph_" + $(this).closest("tr").attr("id")].unPause() === true) {
		$(this).hide(); //hide current icon
		$(this).siblings(".fa-pause").show(); //show previous icon
	}

});

$("#job-table tbody").on("click", "tr.job", function() {
	
	var id = $(this).attr("id");
	if ($("#map_" + id).is(":visible")) { return; }
	
	if ($("#loader-element.map-loader").is("hidden")) { $("#loader-element.map-loader").fadeIn("slow"); }

	id = $("#map_cc .current_map_id").html();
	if (parseInt(id) > 0) {
		$("#map_" + id).fadeOut(300);
		$("#job-table tbody tr[id=" + id + "]").removeClass("info");
		//window["oph_" + id].stop();
		window["oph_" + id].stopQueue();
	}

	//show current
	id = $(this).attr("id");
	
	$("#map_" + id).fadeIn(300);
	refresh_maps(id, 10);
	
	if (parseInt(window["oph_" + id].shareStatus()) === 1) {
		$("#map_cc a i.fa-share.unshare").parent().fadeIn(300);
	} else {
		$("#map_cc a i.fa-share.unshare").parent().fadeOut(300);
	}
	
	if ($("#map_cc").is(":hidden")) { $("#map_cc").fadeIn(300); }
	$(this).addClass("info");
	
	$("#map_cc .current_map_id").html(id);
	
	//window["oph_" + id].start();
	window["oph_" + id].startQueue();

});

$("#job-table tbody").on("click", "tr.job i.fa-times", function() {
	var result = confirm("Are you sure you want to delete this " + moduleName + "?");
	if (result === true) {
		var id = $(this).closest("tr").attr("id");
		var thenum = id.replace( /^\D+/g, "");
		$("<form action='/" + moduleId + "/remove' method='post'>\n\
			<input type='hidden' name='id' value='" + thenum + "' />\n\
		</form>").appendTo("body").submit();
	}
});

$("a[href=#newJobContainer]").on("shown.bs.tab", function() {
	google.maps.event.trigger(window["map"], "resize");
	window["map"].panTo(new google.maps.LatLng(window["coords"].lat, window["coords"].lng));
});

$("a[href=#currentList]").on("shown.bs.tab", function() {
	refresh_maps();
});

$("a[href=#newJobContainer]").on("click", function() {
	
	for (var authKey in window["authKeys"]) {
		if ($("form#editor select[name='authKey_" + authKey + "[]'] option").length > 1) { 
			$("form#editor select[name='authKey_" + authKey + "[]'] option").removeAttr("selected");
			//setTimeout(function() { $("form#editor select[name=authKeyId]").trigger("change"); }, 1500);
		}
		$("form#editor input[name='authKeyUse_" + authKey + "']").prop("checked", false);
		$("form#editor input[name='authKeyUse_" + authKey + "']").trigger("change");
		if ($("form#editor select[name='authKey_" + authKey + "[]'] option").length > 1) { 
			$("form#editor select[name='authKey_" + authKey + "[]'] option").removeAttr("selected");
			//setTimeout(function() { $("form#editor select[name=authKeyId]").trigger("change"); }, 1500);
		}
		$("form#editor input[name='authKeyUse_" + authKey + "']").prop("checked", false);
		$("form#editor input[name='authKeyUse_" + authKey + "']").trigger("change");
	}
	$("form#editor input[name=name]").val("");
	$("form#editor input[name=filter]").val("");
	$("form#editor input[name=filter_ex]").val("");
	$("form#editor input[name='days[]']").each(function (index, val) { $(val).prop("checked", false); });
	$("#day-all").prop("checked", false);
	$("form#editor input[name=time_start]").val("");
	$("form#editor input[name=time_end]").val("");
	$("form#editor input[name=date_start]").val("");
	$("form#editor input[name=date_end]").val("");
	$("form#editor input[name=centre_lat]").val("");
	$("form#editor input[name=centre_lng]").val("");
	
	if (window["moduleId"] === "campaign") {
		
		$("form#editor input[name=hourly_limit]").val("1"); $("#slider-range-max").slider("option", "value", 1);
		//$("form#editor select[name=category] option[value='----------']").prop("selected", "selected");
		$("form#editor textarea[name=text]").val(""); $("#preview_text").html("(no message set yet)");
		$("form#editor input[name=response_text]").val(""); $("#response_preview_text").html("(no response message entered yet)");
		$("form#preview_banner").attr("src", "/images/banner_placeholder.png");
		
		$("form#editor input[name=radius]").val("0.5"); $("#slider-range-radius").slider("option", "value", 0.5);
		
	} else {
		
		$("form#editor select[name=messageLifeSpanLimit] option:first").prop("selected", "selected");
		
		$("form#editor input[name=radius]").val("5"); $("#slider-range-radius").slider("option", "value", 5);
		
	}
	
	$("#optimal-time").html("None");
	//$("form#editor input[name=authKeyType]").val("");
	$("form#editor input[name=id]").val("-1");
	
	window["coords"] = window["coords_default"];
	
	window["centre_marker"].setVisible(false);
	window["centre_circle"].setVisible(false);

	wizardFirst();

});

$("#job-table tbody").on("click", "tr.job i.fa-pencil", function() {

	window["json"] = $.parseJSON($(this).closest("tr").attr("json"));
	
	for (var authKey in window["authKeys"]) {
		if ($("form#editor select[name='authKey_" + authKey + "[]'] option").length > 1) { 
			$("form#editor select[name='authKey_" + authKey + "[]'] option").removeAttr("selected");
			//setTimeout(function() { $("form#editor select[name=authKeyId]").trigger("change"); }, 1500);
		}
		$("form#editor input[name='authKeyUse_" + authKey + "']").prop("checked", false);
		$("form#editor input[name='authKeyUse_" + authKey + "']").trigger("change");
		if ($("form#editor select[name='authKey_" + authKey + "[]'] option").length > 1) { 
			$("form#editor select[name='authKey_" + authKey + "[]'] option").removeAttr("selected");
			//setTimeout(function() { $("form#editor select[name=authKeyId]").trigger("change"); }, 1500);
		}
		$("form#editor input[name='authKeyUse_" + authKey + "']").prop("checked", false);
		$("form#editor input[name='authKeyUse_" + authKey + "']").trigger("change");
	}
	
	for (var i in window["json"].authKey) {
		$("form#editor input[name='authKeyUse_" + window["json"].authKey[i].type + "']").prop("checked", true);
		$("form#editor input[name='authKeyUse_" + window["json"].authKey[i].type + "']").trigger("change");
		$("form#editor select[name='authKey_" + window["json"].authKey[i].type + "[]'] option[value='" + window["json"].authKey[i].id + "," + window["json"].authKey[i].type + "']").attr("selected", "selected");
	}
	$("form#editor input[name=name]").val(window["json"].name);
	$("form#editor input[name=filter]").val(window["json"].filter);
	$("form#editor input[name=filter_ex]").val(window["json"].filter_ex);
	$("form#editor input[name='days[]']").each(function (index, val) { $(val).prop("checked", false); });
	$("form#editor input[name='days[]']").each(function (index, val) {
		var daysArr = window["json"].weekdays.split(","), $el = $(val), idx;
		for (idx in daysArr) {
			if (daysArr[idx] === $el.val()) {
				$el.prop("checked", true); break;
			}
		}
		if (daysArr.length === 7) {
			$("#day-all").prop("checked", true);
		} else {
			$("#day-all").prop("checked", false);
		}
	});
	if (window["json"].end_time !== "00:00:00") { $("form#editor input[name=time_start]").val(window["json"].start_time.substring(0, window["json"].start_time.length - 3)); }
	else { $("form#editor input[name=time_start]").val(""); }
	if (window["json"].end_time !== "00:00:00") { $("form#editor input[name=time_end]").val(window["json"].end_time.substring(0, window["json"].end_time.length - 3)); }
	else { $("form#editor input[name=time_end]").val(""); }
	if (window["json"].start_date !== "0000-00-00") { $("form#editor input[name=date_start]").val(window["json"].start_date); }
	else { $("form#editor input[name=date_start]").val(""); }
	if (window["json"].end_date !== "0000-00-00") { $("form#editor input[name=date_end]").val(window["json"].end_date); }
	else { $("form#editor input[name=date_end]").val(""); }
	$("form#editor input[name=centre_lat]").val(parseFloat(window["json"].centre_lat).toFixed(7));
	$("form#editor input[name=centre_lng]").val(parseFloat(window["json"].centre_lng).toFixed(7));
	$("form#editor input[name=radius]").val(window["json"].radius);
	$("#slider-range-radius").slider("option", "value", window["json"].radius);
	
	if (window["moduleId"] === "campaign") {
		
		$("form#editor input[name=hourly_limit]").val(window["json"].hourly_limit); $("#slider-range-max").slider("option", "value", window["json"].hourly_limit);
		//$("form#editor select[name=category] option[value='" + window["json"].category + "']").prop("selected", "selected");
		$("form#editor textarea[name=text]").val(window["json"].text); $("#text").keyup();
		$("form#editor input[name=response_text]").val(window["json"].response_text); $("#response_text").keyup();
		if (window["json"].banner.length > 0) {
			$("#preview_banner").attr("src", "data:" + window["json"].banner_type + ";base64," + window["json"].banner);
			$("#preview_banner_container").show();
		} else {
			$("#preview_banner").attr("src", "/images/banner_placeholder.png");
			$("#preview_banner_container").hide();
		}
		
	} else {
		
		$("form#editor select[name=messageLifeSpanLimit] option[value='" + window["json"].messageLifeSpanLimit + "']").prop("selected", "selected");
		
	}
	
	$("#optimal-time").html(window["authKeys"][window["json"].authKey[0].type][window["json"].authKey[0].id]);
	//$("form#editor input[name=authKeyType]").val(window["json"].authKeyType);
	$("form#editor input[name=id]").val(window["json"].id);

	//map coordinates
	window["coords"] = { lat: parseFloat(window["json"].centre_lat).toFixed(7), lng: parseFloat(window["json"].centre_lng).toFixed(7) };
	
	window["centre_marker"].setVisible(false);
	window["centre_marker"].setPosition(new google.maps.LatLng(window["coords"].lat, window["coords"].lng));
	window["centre_marker"].setVisible(true);
	
	DrawCircle();
	
	wizardFirst();
	
	$("a[href=#newJobContainer]").tab("show");

});

//set map div and all divs contained within to same width and height as map container
$(".job-map").width($("#job-preview").width());
$(".job-map").height($("#job-preview").height());
$(".opheme_map_").width($("#job-preview").width());
$(".opheme_map_").height($("#job-preview").height());
$(".gm-style").width($("#job-preview").width());
$(".gm-style").height($("#job-preview").height());
$(".gm-style > div:first").width($("#job-preview").width());
$(".gm-style > div:first").height($("#job-preview").height());

//refresh on oprientation change
window.addEventListener("orientationchange", function() { location.reload(); }, false);

function refresh_maps(id, zoomLvl) {
	
	if (id) {
		google.maps.event.trigger(window["map_" + id], "resize");
		window["map_" + id].panTo(new google.maps.LatLng(window["coords_" + id].lat, window["coords_" + id].lng));
		if (zoomLvl) {
			setTimeout("window['map_" + id + "'].setZoom(" + (zoomLvl + 1) + ")", 500);
			setTimeout("window['map_" + id + "'].setZoom(" + zoomLvl + ")", 750);
		} else {
			var zoom = window["map_" + id].getZoom();
			setTimeout("window['map_" + id + "'].setZoom(" + (zoom + 1) + ")", 500);
			setTimeout("window['map_" + id + "'].setZoom(" + zoom + ")", 750);
		}
	} else {
		jQuery.each(window["maps_json"], function(i, map) {
			google.maps.event.trigger(map.handle, "resize");
			window["map_" + map.id].panTo(new google.maps.LatLng(window["coords_" + map.id].lat, window["coords_" + map.id].lng));
			if (zoomLvl) {
				setTimeout("window['map_" + map.id + "'].setZoom(" + (zoomLvl + 1) + ")", 500);
				setTimeout("window['map_" + map.id + "'].setZoom(" + zoomLvl + ")", 750);
			} else {
				var zoom = window["map_" + map.id].getZoom();
				setTimeout("window['map_" + map.id + "'].setZoom(" + (zoom + 1) + ")", 500);
				setTimeout("window['map_" + map.id + "'].setZoom(" + zoom + ")", 750);
			}
		});
	}
	
}

function map_cc_grow() {
	
	var id = parseInt($("#map_cc .current_map_id").html());
	
	$("#list-container").hide();
	$("#map-container").removeClass("col-md-8").addClass("col-md-12");
	$(".dummy").css("padding-top", "50%");
	$("#map_" + id + ".job-map").width($("#job-preview").width()-15);
	$("#map_" + id + ".job-map").height($("#job-preview").height());
	$("#map_" + id + ".job-map").addClass("extra-padding");
	$("#map_" + id + " " + ".opheme_map_").width($("#job-preview").width()-15);
	$("#map_" + id + " " + ".opheme_map_").height($("#job-preview").height());
	$("#map_" + id + " " + ".gm-style").width($("#job-preview").width()-15);
	$("#map_" + id + " " + ".gm-style").height($("#job-preview").height());
	$("#map_" + id + " " + ".gm-style > div:first").width($("#job-preview").width()-15);
	$("#map_" + id + " " + ".gm-style > div:first").height($("#job-preview").height());

	refresh_maps(id);

	$("#map_cc a .fa-expand").hide();
	$("#map_cc a .fa-compress").show();

}

function map_cc_shrink() {
	
	var id = parseInt($("#map_cc .current_map_id").html());
	
	$("#map-container").removeClass("col-md-12").addClass("col-md-8");
	$(".dummy").css("padding-top", "75%");
	$("#list-container").show();
	$("#map_" + id + ".job-map").removeClass("extra-padding");
	$("#map_" + id + ".job-map").width($("#job-preview").width());
	$("#map_" + id + ".job-map").height($("#job-preview").height());
	$("#map_" + id + " " + ".opheme_map_").width($("#job-preview").width());
	$("#map_" + id + " " + ".opheme_map_").height($("#job-preview").height());
	$("#map_" + id + " " + ".gm-style").width($("#job-preview").width());
	$("#map_" + id + " " + ".gm-style").height($("#job-preview").height());
	$("#map_" + id + " " + ".gm-style > div:first").width($("#job-preview").width());
	$("#map_" + id + " " + ".gm-style > div:first").height($("#job-preview").height());

	refresh_maps(id);

	$("#map_cc a .fa-expand").show();
	$("#map_cc a .fa-compress").hide();

}

function map_cc_zoomIn() {
	var id = parseInt($("#map_cc .current_map_id").html());
	window["map_" + id].setZoom(window["map_" + id].getZoom() + 1);
}

function map_cc_zoomOut() {
	var id = parseInt($("#map_cc .current_map_id").html());
	window["map_" + id].setZoom(window["map_" + id].getZoom() - 1);
}

function map_cc_close() {
	var id = parseInt($("#map_cc .current_map_id").html());
	$("#map_" + id).fadeOut(300);
	map_cc_shrink();
	$("#map_cc").fadeOut(300);
}

// Sharing

function shareJob() {
	
	var id = parseInt($("#map_cc .current_map_id").html());
	
	if (window["oph_" + id].share() === true) {
		
		//take care of share link span self selection
		$("span#share-link").click(function(){
			var element = this;
			if (document.body.createTextRange) { // ms
				var range = document.body.createTextRange();
				range.moveToElementText(element);
				range.select();
			} else if (window.getSelection) { // moz, opera, webkit
				var selection = window.getSelection();
				var range = document.createRange();
				range.selectNodeContents(element);
				selection.removeAllRanges();
				selection.addRange(range);
			}
		});
		
		$("#map_cc a i.fa-share.unshare").parent().show();
		
	}
	
}

function createShareForm() {
	
	var id = parseInt($("#map_cc .current_map_id").html());
	$("#postToSM").modal("show");
	$("#postToSM form").submit(function () { shareJobToSM(id); return false; });
	
	$("#postToSM select[name=authKeyId]").html(window["accountsSelectOptions"]);
	$("#postToSM select[name=authKeyId]").closest(".form-group").children("label").css("text-align", "left");
	$("#postToSM select[name=authKeyId]").closest(".form-group").children("label").addClass("col-sm-3").removeClass("col-sm-6");
	$("#postToSM select[name=authKeyId]").closest(".form-group").children("label").children("span").remove();
	$("#postToSM select[name=authKeyId]").closest("div").show();
	$("#postToSM button.close").closest("div").removeClass("col-sm-6").addClass("col-sm-3");;
	
	var count = 0;
	$("#postToSM select[name=authKeyId] option").each(function() { // hide tokens not relevant to current reply
		var $el = $(this); count++;
		if ($el.html().indexOf("Instagram - ") > -1) { //remove Instagram options because posting is not allowed from non mobiles
			$el.remove(); count--;
		}
	});
	
	$("#postToSM select[name=authKeyId] option:first").attr("selected", "selected");
	$("#postToSM select[name=authKeyId]").change();
	
	if (count === 1) {
		var token = $("#postToSM [name=authKeyType]").val();
		$("#postToSM select[name=authKeyId]").closest(".form-group").children("label").removeClass("col-sm-3").addClass("col-sm-6");
		$("#postToSM select[name=authKeyId]").closest(".form-group").children("label").append("<span style='text-transform: capitalize'>" + token + "</span>");
		$("#postToSM select[name=authKeyId]").closest("div").hide();
		$("#postToSM button.close").closest("div").addClass("col-sm-6").removeClass("col-sm-3");
	}
	
	smTypeForReply = $("#postToSM [name=authKeyType]").val();
	
	$("#postToSM textarea").focus();

	setTimeout(function() { $("#postToSM textarea").change(); }, 500);
	
	$("#postToSM span.count-message-length").html("Unlimited");
	
}

function shareJobToSM(id) {
	
	var msg = $("#postToSM textarea[name=msg]").val(),
		smType = $("#postToSM input[name=authKeyType]").val(),
		smId = $("#postToSM select[name=authKeyId]").val();

	$("#postToSM textarea[name=msg]").val("");
	$("#postToSM input[name=authKeyType]").val("");
	$("#postToSM select[name=authKeyId] option:first").prop("selected", "selected");
	
	if (msg.length > 0) {
		if (window["oph_" + id].shareToSM(msg, smType, smId) === true) {
			$("#map_cc a i.fa-share.unshare").parent().show();
		}
		$("#postToSM").modal("hide");
	}
	
}

function unShareJob() {
	var id = parseInt($("#map_cc .current_map_id").html());
	if (window["oph_" + id].unShare() === true) {
		$("#map_cc a i.fa-share.unshare").parent().hide();
	}
}

$("#postToSM form").validate({
	rules: {
		authKeyId: {
			selectcheck: true
		},
		msg: {
			required: true,
			maxlength: share_max_length
		}
	},
	messages: {
		authKeyId: "Please select a Social Media Account for this Share.",
		msg: {
			required: "This is the Message your followers will see, can't be empty.",
			maxlength: "Please write up to " + share_max_length + " characters."
		}
	},
	highlight: function(element) {
		$(element).closest(".form-group").addClass("has-error");
	},
	unhighlight: function(element) {
		$(element).closest(".form-group").removeClass("has-error");
	},
	errorElement: "span",
	errorClass: "help-block",
	errorPlacement: function(error, element) {
		if(element.parent(".input-group").length) {
			error.insertAfter(element.parent());
		} else if ((element).parent().hasClass("checkbox-wrapper")) {
			error.insertAfter(".checkbox-container");
		} else {
			error.insertAfter(element);
		}
	},
	ignore: ""
});
	
$("#postToSM textarea[name=msg]").on("input change", function() {
	var $el = $(this);
	if (smTypeForReply === "twitter") {
		if ($el.val().length > share_max_length) { $el.val($el.val().substr(0, share_max_length)); } else {}
		$("#postToSM span.count-message-length").html(share_max_length - $el.val().length);
	} else {}
});

// Reply to Message

$("#replyToMsg form").submit(function (e) { e.preventDefault(); sendReply(); return false; });

var reply_SMT, reply_SMID, reply_USN, reply_UID, reply_MID, reply_OM, reply_OMD, reply_M;

function createReplyForm(smType, userScreenName, userId, messageId, origMsg, origMsgDate) {
	
	smTypeForReply = smType;
	
	reply_SMT = smType;
	reply_USN = userScreenName;
	reply_UID = userId;
	reply_MID = messageId;
	reply_OM = origMsg;
	reply_OMD = origMsgDate;
	
	$("#replyToMsg").modal("show");
	$("#replyToMsg .modal-title span").html(reply_USN);
	
	$("#replyToMsg select[name=authKeyId]").html(window["accountsSelectOptions"]);
	$("#replyToMsg select[name=authKeyId]").closest(".row").show();
	
	var count = 0;
	$("#replyToMsg select[name=authKeyId] option").each(function() { // hide tokens not relevant to current reply
		var $el = $(this); count++;
		if ($el.html().indexOf(capitalize(reply_SMT) + " - ") === -1) {
			$el.remove(); count--;
		}
	});
	
	if (count === 1) {
		$("#replyToMsg select[name=authKeyId]").closest(".row").hide();
	}
	
	$("#replyToMsg select[name=authKeyId] option:first").attr("selected", "selected");
	$("#replyToMsg select[name=authKeyId]").change();
	
	$("#replyToMsg textarea").val("@" + reply_USN + " ");
	$("#replyToMsg textarea").focusTextToEnd();
	
	setTimeout(function() { $("#replyToMsg textarea").change(); }, 500);
	
	$("#replyToMsg span.count-message-length").html("Unlimited");
	
}

function sendReply() {
	
	// use current map's oUI handler ID
	var id = parseInt($("#map_cc .current_map_id").html());
	
	reply_M = $("#replyToMsg textarea[name=msg]").val();
	reply_SMID = $("#replyToMsg select[name=authKeyId]").val();

	$("#replyToMsg textarea[name=msg]").val("");
	$("#replyToMsg input[name=authKeyType]").val("");
	$("#replyToMsg select[name=authKeyId] option:first").prop("selected", "selected");
	
	if (reply_M.length > 0) {
		if (window["oph_" + id].sendReplyToSM(reply_M, reply_SMT, reply_SMID, reply_USN, reply_UID, reply_MID, reply_OM, reply_OMD) === true) {}
		$("#replyToMsg").modal("hide");
		$("#replyToMsg select[name=authKeyId]").html(accountsSelectOptions);
	}
	
}

$("#replyToMsg form").validate({
	rules: {
		authKeyId: {
			selectcheck: true
		},
		msg: {
			required: true,
			maxlength: reply_max_length
		}
	},
	messages: {
		authKeyId: "Please select a Social Media Account for this Reply.",
		msg: {
			required: "This is the Message the Recipient will see, can't be empty.",
			maxlength: "Please write up to " + reply_max_length + " characters."
		}
	},
	highlight: function(element) {
		$(element).closest(".form-group").addClass("has-error");
	},
	unhighlight: function(element) {
		$(element).closest(".form-group").removeClass("has-error");
	},
	errorElement: "span",
	errorClass: "help-block",
	errorPlacement: function(error, element) {
		if(element.parent(".input-group").length) {
			error.insertAfter(element.parent());
		} else if ((element).parent().hasClass("checkbox-wrapper")) {
			error.insertAfter(".checkbox-container");
		} else {
			error.insertAfter(element);
		}
	},
	ignore: ""
});

$("#replyToMsg textarea[name=msg]").on("input change", function() {
	var $el = $(this);
	if (smTypeForReply === "twitter") {
		if ($el.val().length > reply_max_length) { $el.val($el.val().substr(0, reply_max_length)); } else {}
		$("#replyToMsg span.count-message-length").html(reply_max_length - $el.val().length);
	} else {}
});