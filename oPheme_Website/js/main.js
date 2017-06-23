function pathPrepare ($el) {
	var lineLength = $el[0].getTotalLength();
	$el.css("stroke-dasharray", lineLength);
	$el.css("stroke-dashoffset", lineLength);
}

/* ==========================================================================
   Landing Section Animation Setup
   ========================================================================== */

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

/* ==========================================================================
   Features Section Animation Setup
   ========================================================================== */

// build tween
var tweenFeatures = TweenMax.staggerFromTo(".feature", 2, {scale: 0}, {scale: 1, ease: Back.easeOut}, 0.5);

// build scene
var sceneFeatures = new ScrollMagic.Scene({triggerElement: "#intro-section", duration: 400, offset: 100})
.setTween(tweenFeatures)
.addTo(controller);

/* ==========================================================================
	Discovery Animation
   ========================================================================== */
var discoveryTl = new TimelineMax();

var discoveryTween = discoveryTl//.set(".frame", {autoAlpha: 0})
	.from("#frame-1", 0.5, {scale: 0.5, ease:Back.easeIn})
	.to("#frame-1", 0.5, {scale: 0, opacity: 0, ease:Back.easeOut}, "+=1")
	.from("#frame-2", 0.5, {scale: 3, autoAlpha: 0, ease:Back.easeIn})
	.to("#frame-2", 0.5, {scale: 0, autoAlpha: 0, ease:Back.easeOut}, "+=1")
	.from("#frame-3", 0.5, {scale: 3, autoAlpha: 0, ease:Back.easeIn})
	.to("#frame-3", 0.5, {left: 1500, autoAlpha: 0, ease:Back.easeOut}, "+=1")
	.from("#frame-4", 0.5, {left: -1500, autoAlpha: 0, ease:Back.easeIn})
	.to("#frame-4", 0.5, {left: 1500, ease:Back.easeOut}, "+=1.5")
	.from("#frame-5", 0.5, {left: -1500, autoAlpha: 0, ease:Back.easeIn})
	.to("#frame-5", 1, {top: -1500, ease:Back.easeOut}, "+=2")
	.from("#frame-6", 0.5, {top: 1500, autoAlpha: 0, ease:Back.easeIn})
	.from("#frame-7", 1, {scale: 50, autoAlpha: 0, ease:Back.easeIn},"+=1")
	.from("#frame-8", 1, {left: -1500, ease:Back.easeIn},"+=1")
	.to("#frame-8", 1, {left: -1500, ease:Back.easeOut},"+=2")
	.from("#frame-9", 0.5, {scale: 0, opacity: 0, ease:Back.easeInOut})
	.to("#frame-9", 0.5, {scale: 0, opacity: 1, ease:Back.easeOut},"+=0.5")
	.from("#frame-10", 0.5, {scale: 0, opacity: 0, ease:Back.easeInOut})
	.to("#frame-10", 0.5, {scale: 0, opacity: 1, ease:Back.easeOut},"+=0.5")
	.from("#frame-11", 0.5, {scale: 0, opacity: 0, ease:Back.easeInOut})
	.to("#frame-11", 0.5, {scale: 0, opacity: 1, ease:Back.easeOut},"+=0.5")
	.from("#frame-12", 0.5, {scale: 0, opacity: 0, ease:Back.easeInOut})
	.to("#frame-12", 0.5, {scale: 0, opacity: 1, ease:Back.easeOut},"+=0.5")
	.from("#frame-13", 0.5, {scale: 0, opacity: 0, ease:Back.easeInOut})
	.from("#frame-14", 1, {left: -1500, ease:Back.easeIn}, "+=1")
	.to("#frame-14", 1, {left: -1500, ease:Back.easeOut}, "+=2")
	.from("#frame-15", 1, {opacity: 0, ease:Back.easeInOut})
	.from("#frame-16", 1, {top: 1500, ease:Back.easeIn}, "+=2")
	.from("#frame-17", 1, {top: -1500, ease:Back.easeIn}, "+=2");

