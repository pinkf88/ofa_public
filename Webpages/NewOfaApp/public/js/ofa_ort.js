$("tr.firstline").hover(function()
{
    $(this).css("background", "lightgrey");
}, function()
{
    $(this).css("background", "");
});

$(document).ready(function() 
{
    $('div.ort_map').height($(window).height() - 245);
    ort_createMap();
});

function ort_getCoordinates(ortid, ort)
{
    $.getJSON(
            'http://www.whateverorigin.org/get?url='
                    + encodeURIComponent('http://de.wikipedia.org/wiki/' + ort.replace(/ /g, '_')) + '&callback=?',
            function(data)
            {
                data = data.contents;

                var ortlaenge = 0.0, ortbreite = 0.0;

                var lat = data.substr(data.indexOf('<span class="latitude">'));
                lat = lat.substring(23, lat.indexOf('</span>'));

                if (lat.indexOf('.') >= 0)
                {
                    ortbreite = parseFloat(lat);
                }

                var lng = data.substr(data.indexOf('<span class="longitude">'));
                lng = lng.substring(24, lng.indexOf('</span>'));

                if (lng.indexOf('.') >= 0)
                {
                    ortlaenge = parseFloat(lng);
                }

                if (ortlaenge != 0.0 && ortbreite != 0.0)
                {
                    // window.console.info('Länge: ' + ortlaenge + ' /
                    // Breite: ' + ortbreite);
                    $("#ort_mapgoto_" + ortid).html(
                            '<a href="javascript:ort_mapGotoCoordinates(' + runden(ortlaenge, 2) + ', '
                                    + runden(ortbreite, 2) + ');">Map</a>');

                    $("#lng_" + ortid).html('<i>' + runden(ortlaenge, 2) + '</i>');
                    $("#lat_" + ortid).html('<i>' + runden(ortbreite, 2) + '</i>');

                    $.ajax({
                        dataType : "json",
                        url : "/inc/ofa_UpdateOrt.php?id=" + ortid + "&laenge=" + Math.round(ortlaenge * 100)
                                + "&breite=" + Math.round(ortbreite * 100)
                    }).done(function(data)
                    {
                    }).fail(function(jqXHR, textStatus)
                    {
                        console.log("Database access failed: " + textStatus);
                    });
                }
                else
                {
                    alert("Keine Geokoordinaten für " + ort + " verfügbar.");
                }
            }).fail(function(jqXHR, textStatus, errorThrown)
    {
        alert("Keine Geokoordinaten für " + ort + " verfügbar [ " + textStatus + " / " + errorThrown + "].");
    });

    /*
     * $ .getJSON(
     * "http://de.wikipedia.org/w/api.php?action=query&prop=revisions&rvprop=content&indexpageids=true&titles=" +
     * ort + "&format=json&callback=?", function(data) { var page, pageids,
     * pages, revisions, id = 0; var query = data.query; var ortlaenge = 0.0,
     * ortbreite = 0.0;
     *
     * if (query) { pageids = query.pageids;
     *
     * if (pageids) { id = pageids[0]; }
     *
     * pages = query.pages;
     *
     * if (pages) { page = pages[id];
     *
     * if (page) { revisions = page.revisions;
     *
     * if (revisions) { var matches = revisions[0]['*']
     * .match(/\|\s+(Breitengrad)\s+=\s+(.+)\n/);
     *
     * if (matches) { ortbreite = parseFloat(matches[2]); }
     *
     * matches = revisions[0]['*'] .match(/\|\s+(Längengrad)\s+=\s+(.+)\n/);
     *
     * if (matches) { ortlaenge = parseFloat(matches[2]); } } } } }
     *
     * if (ortlaenge != 0.0 && ortbreite != 0.0) { //
     * window.console.info('Länge: ' + ortlaenge + ' / // Breite: ' +
     * ortbreite); $("#lng_" + ortid).html(runden(ortlaenge, 2)); $("#lat_" +
     * ortid).html(runden(ortbreite, 2)); } else { alert("Keine Geokoordinaten
     * für " + ort + " verfügbar."); } });
     */
}

var ort_map = null;
var ort_marker = null;

function ort_createMap()
{
    var welt = new google.maps.LatLng(48.13, 11.57);
    var myOptions = {
        zoom : 5,
        center : welt,
        mapTypeId : google.maps.MapTypeId.HYBRID,
        overviewMapControl : true,
        overviewMapControlOptions : {
            opened : true
        }
    };

    ort_map = new google.maps.Map(document.getElementById("ort_map"), myOptions);

    image = new google.maps.MarkerImage("https://www.juergen-reichmann.de/pics/mm_20_red.png", new google.maps.Size(12,
            20), new google.maps.Point(0, 0), new google.maps.Point(6, 20));

    shadow = new google.maps.MarkerImage("https://www.juergen-reichmann.de/pics/mm_20_shadow.png", new google.maps.Size(
            22, 20), new google.maps.Point(0, 0), new google.maps.Point(6, 20));

    ort_marker = new google.maps.Marker({
        // position : welt,
        map : ort_map,
        shadow : shadow,
        icon : image,
        clickable : true
    });
}

function ort_mapGotoCoordinates(laenge, breite)
{
    ort_map.setCenter(new google.maps.LatLng(breite, laenge));
    ort_map.setZoom(13);
    ort_marker.setPosition(new google.maps.LatLng(breite, laenge));
}
