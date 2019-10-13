var last_webid = 0;

$("tr.firstline").mouseenter(function()
{
    $("#" + this.id).addClass("hasFocus");
    last_webid = this.id;

    setTimeout(function()
    {
        if ($("#" + last_webid).hasClass("hasFocus"))
        {
            web_showSerien(last_webid);
        }
    }, 800);
});

$("tr.firstline").mouseleave(function()
{
    $("#" + this.id).removeClass("hasFocus");
});

$("tr.firstline").hover(function()
{
    $(this).css("background", "lightgrey");
}, function()
{
    $(this).css("background", "");
});

$("#webseriedialog").dialog({
    autoOpen : false,
    height : 300,
    width : 700,
    modal : true,
    buttons : {
        "Ok" : function()
        {
            var titel = $('input[name=webserie_titel]').val();
            
            $.ajax({
                url : "/inc/ofa_UpdateWebSerie.php?webid=" + $('input[name=webid]').val()
                        + "&serieid=" + $('input[name=serieid]').val()
                        + "&titel=" + titel
                        + "&pfad=" + $('input[name=webserie_pfad]').val()
            }).done(function(data)
            {
            }).fail(function(jqXHR, textStatus)
            {
                console.log("Database access failed: " + textStatus);
            });

            $(this).dialog("close");
        },
        Cancel : function()
        {
            $(this).dialog("close");
        }
    },
    close : function()
    {
    }
});

function web_editSerie(webid, serieid)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetWebSerie.php?webid=" + webid + "&serieid=" + serieid
    }).done(function(data)
    {
        $('input[name=webid]').val(webid);
        $('input[name=serieid]').val(serieid);
        $('input[name=webserie_titel]').val(data.titel);
        $('input[name=webserie_pfad]').val(data.pfad);
        $("#webseriedialog").dialog("open");
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function web_deleteSerie(webid, serieid)
{
    $.ajax({
        url : "/inc/ofa_DeleteSerieFromWeb.php?webid=" + webid + "&serieid=" + serieid
    }).done(function(data)
    {
        web_showSerien(webid);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function web_showSerien(webid)
{
    var output = "";

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetWeb.php?webid=" + webid
    }).done(function(data)
    {
        $('#serienliste').empty();
        $('#serienliste').html(data.web_serien);
        $('.scroll-pane').jScrollPane();

        output += '<b>' + data.web + '</b><br>\n';
        output += data.web_anzahl + ' Serien\n';

        $("#webinformation").html(output);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function web_updateSerienliste()
{
    var sortedIDs = $('#serienliste').sortable('toArray');

    serien = '';

    for ( var i = 0; i < sortedIDs.length; i++)
        serien += sortedIDs[i] + '|';

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_UpdateWeb.php?serien=" + serien
    }).done(function(data)
    {
        web_showSerien(parseInt(data.webid));
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function web_showSerienGrid(serieid)
{
    $('#frame_sort')[0].contentWindow.web_fillGrid(serieid);

    $('#web_sort').height($(window).height() - 70);
    $('#web_normal').hide();
    $('#web_sort').show();
}

function web_hideSerienGrid(serieid)
{
    $('#web_normal').show();
    $('#web_sort').hide();

    web_showSerien(serieid);
}
        