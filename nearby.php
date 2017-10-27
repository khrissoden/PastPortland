<?php   
//hooking up to the database and everything.
	session_start();
    include ("connect.php");
?>

<div id="search">
	<form id="searchForm">
		<label for="searchTarget"></label><input id="searchTarget" name="searchTarget" type="text" />
		<input type="submit" id="searchSubmit" value="Search" />
	</form>
	<div id="actionBox" style="background-color: yellow;">Searching....</div>
	<div id='mapID' style='height: 300px;'></div>
	<div id="searchResults">
	</div>
</div>


<script>
$(document).ready(function() {
	getLocation();
	$("#actionBox").hide();
	$("#searchTarget").focus();
	$("#searchSubmit").click(function() {
		$("#actionBox").show();
		var sInput = $("#searchForm").serialize();
		$.ajax({
			type: "POST",
			url: "searchSubmit.php",
			data: sInput
		}).done(function(data){
			$("#searchResults").html(data);
			$("#actionBox").hide();
		}); 
		$("#searchTarget").val("");
		$("#searchTarget").focus();
		return false;
	});	
	$("#mainContent").on('click', '.linkSearch', function(event) {
		event.preventDefault();
		$(".actionBox").show();
		var sInput = $(this).attr('id');
		$.ajax({
			type: "POST",
			url: "linkSearch.php",
			data: "searchTarget=" + sInput
		}).done(function(data){
			$("#searchResults").html(data);
			$(".actionBox").hide();
		}); 
		$("#searchTarget").focus();
		return false; 
	});
});

var x = document.getElementById("searchResults");

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
	var lat = position.coords.latitude;
	var lon = position.coords.longitude;
    x.innerHTML = "Searching...";	
	$.ajax({
			type: "POST",
			url: "searchNearby.php",
			data: {lat: lat, lon: lon}		
		}).done(function(data){
			showMap(data);
		});
}

function showMap(data) {
	
	var newInfo = JSON.parse(data);
	var lat = parseFloat(newInfo.currentLocation.lat);
	var lon = parseFloat(newInfo.currentLocation.lon);
	var newAddress = newInfo.addr955.newStreetNumber + " " + newInfo.addr955.newStreetName;
	
//	alert (typeof lat);

	function initMap() {
		var mapCenter = {lat: lat, lng: lon};
		
		var map = new google.maps.Map(document.getElementById('mapID'), {
		zoom: 18,
		center: mapCenter
		});	
		
		var marker1 = new google.maps.Marker({
			position: {lat: lat, lng: lon},
			map: map,
			title: newAddress
		});
		
		var buildingCoords = [
		{lat: 45.519506, lng: -122.650880 },
		{lat: 45.519506, lng: -122.650818 },
		{lat: 45.519430, lng: -122.650818 },
		{lat: 45.519430, lng: -122.650880 }
		];
		
		var buildingShape = new google.maps.Polygon({
			paths: buildingCoords,
			strokeColor: '#FF0000',
			strokeOpacity: 0.8,
			strokeWeight: 3,
			fillColor: '#FF0000',
			fillOpacity: 0.35,
			title: "Test building"
		});
		buildingShape.setMap(map);
	}	
	
	initMap();
	
	for (var key in newInfo) {
		if(newInfo.hasOwnProperty(key)) {
			console.log(newInfo[key]["lat"]);
		}
	}
	
	
	$("#searchResults").html(data);
}


</script>
<script async defer
	src='https://maps.googleapis.com/maps/api/js?key=AIzaSyCjqnUIHuq9oaxQs3owCfnTAb7edn15dnc&callback=initMap'>
</script>	