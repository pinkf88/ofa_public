$(function()
{
});

var g_serieid = 0;

function serie_fillTable(serieid)
{
    g_serieid = serieid;

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetSerieLabel.php?serieid=" + serieid
    }).done(function(data)
    {
        $('#labeltable').empty();
        $('#labeltable').html(data.serie_labels);
        $('#bildertable').empty();
        $('#bildertable').html(data.serie_bilder);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });

    $(".fancybox").fancybox();
}

function serie_invalidLabel(bildlabelid)
{
    $.ajax({
        url : "/inc/ofa_InvalidBildLabel.php?bildlabelid=" + bildlabelid
    }).done(function(data)
    {
        $('#bl' + bildlabelid).html(data);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_validLabel(bildlabelid)
{
    $.ajax({
        url : "/inc/ofa_ValidBildLabel.php?bildlabelid=" + bildlabelid
    }).done(function(data)
    {
        $('#bl' + bildlabelid).html(data);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_invalidLabelForSerie(serieid, labelid)
{
    $.ajax({
        url : "/inc/ofa_InvalidBilderLabel.php?serieid=" + serieid + "&labelid=" + labelid
    }).done(function(data)
    {
        $('#label' + labelid).html(data);

        $.ajax({
            dataType : "json",
            url : "/inc/ofa_GetSerieLabel.php?serieid=" + serieid
        }).done(function(data)
        {
            // $('#bildertable').empty();
            $('#bildertable').html(data.serie_bilder);
        }).fail(function(jqXHR, textStatus)
        {
            console.log("Database access failed: " + textStatus);
        });
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_hideBilderTable()
{
    parent.serie_hideBilderGrid(g_serieid);
}
