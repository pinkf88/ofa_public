$(function()
{
});

function bild_hideBilderGrid()
{
    parent.bild_hideBilderGrid();
}

var g_kameras = [];
var g_objektive = [];
var g_bilder = [];

function bild_fillGrid(bildtyp, jahr, ortid, landid, nummer_von, nummer_bis, suchtext, wertung_min, countperpage, serieid, serie)
{
    $('#bildergrid_serie').html('Serie: <b>' + serie + '</b>');

    var url = '/inc/ofa_GetBilder.php?bildtyp=' + bildtyp + '&jahr=' + jahr + '&ortid=' + ortid + '&landid=' + landid
        + '&nummer_von=' + nummer_von + '&nummer_bis=' + nummer_bis
        + '&suchtext=' + suchtext + '&wertung_min=' + wertung_min + '&countperpage=' + countperpage + '&serieid=' + serieid;

    // console.log('url=' + url);

    $.ajax({
        dataType:   'json',
        url:        url
    }).done(function(data)
    {
        g_kameras = data.kameras;
        g_objektive = data.objektive;
        g_bilder.length = 0;

        for (var i = 0; i < data.bilder.length; i++) {
            var splits = data.bilder[i].split('|');

            var bko = {
                bildid:         splits[1],
                nummer:         splits[2],
                datei:          splits[3],
                kamera:         splits[4],
                objektiv:       splits[5],
                wertung:        splits[6],
                pfad:           splits[7],
                extension:      splits[8],
                seriebildid:    parseInt(splits[9])
            }

            // console.log('bko', bko);
            g_bilder.push(bko);
        }

        var html = '<div id="bildergrid_select_kamera">| Alle | ';

        for (var i = 0; i < g_kameras.length; i++) {
            html += '<a href="javascript:bild_setKamera(\'' + g_kameras[i] + '\')">' + g_kameras[i] + '</a> | ';
        }

        html += '</div><div id="bildergrid_select_objektiv">| Alle | ';

        for (var i = 0; i < g_objektive.length; i++) {
            html += '<a href="javascript:bild_setObjektiv(\'' + g_objektive[i] + '\')">' + g_objektive[i] + '</a> | ';
        }

        html += '</div>';

        $('#bildergrid_select').html(html);

        bild_showHideBilder();
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ofa_bild_show.bild_fillGrid(): ' + textStatus);
    });
}

var g_kamera = 'Alle';
var g_objektiv = 'Alle';

function bild_setKamera(kamera)
{
    g_kamera = kamera;

    var html = '| ';
    
    if (kamera == 'Alle') {
        html +=  'Alle | ';
    } else {
        html += '<a href="javascript:bild_setKamera(\'Alle\')">Alle</a> | '
    }

    for (var i = 0; i < g_kameras.length; i++) {
        if (kamera == g_kameras[i]) {
            html += g_kameras[i] + ' | ';
        } else {
            html += '<a href="javascript:bild_setKamera(\'' + g_kameras[i] + '\')">' + g_kameras[i] + '</a> | ';
        }
    }
    
    $('#bildergrid_select_kamera').html(html);

    bild_showHideBilder();
}

function bild_setObjektiv(objektiv)
{
    g_objektiv = objektiv;

    var html = '| ';
    
    if (objektiv == 'Alle') {
        html +=  'Alle | ';
    } else {
        html += '<a href="javascript:bild_setObjektiv(\'Alle\')">Alle</a> | '
    }

    for (var i = 0; i < g_objektive.length; i++) {
        if (objektiv == g_objektive[i]) {
            html += g_objektive[i] + ' | ';
        } else {
            html += '<a href="javascript:bild_setObjektiv(\'' + g_objektive[i] + '\')">' + g_objektive[i] + '</a> | ';
        }
    }
    
    $('#bildergrid_select_objektiv').html(html);

    bild_showHideBilder();
}

