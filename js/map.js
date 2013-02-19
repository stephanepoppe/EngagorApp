

infos = [];


$.ajax({
	url: "mentions.php",
	cache: false,
	dataType: "json",
	
}).done(function(data) {

	
	var mapOptions = {
	  zoom: 8,
	  center: new google.maps.LatLng(data[0].location.latitude, data[0].location.longitude),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(document.getElementById("map"), mapOptions);

	
	for (var key in data) {
		var icon;
				
		// Content infowindows
		var contentString = "<div id=\"infow\">";

			
		if (data[key].message.title != null){
			contentString += "<h5>"+ data[key].message.title + "</h5>";
		}
		
		// Message
		contentString += "<p>";
		
		if (data[key].author != null){
			if (data[key].author.name != null){
				contentString += "Posted by: " + data[key].author.name + " (<a href="+ data[key].author.url +">Profile</a>)" ;
			}
			if (data[key].author.type == "SearchProviderFoursquare"){
				contentString += "Source: Foursquare";
			}
			
		}
		
		if (data[key].message.content != null){
			contentString += "<h6>Content</h6><p>" + data[key].message.content + "</p>"
		}
		
		// end Message
		contentString += "</p>";
		
		//  urls
		if (data[key].permalink !=  null){
			contentString += "<a role=\"button\" class=\"btn\" href="+ data[key].permalink + ">view @ Engagor</a>		"
		}
			    		
		contentString += "</div>"
		
		if (data[key].source.type == "SearchProviderFoursquare"){
			icon = "img/foursqicon.ico";
		}
							
		
		// Create a marker
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(data[key].location.latitude, data[key].location.longitude), 
		  	map: map,
		  	content: contentString,
		  	icon: icon
		});	
		
		
		// Multiple infowindows source: http://www.codefx.biz/2011/01/google-maps-api-v3-multiple-infowindows
							
		// Eventlistener infowindows
		google.maps.event.addListener(marker, 'click', function() {
		
			/* close the previous info-window */
			closeInfos();
			
			/* the marker's content gets attached to the info-window: */
			var info = new google.maps.InfoWindow({content: this.content});
			
			/* trigger the infobox's open function */
			info.open(map,this);
			
			/* keep the handle, in order to close it on next click event */
			 infos[0]=info;
		
		  	//infowindow.open(map,marker);
		});
			
	}
});


function closeInfos(){

if(infos.length > 0){

  /* detach the info-window from the marker ... undocumented in the API docs */
  infos[0].set("marker", null);

  /* and close it */
  infos[0].close();

  /* blank the array */
  infos.length = 0;
}
}



function loadScript() {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyAjJNWOip3T_HFpH5QUdhBZbfT_uiyiYQ8&sensor=false&callback=initialize";
	document.body.appendChild(script);
}

function initialize() {

}

window.onload = loadScript;