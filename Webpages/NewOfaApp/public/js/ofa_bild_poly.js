// ÄÖÜ äöü
var map = null;
var image = null;
var canvas = null;
var ctx = null;
var image = null;
var degrees = 0;

$(window).load(function() {
    var latlngs = new Array();
    var coords = JSON.parse(polygon);
    console.log(JSON.stringify(coords));

    for (var i = 0; i < coords.length; i++) {
        latlngs.push(new google.maps.LatLng(coords[i][1], coords[i][0]));
    }

    console.log(latlngs);

    var latlng = new google.maps.LatLng(breite, laenge);
    var myOptions =
    {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.HYBRID
    };

    map = new google.maps.Map(document.getElementById("jr_map"), myOptions);

    myPolygon = new google.maps.Polygon({
        paths: latlngs,
        draggable: true, // turn off if it gets annoying
        editable: true,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.1
    });

    myPolygon.setMap(map);
    google.maps.event.addListener(myPolygon.getPath(), "insert_at", getPolygonCoords);
    google.maps.event.addListener(myPolygon.getPath(), "set_at", getPolygonCoords);

    canvas = document.getElementById("canvas"); // $("#canvas");
    ctx = canvas.getContext("2d");

    image = document.createElement("img");

    image.onload = function() {
        console.log("w=" + image.width + " / h=" + image.height);
        ctx.drawImage(image, canvas.width/2-image.width * 0.7/2, canvas.height/2-image.height * 0.7/2, image.width * 0.7, image.height * 0.7);
    }
    
    image.src = img_src;
});

function drawRotated(angle){
    degrees += angle;

    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.save();
    ctx.translate(canvas.width/2, canvas.height/2);
    ctx.rotate(degrees*Math.PI/180);
    ctx.drawImage(image,-image.width * 0.7/2,-image.height * 0.7/2, image.width * 0.7, image.height * 0.7);
    ctx.restore();
}

//Display Coordinates below map
function getPolygonCoords() {
    var len = myPolygon.getPath().getLength();
    var json = "";

    for (var i = 0; i < len; i++) {
        json += "[" + myPolygon.getPath().getAt(i).lng().toFixed(5) + "," + myPolygon.getPath().getAt(i).lat().toFixed(5) + "],";
    }

    json = "[" + json.slice(0, -1) + "]";

    document.getElementById('info').innerHTML = json;

    return json;
}

function saveToDatabase() {
    var polyjson = getPolygonCoords();

    $.ajax({
        url : "/inc/ofa_UpdateBilddaten.php?bildnr=" + bildnr + "&polygon=" + polyjson
    }).done(function(msg)
    {
        document.getElementById('info').innerHTML = polyjson + " saved.";
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });

}
