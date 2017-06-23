var intervalSec = 30,
	interactionDiv = "#interactions",
	max_length = 140, // reply msg length
	loadMaxOld = 10,
	clock = $("#countdown"), clockInterval, clockCounter;

function showInteractions(arrOfJson, target, old) {
	
	if (arrOfJson.length === 0) { return []; }
	
	var loaded = 0, newOnes = 0;;

	do {
		
		var interaction, row, intMsg, intIcon, intImg, intTime, usn, smUid, smUSN, smType, timeAdded;
		
		if (old) {
			interaction = arrOfJson.shift();
		} else {
			interaction = arrOfJson.pop();
		}
		
		smUid = interaction.sm_user_id; smUSN = interaction.sm_user_screen_name; smType = interaction.authKeyType;
		
		usn = "<a href='https://" + smType + ".com/" + smUSN + "' target='_blank'>@" + smUSN + "</a>";

		row = $("<div></div>").attr("id", "row_" + arrOfJson.length).addClass("smInteractionRow");

		intImg = $("<img/>").addClass("img-responsive").attr("src", "/images/map/" + smType + "_icon.png");
		switch (interaction.type) {
			case "follow_out":
				intIcon = $("<i></i>").addClass("fa fa-hand-o-right");
				intMsg = $("<div>Followed " + usn + "</div>").addClass("smInteraction-message");
				timeAdded = interaction.added_at;
				break;
			case "follow_in":
				intIcon = $("<i></i>").addClass("fa fa-hand-o-left");
				intMsg = $("<div>" + usn + " is following you</div>").addClass("smInteraction-message");
				timeAdded = interaction.added_at;
				break;
			case "message_out":	
				intIcon = $("<i></i>").addClass("fa fa-comments");
				intMsg = $("<div>Messaged " + usn + "</div>").addClass("smInteraction-message");
				//timeAdded = interaction.message_added_at;
				timeAdded = interaction.added_at;
				break;
			case "message_in":
				intIcon = $("<i></i>").addClass("fa fa-comments");
				intMsg = $("<div>" + usn + " replied to your message</div>").addClass("smInteraction-message");
				//timeAdded = interaction.message_added_at;
				timeAdded = interaction.added_at;
				break;
			case "favourite_out":
				intIcon = $("<i></i>").addClass("fa fa-star");
				intMsg = $("<div>Favourited " + usn + "'s message</div>").addClass("smInteraction-message");
				timeAdded = interaction.added_at;
				break;
			case "favourite_in":
				intIcon = $("<i></i>").addClass("fa fa-star");
				intMsg = $("<div>" + usn + " favourited your message</div>").addClass("smInteraction-message");
				timeAdded = interaction.added_at;
				break;
			default:
				break;
		}
		intTime = $("<span>" + timeConverter(timeAdded) +  "</span>").addClass("smInteraction-time");
		
		row.append(intIcon);
		row.append(intImg);
		row.append(intMsg);
		row.append(intTime);
		
		row.addClass("activate");
		
		if (timeAdded >= lastActionTime && interaction.type.indexOf("_out") === -1) {
			row.addClass("alert-info");
			newOnes++;
		}

		row.click({ a: smUid, b: smUSN, c: smType, x: "#row_" + arrOfJson.length }, clickThis);

		if (old) {
			$(target).append(row);
		} else {
			$(target).prepend(row);
		}
		
	} while(++loaded < loadMaxOld && arrOfJson.length > 0);
	
	/*if (newOnes) {
		$("#wizard-menu-smInteraction .badge").html((parseInt($("#wizard-menu-smInteraction .badge").html()) + newOnes));
		//$("#wizard-menu-smInteraction .badge").html(cnt);
	}
	if (parseInt($("#wizard-menu-smInteraction .badge").html()) > 0) {
		$("#wizard-menu-smInteraction .badge").addClass("alert-danger");
	} else {
		$("#wizard-menu-smInteraction .badge").removeClass("alert-danger");
	}*/
	
	return arrOfJson;
	
}

function clickThis(ev) {
	getConversation(ev.data.a, ev.data.b, ev.data.c);
	var badges = parseInt($("#wizard-menu-smInteraction .badge").html());
	if (badges > 0) { $("#wizard-menu-smInteraction .badge").html((badges - 1)); }
	if ($(ev.data.x).hasClass("alert-info")) { 
		$(ev.data.x).removeClass("alert-info");
		$("#wizard-menu-smInteraction .badge").html((parseInt($("#wizard-menu-smInteraction .badge").html()) - 1));
		if (parseInt($("#wizard-menu-smInteraction .badge").html()) === 0) {
			$("#wizard-menu-smInteraction .badge").removeClass("alert-danger");
		}
	}
}

