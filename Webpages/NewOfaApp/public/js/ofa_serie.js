var last_serieid = 0;

$(function() {
    if ($('.leftside_serie').length) {
        $('.scroll-pane').jScrollPane();
        $('#bilderliste').sortable();
        $('#bilderliste').disableSelection();

        $('#bilderliste').sortable({
            update: function(event, ui)
            {
                serie_updateBilderliste();
            }
        });
    }
});

$("tr.firstline").mouseenter(function()
{
    $("#" + this.id).addClass("hasFocus");
    last_serieid = this.id;

    setTimeout(function()
    {
        if ($("#" + last_serieid).hasClass("hasFocus"))
        {
            serie_showBilder(last_serieid);
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

$("#bildzusatzdialog").dialog({
    autoOpen : false,
    height : 500,
    width : 1000,
    modal : true,
    buttons : {
        "Ok" : function()
        {
            var zusatz = $('textarea[name=seriebild_zusatz]').val().replace(new RegExp('\r?\n', 'g'), '%0D');

            $.ajax({
                url : "/inc/ofa_UpdateSerieBild.php?serieid=" + $('input[name=serieid]').val()
                        + "&bildid=" + $('input[name=bildid]').val() + "&zusatz=" + zusatz
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
                url : "/inc/ofa_AddSerieToWeb.php?webid=" + $('input[name=webid]').val()
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

function serie_showBilder(serieid)
{
    var output = "";

    $.ajax({
        dataType: "json",
        url : "/inc/ofa_GetSerie.php?serieid=" + serieid
    }).done(function(data)
    {
        // console.log(data);
        $('#bilderliste').empty();
        $('#bilderliste').html(data.serie_bilder);
        $('.scroll-pane').jScrollPane();

        output += '<b>' + data.serie + '</b><br>\n';
        output += data.serie_anzahl + ' Bilder\n';

        $("#serieinformation").html(output);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });

    $(".fancybox").fancybox();
}

function serie_editBild(serieid, bildid)
{
    $.ajax({
        url : "/inc/ofa_GetSerieBild.php?serieid=" + serieid + "&bildid=" + bildid
    }).done(function(msg)
    {
        $('input[name=serieid]').val(serieid);
        $('input[name=bildid]').val(bildid);
        $('textarea[name=seriebild_zusatz]').val(msg);
        $("#bildzusatzdialog").dialog("open");

    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_deleteBild(serieid, bildid)
{
    $.ajax({
        url : "/inc/ofa_DeleteBildFromSerie.php?serieid=" + serieid + "&bildid=" + bildid
    }).done(function(data)
    {
        serie_showBilder(serieid);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_updateBilderliste()
{
    var sortedIDs = $('#bilderliste').sortable('toArray');
    var bilder = '';

    for (var i = 0; i < sortedIDs.length; i++) {
        bilder += sortedIDs[i] + '|';
    }

    $.ajax({
        method: "POST",
        dataType : "text",
        url : "/inc/ofa_UpdateSerie.php?serieid=" + g_serieid,
        data: "bilder=" + bilder
    }).done(function(data)
    {
        serie_showBilder(parseInt(g_serieid));
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_addToWeb(serieid)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetSerie.php?serieid=" + serieid
    }).done(function(data)
    {
        $('input[name=webid]').val($('select[name=webid]').val());
        $('input[name=serieid]').val(serieid);
        $('input[name=webserie_titel]').val(data.serie);
        $('input[name=webserie_pfad]').val(data.serie.split(' ').join('_').toLowerCase().replace(/:/g, ''));
        $("#webseriedialog").dialog("open");
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_showBilderGrid(serieid)
{
    $('#frame_sort')[0].contentWindow.serie_fillGrid(serieid);

    $('#serie_sort').height($(window).height() - 70);
    $('#serie_normal').hide();
    $('#serie_sort').show();
}

function serie_showBilderTable(serieid)
{
    $('#frame_label')[0].contentWindow.serie_fillTable(serieid);

    $('#serie_label').height($(window).height() - 70);
    $('#serie_normal').hide();
    $('#serie_label').show();
}

function serie_hideBilderGrid(serieid)
{
    $('#serie_normal').show();
    $('#serie_sort').hide();
    $('#serie_label').hide();

    serie_showBilder(serieid);
}

var g_serieid = 0;
var g_vision_log = 0;

function serie_vision(serieid)
{
    g_serieid = serieid;
    serie_runVision();
}

function addToTextarea($ta, text) {
    var val = $ta.val();

    if (val) {
        $ta.val(val + text);
    } else {
        $ta.val(text);
    }

    $ta.scrollTop($ta[0].scrollHeight);
}

var g_run_vision_stop = null;
var g_run_vision_last = null;

function serie_runVision()
{
    $.ajax({
        type : "GET",
        url : "inc/ofa_GetVision.php?serieid=" + g_serieid + "&log=" + g_vision_log
    }).done(function(data)
    {
        addToTextarea($('#serie_log'), data);

        if (data.includes('Vision Processing beendet.') == true) {
            if (g_run_vision_stop != null) {
                clearTimeout(g_run_vision_stop);
                g_run_vision_stop = null;
            }

            g_serieid = 0;
            g_vision_log = 0;
        } else {
            g_vision_log = 1;
            g_run_vision_last = setTimeout(serie_runVision, 10000);          // 10 Sekunden
            g_run_vision_stop = setTimeout(serie_runVisionStop, 600000);     // 10 Minuten
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function serie_runVisionStop()
{
    if (g_run_vision_last != null) {
        clearTimeout(g_run_vision_last);
        g_run_vision_last = null;
    }

    g_vision_log = 0;
}

function serie_playSerie(serieid, runtype)
{
    $.ajax({
        url : "inc/ofa_ControlMedia.php?type=series&serieid=" + serieid + "&runtype=" + runtype
    }).done(function(data)
    {
        startSeriesTimer();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

var series_timer = null;

function startSeriesTimer()
{
    if (series_timer) {
        clearInterval(series_timer);
    }

    series_timer = setInterval(function() {
        serie_controlBild('info');
    }, 4000);
}

var no_picture_showing_counter = 0;

function serie_controlBild(todo)
{
    var url = 'inc/ofa_ControlMedia.php?type=series&';

    switch (todo) {
        case 'update':
            url = 'inc/ofa_ControlMedia.php?type=manage&picture';
            break;

        case 'info':
        case 'pause':
        case 'stop':
            url += todo;
            break;

        case 'next':
        case 'prev':
            url += 'goto=' + todo;
            break;

        default:
            return;
    }

    $.ajax({
        url : url
    }).done(function(data)
    {
        if (data == 'No picture showing.') {
            if (series_timer) {
                no_picture_showing_counter++;

                if (no_picture_showing_counter > 30) {
                    no_picture_showing_counter = 0;
                    clearInterval(series_timer);
                    series_timer = null;
                }
            }
        } else {
            if (series_timer == null) {
                startSeriesTimer();
            }
        }

        $("#serie_info").html(data);

        if ($('#control_picture').length) {
            if ($('.running_picture_nummer') != undefined && $('.running_picture_nummer')[0] != undefined && $('.running_picture_nummer')[0].id != undefined) {
                $.ajax({
                    dataType : "json",
                    url : "/inc/ofa_GetBildInfo.php?id=" + $('.running_picture_nummer')[0].id + "&version=2"
                }).done(function(data)
                {
                    $('#control_picture').html('<img class="control_picture" src="https://ofa-app.erde-in-bildern.eu/' + data.bilddata.bildpath + '">');
                }).fail(function(jqXHR, textStatus)
                {
                    console.log("Database access failed: " + textStatus);
                });
            }
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
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
