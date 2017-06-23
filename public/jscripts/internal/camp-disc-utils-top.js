//maps array filled with map handles and ids
window["maps_json"] = {};

window["distinctMapsComplete"] = [];

$(document).ready(function() {
	//try { $("#job-table tbody tr:first").click(); } catch (e) {}
	//$("#loader-element.map-loader").fadeOut("slow");
	$("body").on("messagesInitialComplete", function(ev) {
		if (window["distinctMapsComplete"].indexOf(ev.id) === -1) {
			window["distinctMapsComplete"].push(ev.id);
			//if (window["distinctMapsComplete"].length === $("#job-table tbody tr").length) {
			var doItNow = false;
			if ($("#job-table tbody tr").length > 4) { 
				if (window["distinctMapsComplete"].length >= 1) {
					doItNow = true;
				}
			}
			else {
				if (window["distinctMapsComplete"].length >= 1) {
					doItNow = true;
				} 
			}
			if (doItNow === true) {
				$("#job-table tbody tr:first").click();
				$("#loader-element.map-loader").fadeOut("slow");
			}
			if (window["distinctMapsComplete"].length === $("#job-table tbody tr").length) {
				$("#loaderAnimatedIcon").remove();
			}
		}
	});
	window["accountsSelectOptions"] = $("#replyToMsg select[name=authKeyId]").html();
});

// draws circle around pin on editor map
function DrawCircle() {
	
	var rad = $("input[name=radius]").val();
	
    rad *= 1637; // convert to meters if in Miles
	
    if (window["centre_circle"] !== null) {
        window["centre_circle"].setMap(null);
    }
	
    window["centre_circle"] = new google.maps.Circle({
        center: window["centre_marker"].getPosition(),
        radius: rad,
        strokeColor: "#0000FF",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#0000FF",
        fillOpacity: 0.35,
        map: window["map"]
    });
	
	window["centre_circle"].setVisible(true);
	
}

function getLocationGoogle(query, e) {
	var charCode = -1;
	if (e.which || e.keyCode) { 
		charCode = (typeof e.which === "number") ? e.which : e.keyCode;
		e.preventDefault();
	}
	if (charCode === 13 || e === "fromButton") {
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({ address: query }, function(results, status) {
			if (status === google.maps.GeocoderStatus.OK) {
				if (results[0]) {
					var lat = results[0].geometry.location.lat();
					var lng = results[0].geometry.location.lng();
					var gc = new google.maps.LatLng(lat, lng);
					window["map"].panTo(gc);
					window["centre_marker"].setVisible(false);
					window["centre_marker"].setPosition(gc);
					window["centre_marker"].setVisible(true);
					DrawCircle();
					$("#centre_lat").val(gc.lat()); $("#centre_lng").val(gc.lng());
					$("form#editor").valid();
					$("#googleLocationSearch").val("");
				}
			}
		});
	}
	return false;
}