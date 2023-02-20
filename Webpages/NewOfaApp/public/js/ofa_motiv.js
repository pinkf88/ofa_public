$("tr.firstline").hover(function()
{
    $(this).css("background", "lightgrey");
}, function()
{
    $(this).css("background", "");
});

$(document).ready(function() 
{
    $('div.motiv_map').height($(window).height() - 245);
    motiv_createMap();
});

var motiv_map = null;
var motiv_marker = null;

function motiv_createMap()
{
    var welt = new google.maps.LatLng(48.13, 11.57);

    var myOptions = {
        zoom:               5,
        center:             welt,
        mapTypeId:          google.maps.MapTypeId.HYBRID,
        overviewMapControl: true,
        overviewMapControlOptions: {
            opened: true
        }
    };

    motiv_map = new google.maps.Map(document.getElementById("motiv_map"), myOptions);

    image = new google.maps.MarkerImage("https://www.juergen-reichmann.de/pics/mm_20_red.png", new google.maps.Size(12,
            20), new google.maps.Point(0, 0), new google.maps.Point(6, 20));

    shadow = new google.maps.MarkerImage("https://www.juergen-reichmann.de/pics/mm_20_shadow.png", new google.maps.Size(
            22, 20), new google.maps.Point(0, 0), new google.maps.Point(6, 20));

    google.maps.event.addListener(motiv_map, "center_changed", function()
    {
        var latlng = motiv_map.getCenter();
        // updateKoordinaten(latlng);
        motiv_marker.setPosition(latlng);
    });

    motiv_marker = new google.maps.Marker({
        position:   welt,
        map:        motiv_map,
        shadow:     shadow,
        icon:       image,
        clickable:  true
    });

    geocoder = new google.maps.Geocoder();

    google.maps.event.addListener(motiv_marker, "click", function()
    {
        if (geocoder) {
            latlng = motiv_marker.getPosition();

            geocoder.geocode({
                'latLng' : latlng
            }, function(results, status) {
                var content = "";

                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        content = results[0].formatted_address;
                    }

                    if (results[1]) {
                        content = content + "<br>" + results[1].formatted_address;
                    }

                    if (results[2]) {
                        content = content + "<br>" + results[2].formatted_address;
                    }
                } else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
                    content = "Keine weiteren Geoinformationen verfügbar.";
                } else {
                    content = "Keine weiteren Geoinformationen verfügbar.";
                }

                infowindow = new google.maps.InfoWindow({
                    content : "<div class='infowindow'>" + content + "</div>"
                });

                infowindow.open(motiv_map, motiv_marker);
            });
        }
    });
}

function motiv_mapGotoCity(laenge, breite)
{
    motiv_map.setCenter(new google.maps.LatLng(breite, laenge));
    motiv_map.setZoom(13);
}

function motiv_getCoordinates(motivid)
{
    var latlng = motiv_map.getCenter();

    $("#lng_" + motivid).html(runden(latlng.lng(), 4));
    $("#lat_" + motivid).html(runden(latlng.lat(), 4));

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_UpdateMotiv.php?id=" + motivid + "&laenge=" + Math.round(latlng.lng() * 10000)
                + "&breite=" + Math.round(latlng.lat() * 10000)
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}