function bild_showHideBilder()
{
    var bildergrid = '';

    for (var j = 0; j < g_bilder.length; j++) {
        var add_bild = false;

        if (g_kamera == 'Alle' && g_objektiv == 'Alle') {
            add_bild = true;
        } else if (g_kamera == g_bilder[j].kamera && (g_objektiv == 'Alle' || g_objektiv == g_bilder[j].objektiv)) {
            add_bild = true;
        } else if (g_objektiv == g_bilder[j].objektiv && (g_kamera == 'Alle' || g_kamera == g_bilder[j].kamera)) {
            add_bild = true;
        }

        if (add_bild == true) {
            var class_nummerdatei = '';

            if (g_bilder[j].seriebildid > 1) {
                class_nummerdatei = ' class="color_red"';
            }

            bildergrid += '<li id="id' + g_bilder[j].bildid + '" class="ui-state-default">'
                + '<div class="bildergrid">'
                + '<div id="nummerdatei' + g_bilder[j].bildid + '"' + class_nummerdatei + '>' + g_bilder[j].nummer + ' | ' + g_bilder[j].datei + ' | <span id="wertung' + g_bilder[j].bildid + '">0</span></div>'
                + '<div>'
                + '<a class="fancybox" rel="group" href="' + g_bilder[j].pfad + '.jpg" title="' + g_bilder[j].nummer + '|' + g_bilder[j].bildid + '|' + g_bilder[j].wertung + '">'
                + '<img id="img' + g_bilder[j].bildid + '" class="mini" src="' + g_bilder[j].pfad + '.' + g_bilder[j].extension + '"></a><br>'
                + '</div>'
                + '<div class="bildergrid_wertung">'
                + '<a style="color: #2a8af2;" class="bildergrid" href="javascript:bild_playBild(' + g_bilder[j].nummer + ')">Play</a> | '
                + '<a style="color: #2a8af2;" class="bildergrid" href="javascript:bild_addToSerie(' + g_bilder[j].bildid + ')">Serie</a> | '
                + '<a style="color: #2a8af2;" class="bildergrid" href="javascript:bild_setWertung(' + g_bilder[j].bildid + ',0)">0</a> '
                + '<a style="color: #2a8af2;" class="bildergrid" href="javascript:bild_setWertung(' + g_bilder[j].bildid + ',1)">1</a> '
                + '<a style="color: #2a8af2;" class="bildergrid" href="javascript:bild_setWertung(' + g_bilder[j].bildid + ',2)">2</a> '
                + '<a style="color: #2a8af2;" class="bildergrid" href="javascript:bild_setWertung(' + g_bilder[j].bildid + ',3)">3</a> '
                + '<a style="color: #2a8af2;" class="bildergrid" href="javascript:bild_setWertung(' + g_bilder[j].bildid + ',4)">4</a> '
                + '<a style="color: #2a8af2;" class="bildergrid" href="javascript:bild_setWertung(' + g_bilder[j].bildid + ',5)">5</a>'
                + '</div>'
                + '</div>'
                + '</li>';
        }
    }

    $('#bildergrid').empty();
    $('#bildergrid').html(bildergrid);
    $('.fancybox').fancybox();
}

function bild_setWertung(bildid, wertung)
{
    $.ajax({
        dataType:   'json',
        url:        '/inc/ofa_UpdateBildWertung.php?bildid=' + bildid + '&wertung=' + wertung
    }).done(function(data)
    {
        $('#wertung' + data.bildid).html('' + data.wertung);

        if ($('#rat_' + data.bildid) != undefined && $('#rat_' + data.bildid)[0] != undefined) {
            $('#rat_' + data.bildid)[0].innerHTML = '' + data.wertung;
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log('bild_setWertung(): ' + textStatus);
    });
}

$(document).keypress(function(event) {
    if ($('.fancybox-image').length) {
        var span = ($('span.child')[0].innerHTML).split('|');
        var nummer = span[0];
        var bildid = span[1];

        if (event.charCode >= 48 && event.charCode <= 53) { // 0 - 5
            bild_setWertung(bildid, event.charCode - 48);
            $('span.child')[0].innerHTML = nummer + '|' + bildid + '|' + (event.charCode - 48);
        } else if (event.charCode == 112) { // p
            parent.bild_playBild(nummer);
        } else if (event.charCode == 115) { // s
            $('#nummerdatei' + bildid).addClass('color_red');
            parent.bild_addToSerie(bildid);
        }
    }
});

function bild_playBild(nummer)
{
    parent.bild_playBild(nummer);
}

function bild_addToSerie(bildid)
{
    $('#nummerdatei' + bildid).addClass('color_red');
    parent.bild_addToSerie(bildid);
}
