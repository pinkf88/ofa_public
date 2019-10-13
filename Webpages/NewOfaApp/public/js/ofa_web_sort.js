$(function()
{
    $("#seriengrid").sortable();
    $("#seriengrid").disableSelection();

    $('#seriengrid').sortable({
        update: function(event, ui)
        {
            web_updateSerienGrid();
        }
    });
});

var g_webid = 0;

function web_fillGrid(webid)
{
    g_webid = webid;
    
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetWeb.php?webid=" + webid
    }).done(function(data)
    {
        $('#seriengrid').empty();
        $('#seriengrid').html(data.web_serien);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function web_updateSerienGrid()
{
    var sortedIDs = $('#seriengrid').sortable('toArray');

    serien = '';

    for ( var i = 0; i < sortedIDs.length; i++)
        serien += sortedIDs[i] + '|';

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_UpdateWeb.php?serien=" + serien
    }).done(function(data)
    {
        web_fillGrid(sortedIDs[0]);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function web_editSerie(webid, bildid)
{
    parent.web_editSerie(webid, bildid);
}

function web_deleteSerie(webid, serieid)
{
    $.ajax({
        url : "/inc/ofa_DeleteSerieFromWeb.php?webid=" + webid + "&serieid=" + serieid
    }).done(function(data)
    {
        web_fillGrid(webid);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function web_hideSerienGrid()
{
    parent.web_hideSerienGrid(g_webid);
}