function getConversation(smUid, smUSN, smType) {
	
	var data = { userId: _oUserId, smUid: smUid, sortType: 'asc', type: 'message' };
	
	$.ajax({
		async: true,
		type: "POST",
		cache: false,
		dataType: "json",
		url: "/ajax/socialMedia/getInteraction",
		data: data
	}).done(function(msg) { if (msg instanceof Array && msg.length > 0) { setupConversation(msg, smUSN, smType); } });
	
}

function setupConversation(arrOfJson, intUSN, smType) {
	
	if (arrOfJson.length === 0) { return; }
	
	$("#conversation-view").remove();

	var usn = "<a href='https://" + smType + ".com/" + intUSN + "' target='_blank'>@" + intUSN + "</a>";

	var intDiv = $("<div></div>").attr("id", "conversation-view").addClass("modal fade in").attr("style", "display: block").attr("aria-hidden", "false")
	.append(
		$("<div></div").addClass("modal-dialog")
		.append(
			$("<div></div").addClass("modal-content")
			.append(
				$("<div></div").addClass("modal-header")
				.append(
					$("<button></button>").attr("type", "button").addClass("close").click(function() { $("#conversation-view").modal("hide"); })
					.append(
						$("<span></span>").attr("aria-hidden", "true").html("x")
					)
					.append(
						$("<span></span>").addClass("sr-only").html("Close")
					)
				)
				.append(
					$("<h4></h4>").addClass("modal-title").html("Conversation with " + usn + "")
				)
			)
			.append(
				$("<div></div").addClass("modal-body")
			)
		)
	);

	var intDivInner = intDiv.find(".modal-body");
	
	var row = $("<div></div>").addClass("message message-sent")
	.append($("<span>" + usn + " - " + timeConverter(arrOfJson[0].original_message_added_at) + "</span>"))
	.append($("<p>" + arrOfJson[0].original_message + "</p>"))
	.appendTo(intDivInner);
	
	do {
		
		var interaction = arrOfJson.shift();
		
		var row = $("<div></div>").addClass("message");
		var intMsg = $("<p>" + Autolinker.link(interaction.message, { twitter: false }) + "</p>");
		
		if (parseInt(interaction.favourited) === 1) {
			intMsg.append($("<i></i>").addClass("fa fa-star").attr("style", "color: yellow; float: right; font-size: 16px;").attr("title", "Favourited"));
		}
		
		switch (interaction.type) {
			case "message_in":
				row.append($("<span></span>").append(usn + " - " + timeConverter(interaction.message_added_at)));
				row.append(intMsg);
				row.addClass("message-sent");
				break;
			case "message_out":
				row.append($("<span></span>").attr("style", "display: block").append("You - " + timeConverter(interaction.message_added_at)));
				row.append(intMsg);
				row.append($("<div></div>").addClass("clearfix"));
				row.addClass("message-received");
				break;
			default:
				break;
		}

		intDivInner.append(row);
		
	} while(arrOfJson.length > 0);
	
	intDivInner
	.append($("<br>"))
	.append(
		$("<label></label>").addClass("control-label").append("Reply: (Characters left: <span id='countReplyChars'>Unlimited</span>)")
	)
	.append(
		$("<textarea></textarea>").append("@" + intUSN + " ").addClass("form-control").attr("rows", 3).attr("id", "replyText").on("input change", function() {
			var $el = $(this);
			if (smType === "twitter") {
				if ($el.val().length > max_length) { $el.val($el.val().substr(0, max_length)); } else {}
				$("#countReplyChars").html(max_length - $el.val().length);
			} else {}
		})
	)
	.append(
		$("<button></button>").append($("<i></i>").addClass("fa fa-reply")).append("&nbsp;Reply").attr("type", "button").addClass("btn btn-primary").attr("style", "margin-right: 5px").click(function() {
			var data = {
				oUserId: _oUserId,
				smType: interaction.authKeyType,
				smId: interaction.authKeyId,
				message: $("#replyText").val(),
				usn: intUSN,
				uId: interaction.sm_user_id,
				mId: interaction.message_id, 
				origMsg: interaction.message,
				origMsgDate: interaction.message_added_at,
				fromInteraction: true
			};
			$.ajax({
				async: true,
				type: "POST",
				cache: false,
				dataType: "json",
				url: "/ajax/socialMedia/sendReply",
				data: data
			}).done(function(msg) { 
				$("#conversation-view").modal("hide");
				var message, status;
				if (msg === true) {
					status = "success";
					message = "Message has been successfully sent!";
				} else {
					status = "danger";
					message = "Message has not been sent. Reason: " + msg + ".";// Please contact Support for assistance.";
				}
				displayAlert(status, message);
			});
		})
	)
	.append(
		$("<button></button>").append($("<i></i>").addClass("fa fa-" + smType)).append("&nbsp;Follow").attr("type", "button").addClass("btn btn-primary").attr("style", "margin-right: 5px").click(function() {
			if (confirm("Are you sure you want to follow @" + intUSN + "?") === true) {
				var data = { oUserId: _oUserId, userId: interaction.sm_user_id, screen_name: intUSN, authKeyId: interaction.authKeyId , smType: smType };
				$.ajax({
					async: true,
					type: "POST",
					cache: false,
					dataType: "json",
					url: "/ajax/socialMedia/follow",
					data: data
				}).done(function(msg) {
					$("#conversation-view").modal("hide");
					var message, status;
					if (msg === true) {
						status = "success";
						message = "User has been successfully followed!";
					} else {
						status = "danger";
						message = "User has not been successfully followed. Reason: " + msg + ".";// Please contact Support for assistance.";
					}
					displayAlert(status, message);
				});
			}
		})
	)
	.append(
		$("<button></button>").append("Cancel").attr("type", "button").addClass("btn btn-default").click(function() { $("#conversation-view").modal("hide"); })
	);
	
	intDiv.find(".modal-body").append(intDivInner);
	
	$("body").append(intDiv);
	
	if (smType === "twitter") {
		$("#countReplyChars").html(max_length - intUSN.length - 2); // 2 chars are the @ before and space after the screen_name
	}
	
	$("#conversation-view textarea").focusTextToEnd();
	
	$("#conversation-view").modal();

}

