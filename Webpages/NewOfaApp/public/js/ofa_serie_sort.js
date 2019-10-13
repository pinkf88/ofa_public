$(function()
{
    $("#bildergrid").sortable();
    $("#bildergrid").disableSelection();

    $('#bildergrid').sortable({
        update: function(event, ui)
        {
            serie_updateBilderGrid();
        }
    });
});

var g_serieid = 0;

function serie_fillGrid(serieid)
{
    g_serieid = serieid;

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetSerie.php?serieid=" + serieid
    }).done(function(data)
    {
        $('#bildergrid').empty();
        $('#bildergrid').html(data.serie_bilder);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });

    $(".fancybox").fancybox();
}

function serie_updateBilderGrid()
{
    var sortedIDs = $('#bildergrid').sortable('toArray');
    var bilder = '';

    for (var i = 0; i < sortedIDs.length; i++) {
        bilder += sortedIDs[i].replace('id', '') + '|';
    }

    $.ajax({
        method: "POST",
        dataType : "text",
        url : "/inc/ofa_UpdateSerie.php?serieid=" + g_serieid,
        data: "bilder=" + bilder
    }).done(function(data)
    {
        serie_fillGrid(g_serieid);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_editBild(serieid, bildid)
{
    parent.serie_editBild(serieid, bildid);
}

function serie_deleteBild(serieid, bildid)
{
    $.ajax({
        url : "/inc/ofa_DeleteBildFromSerie.php?serieid=" + serieid + "&bildid=" + bildid
    }).done(function(data)
    {
        // serie_fillGrid(serieid);
        $('#id' + bildid).hide();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_hideBilderGrid()
{
    parent.serie_hideBilderGrid(g_serieid);
}

function serie_setDauer(serieid, bildid, dauer)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_UpdateSerieBildDauer.php?serieid=" + serieid + "&bildid=" + bildid + "&dauer=" + dauer
    }).done(function(data)
    {
        $('#dur' + data.bildid).html(data.dauer + ' Sekunden');
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}