var sceneDiscovery = new ScrollMagic.Scene({triggerElement: "#discovery"})
	.setTween(discoveryTween)
	.addTo(controller);

$("#replay-discovery").on('click', function() {
	discoveryTl.restart();
});

/* ==========================================================================
	Map
   ========================================================================== */




var tlMap = new TimelineMax();

var tweenMap =  tlMap.set("#discover-pins", {opacity: 0, scale: 100})
				.set(".animated-pins", {scale: 0})
				.set("#replay-animation", {opacity: 0})
				.to("#discover-pins", 0.35, {opacity: 1, scale: 1, ease: Circ.easeInOut})
				.to("#discover-pins", 0.2, {scale: 1.2})
				.to("#discover-pins", 0.2, {scale: 1})
				.to("#pin-1", 0.2, {scale: 1, opacity: 1}, 1.5)
				.to("#pin-1", 0.2, {opacity: 0}, "+=1.5")
				.to("#pin-2", 0.2, {scale: 1, opacity: 1}, "+=0.1")
				.to("#pin-2", 0.2, {opacity: 0}, "+=1.4")
				.to("#pin-3", 0.2, {scale: 1, opacity: 1}, "+=0.1")
				.to("#pin-3", 0.2, {opacity: 0}, "+=1.3")
				.to("#pin-4", 0.2, {scale: 1, opacity: 1}, "+=0.1")
				.to("#pin-4", 0.2, {opacity: 0}, "+=1.2")
				.to("#pin-5", 0.2, {scale: 1, opacity: 1}, "+=0.1")
				.to("#replay-animation", 0.1, {opacity: 1}, "+=1.5")
				.to("#replay-animation i", 0.4, {rotation: 720}, "+=0.2");


// build scene
var sceneMap = new ScrollMagic.Scene({triggerElement: "#discover-animation"})
				.setTween(tweenMap)
				.addTo(controller);

$("#replay-animation").on('click', function() {
	tlMap.restart();
});


/* ==========================================================================
   Platforms Section Animation Setup
   ========================================================================== */

// build tween
var tweenPlatformOphemeApp = TweenMax.fromTo("#try-opheme", 1,
			{left: -900},
			{left: 0, ease: Linear.easeInOut}
		);

// build scene
var scenePlatformOphemeApp = new ScrollMagic.Scene({triggerElement: "#platforms", duration: 200, offset: -100})
.setTween(tweenPlatformOphemeApp)
.addTo(controller);

// build tween
var tweenPlatformHootsuiteApp = TweenMax.fromTo("#try-hootsuite", 1,
			{right: -900},
			{right: 0, ease: Linear.easeInOut}
		);

// build scene
var scenePlatformHootsuiteApp = new ScrollMagic.Scene({triggerElement: "#platforms", duration: 200, offset: -100})
.setTween(tweenPlatformHootsuiteApp)
.addTo(controller);

// current window (page) scroll position
var pageScrollPosition;

$("#terms-toggle").on("click", function() {
	// save current window (page) scroll position
	pageScrollPosition = $(window).scrollTop();
	$("#page").hide();
	$("#terms-content").fadeIn("slow");
	$("#terms-content").scrollTop(0);
});

$("#terms-content").on("click", function(e) {
	// don't close if an anchor tag is clicked within
	if ($(e.target).is("a")) return;
	$("#terms-content").hide();
	$("#page").fadeIn("slow");
	// restore last window (page) scroll position
	$(window).scrollTop(pageScrollPosition);
});

/* ==========================================================================
   Contact Form Validation
   ========================================================================== */

$("form#form").validate({
	errorClass: "alert alert-error",
	validClass: "alert alert-success",
	//validation rules
	rules: {
		name: "required",
		company: "required",
		email: {
			required: true,
			email: true
		},
		message: "required"
	},
	messages: {
		name: "Please enter your full name.",
		company: "Please enter your company name.",
		email: "Please enter a valid email address.",
		message: "Please enter your message."
	}
});