function getInteraction() {
	
	var time = Math.round(new Date().getTime() / 1000) - intervalSec,
		data = { userId: _oUserId, since: time };
			
	$.ajax({
		async: true,
		type: "POST",
		cache: false,
		dataType: "json",
		url: "/ajax/socialMedia/getInteraction",
		data: data
	}).done(function(msg) { 
		if (msg instanceof Array) {
			if (msg.length > 0) {
				showInteractions(msg, interactionDiv);
			}
		} else {
			console.log(msg);
		}
	}).complete(function() {
		startClock(true);
	});
	
}

function startClock(reset) {
	
	if (reset === true) {
		clockInterval = setInterval(startClock, 1000);
		clockCounter = intervalSec;
	}
	
	$(".radial-progress").attr("data-progress", clockCounter--);
	
	if (clockCounter === 0) { 
		clearInterval(clockInterval);
	}
	
}

function displayAlert(status, message) {
	
	var alert = "\
		<div class='container msgJs'>\
			<div class='row'>\
				<div class='col-md-12'>\
					<div id='message-container'>\
						<div class='alert alert-" + status + "'>\
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>\
							" + message + "\
						</div>\
					</div>\
				</div>\
			</div>\
		</div>";
	$(alert).insertBefore($("#smInteraction-container"));
	
}

$("#interactionsSearchBox").keyup(function() {
	var query = $(this).val().toLowerCase();
	$(".smInteractionRow").each(function() {
		var $el = $(this), currentText = $el.html().toLowerCase();
		if (query.length > 0) {
			if (currentText.indexOf(query) > 0) {
				$el.show();
			} else {
				$el.hide();
			}
		} else {
			$el.show();
		}
	});
});

$("#loadMoreRows").click(function() {
	intResInitial = showInteractions(intResInitial, interactionDiv, true);
	if (intResInitial.length === 0) { $("#loadMoreRows").parent().remove(); }
});

setInterval(function() {
	getInteraction();
}, intervalSec * 1000);

intResInitial = showInteractions(intResInitial, interactionDiv, true);
if (intResInitial.length === 0) { $("#loadMoreRows").parent().remove(); }

startClock(true);