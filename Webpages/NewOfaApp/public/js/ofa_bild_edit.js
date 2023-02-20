var bildform_motive;


$(function() {
    var bildid = parseInt('0' + $("input[name*='id']").val());
    // console.log('bildid', bildid);

    var bildform_ortid = $("select[name*='ortid']").val();

    if (bildform_ortid > 0) {
        bild_getMotive(bildform_ortid);
    }

    $("select[name*='ortid']").change(function() {
        bildform_ortid = $("select[name*='ortid']").val();
        bild_getMotive(bildform_ortid);
    });

    $("input[name*='beschreibung']")
        .bind('keydown', function(event) {
            if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete('instance').menu.active) {
                event.preventDefault();
            }
        })
        .autocomplete(
        {
            minLength: 0,
            source: function(request, response) {
                // delegate back to autocomplete, but extract the last term
                response($.ui.autocomplete.filter(bildform_motive, extractLast(request.term)));
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function(event, ui) {
                var terms = split(this.value);
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push(ui.item.value);
                // add placeholder to get the comma-and-space at the end
                terms.push('');
                this.value = terms.join(' ');
                return false;
            }
        });

    $("textarea[name*='bemerkung']")
        .bind('keydown', function(event) {
            if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete('instance').menu.active) {
                event.preventDefault();
            }
        })
        .autocomplete(
        {
            minLength: 0,
            source: function(request, response) {
                // delegate back to autocomplete, but extract the last term
                response($.ui.autocomplete.filter(bildform_motive, extractLast(request.term)));
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function(event, ui) {
                var terms = split(this.value);
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push(ui.item.value);
                // add placeholder to get the comma-and-space at the end
                terms.push('');
                this.value = terms.join(' ');
                return false;
            }
        });

    if (bildid > 0) {
        var info1 = '';
        var info2 = '';
        var url = '/inc/ofa_GetBildEditInfo.php?id=' + bildid;
    
        $.ajax({
            dataType:   'json',
            url:        url
        }).done(function(data) {
            // console.log('data', data);
    
            info1 += data.geodaten + '\n';
            info1 += data.serieliste + '\n';
            info1 += data.dists + '\n';
            info2 += data.bilddaten + '\n';

            $('#bildinformation1').html(info1);
            $('#bildinformation2').html(info2);

            var polygon = JSON.parse(data.polygon);

            if (Math.abs(data.breite) < 0.1 && Math.abs(data.laenge) < 0.1 && polygon.length > 0) {
                var latlngs = [];

                for (var j = 0; j < polygon.length; j++) {
                    var latlng = {
                        latitude:   polygon[j][1],
                        longitude:  polygon[j][0]
                    };

                    latlngs.push(latlng);
                }

                var center_ll = window.geolib.getCenter(latlngs);
                data.breite = center_ll.latitude;
                data.laenge = center_ll.longitude;
            }

            if (Math.abs(data.breite) > 0.1 && Math.abs(data.laenge) > 0.1) {
                bild_createMap(data.breite, data.laenge, polygon);
            }
        }).fail(function(jqXHR, textStatus) {
            console.log("bild_showInformation(): Ajax access failed: ", url, textStatus);
        });
    }
});

function bild_getMotive(ortid)
{
    var url = '/inc/ofa_GetOrtMotive.php?ortid=' + ortid;

    $.ajax({
        dataType:   'json',
        url:        url
    }).done(function(data)
    {
        bildform_motive = data.motive;
    }).fail(function(jqXHR, textStatus)
    {
        console.log('bild_getMotive() Ajax Error', url, textStatus);
    });
}

var bild_map = null;
var bild_marker = null;

function bild_createMap(breite, laenge, polygon)
{
    var standort = new google.maps.LatLng(breite, laenge);

    var myOptions = {
        zoom:               14,
        center:             standort,
        mapTypeId:          google.maps.MapTypeId.HYBRID,
        overviewMapControl: true,
        heading:            0,
        tilt:               45,
        overviewMapControlOptions: {
            opened: true
        }
    };

    bild_map = new google.maps.Map(document.getElementById('bild_map'), myOptions);

    var image = new google.maps.MarkerImage('https://www.juergen-reichmann.de/pics/mm_20_red.png', new google.maps.Size(12,
            20), new google.maps.Point(0, 0), new google.maps.Point(6, 20));

    var shadow = new google.maps.MarkerImage('https://www.juergen-reichmann.de/pics/mm_20_shadow.png', new google.maps.Size(
            22, 20), new google.maps.Point(0, 0), new google.maps.Point(6, 20));

    bild_marker = new google.maps.Marker({
        position:   standort,
        map:        bild_map,
        shadow:     shadow,
        icon:       image,
        clickable:  true
    });

    if (polygon.length > 0) {
        var latlngs = [];
        // console.log(JSON.stringify(polygon));

        for (var i = 0; i < polygon.length; i++) {
            latlngs.push(new google.maps.LatLng(polygon[i][1], polygon[i][0]));
        }

        // console.log(latlngs);

        myPolygon = new google.maps.Polygon({
            paths:          latlngs,
            draggable:      false, // turn off if it gets annoying
            editable:       false,
            strokeColor:    '#FF0000',
            strokeOpacity:  0.8,
            strokeWeight:   2,
            fillColor:      '#FF0000',
            fillOpacity:    0.1
        });

        myPolygon.setMap(bild_map);

        var breite_max = -90.0;
        var breite_min = 90.0;
        var laenge_max = -180.0;
        var laenge_min = 180.0;

        for (var i = 0; i < polygon.length; i++) {
            if (polygon[i][1] > breite_max) {
                breite_max = polygon[i][1];
            }
    
            if (polygon[i][1] < breite_min) {
                breite_min = polygon[i][1];
            }
    
            if (polygon[i][0] > laenge_max) {
                laenge_max = polygon[i][0];
            }
    
            if (polygon[i][0] < laenge_min) {
                laenge_min = polygon[i][0];
            }

            if (breite > breite_max) {
                breite_max = breite;
            }
    
            if (breite < breite_min) {
                breite_min = breite;
            }
    
            if (laenge > laenge_max) {
                laenge_max = laenge;
            }
    
            if (laenge < laenge_min) {
                laenge_min = laenge;
            }
        }
    
        bild_map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(breite_min, laenge_min), new google.maps.LatLng(breite_max, laenge_max)));
    }

    geocoder = new google.maps.Geocoder();

    google.maps.event.addListener(bild_marker, 'click', function()
    {
        if (geocoder) {
            var latlng = bild_marker.getPosition();

            geocoder.geocode({
                'latLng':   latlng
            }, function(results, status) {
                var content = '';

                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        content = results[0].formatted_address;
                    }

                    if (results[1]) {
                        content = content + '<br>' + results[1].formatted_address;
                    }

                    if (results[2]) {
                        content = content + '<br>' + results[2].formatted_address;
                    }
                } else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
                    content = 'Keine weiteren Geoinformationen verfügbar.';
                } else {
                    content = 'Keine weiteren Geoinformationen verfügbar.';
                }

                infowindow = new google.maps.InfoWindow({
                    content : '<div class="infowindow">' + content + '</div>'
                });

                infowindow.open(bild_map, bild_marker);
            });
        }
    });
}
