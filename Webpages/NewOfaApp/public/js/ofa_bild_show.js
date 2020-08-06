$(function()
{
});

function bild_hideBilderGrid()
{
    parent.bild_hideBilderGrid();
}

function bild_fillGrid(bildtyp, jahr, ortid, landid, nummer_von, nummer_bis, suchtext, wertung_min, countperpage)
{
    var url = "/inc/ofa_GetBilder.php?bildtyp=" + bildtyp + "&jahr=" + jahr + "&ortid=" + ortid + "&landid=" + landid
        + "&nummer_von=" + nummer_von + "&nummer_bis=" + nummer_bis
        + "&suchtext=" + suchtext + "&wertung_min=" + wertung_min + "&countperpage=" + countperpage;

    // console.log('url=' + url);

    $.ajax({
        dataType: "json",
        url: url
    }).done(function(data)
    {
        $('#bildergrid').empty();
        $('#bildergrid').html(data.bilder);

        $(".fancybox").fancybox();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ofa_bild_show.bild_fillGrid(): " + textStatus);
    });
}

function bild_setWertung(bildid, wertung)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_UpdateBildWertung.php?bildid=" + bildid + "&wertung=" + wertung
    }).done(function(data)
    {
        $('#wertung' + data.bildid).html('Wertung: ' + data.wertung);
        $('#rat_' + data.bildid)[0].innerHTML = '' + data.wertung;
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_setWertung(): " + textStatus);
    });
}

$(document).keypress(function(event) {
    if ($(".fancybox-image").length && event.charCode >= 48 && event.charCode <= 53) {
        bild_setWertung(parseInt(($("span.child")[0].innerHTML)), event.charCode - 48);

        $("span.child")[0].innerHTML = parseInt(($("span.child")[0].innerHTML)) + ' | ' + (event.charCode - 48);
    }
});
