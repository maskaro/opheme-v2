$(".flipper").height(400); //hack to make the container have the expected height, without this it has 0px height

$("#login-button").click(function() {
	$(".flipper").animate({ height: "400px" }, "fast"); //hack to make the container have the expected height, without this it has 0px height
	$("#flip-toggle").toggleClass("flip");
	$.scrollTo(0, 400);
	setTimeout("$('#terms-content').fadeOut();",100);
	if ($("#forgot .panel-body").is(":visible")) { $("#forgot .panel-body").hide(); }
});

$("#register-button").click(function() {
	$(".flipper").animate({ height: "500px" }, "fast"); //hack to make the container have the expected height, without this it has 0px height
	$("#flip-toggle").toggleClass("flip");
	$.scrollTo(0, 400);
});

if (window.location.href.indexOf("register") >= 0) { $("#register-button").click(); }

$("#terms-button").click(function() {
	$("#terms-content").fadeToggle();
	$.scrollTo("#terms-content", 400);
});

$(".back-up").click(function() {
	$.scrollTo(0, 400);
	setTimeout("$('#terms-content').fadeToggle();",200);
});

$("form#login").validate({
	rules: {
		email: {
			required: true,
			email: true
		},
		password: "required"
	},
	messages: {
		email: "Please enter a valid email address.",
		password: "Please enter your password."
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
		} else {
			error.insertAfter(element);
		}
	}
});

$("form#forgot").validate({
	rules: {
		email: {
			required: true,
			email: true
		}
	},
	messages: {
		email: "Please enter a valid email address."
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
		} else {
			error.insertAfter(element);
		}
	}
});

$("form#register").validate({
	rules: {
		//token: "required",
		email: {
			required: true,
			email: true
		},
		firstName: "required",
		password: "required",
		"confirm-password": {
			required: true,
			equalTo: "#password"
		},
		terms: "required"
		//captcha_code: "required"
	},
	messages: {
		//token: "Please enter the Secret Token given to you.",
		email: "Please enter a valid email address.",
		firstName: "Please enter your First Name so we can better presonalise your experience.",
		password: "Please enter your password.",
		"confirm-password": {
			required: "Please enter your password again.",
			equalTo: "Please enter same password as above."
		},
		terms: "You must agree to our Terms and Conditions."
		//captcha_code: "Please enter the captcha code as seen in the image below."
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
		} else {
			error.insertAfter(element);
		}
	}
});

if ($(".msgs-container").length) { $("form input:not([type=submit])").on("focus", function() { setTimeout(function() { $(".msgs-container").remove(); }, 5000); }); }

/*function refreshCaptcha() {
	var img = document.images["captchaimg"];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}*/


/* ==========================================================================
   Logo Animation Setup
   ========================================================================== */

function pathPrepare ($el) {
	var lineLength = $el[0].getTotalLength();
	$el.css("stroke-dasharray", lineLength);
	$el.css("stroke-dashoffset", lineLength);
}

var $pin = $("path#pin");
var $opheme = $("path#letters");
var $introSub = $("#intro-sub");

// prepare SVG
//pathPrepare($pin);
pathPrepare($opheme);

// init controller
var controller = new ScrollMagic.Controller();

var tlLogo = new TimelineMax();


tlLogo.set($pin, {scale:0})
	.set($introSub, {transform: "translateY(100px)", opacity:0})
	.to($pin, 1, {scale:1, ease:Expo.easeOut})
	.to($opheme, 5, {strokeDashoffset: 0, ease:Linear.easeIn}, 0.25)
	.to($opheme, 0.25, {attr:{"fill-opacity":1}, ease:Linear.easeIn}, 1.2)
	.to($pin, 0.25, {attr:{"stroke-width":0}, ease:Linear.easeIn}, 1.2)
	.to($opheme, 0.5, {attr:{"stroke-width":0}, ease:Linear.easeIn}, 2.4)
	.to($introSub, 0.2, {transform: "translateY(0)", opacity:1, ease:Linear.easeIn}, 1.95);