$(function() {
    $('td[name=serieid]').selectmenu();
});

var last_bildid = 0;

function bild_mouseenter(id)
{
    $("#" + id).addClass("hasFocus");

    if (last_bildid != id)
    {
        last_bildid = id;

        setTimeout(function()
        {
            if ($("#" + last_bildid).hasClass("hasFocus"))
            {
                bild_showInformation(last_bildid);
            }
        }, 800);
    }
}

function bild_mouseleave(id)
{
    if (id != last_bildid)
    {
        $("#" + id).removeClass("hasFocus");
    }
}

$("tr.firstline").mouseenter(function()
{
    bild_mouseenter(this.id);
});

$("tr.secondline").mouseenter(function()
{
    bild_mouseenter(this.id);
});

$("tr.firstline").mouseleave(function()
{
    bild_mouseleave(this.id);
});

$("tr.secondline").mouseleave(function()
{
    bild_mouseleave(this.id);
});

$("tr.firstline").hover(function()
{
    $(this).css("background", "lightgrey");

    if ($(this).next().hasClass("secondline"))
    {
        $(this).next().css("background", "lightgrey");
    }
}, function()
{
    $(this).css("background", "");
    $(this).next().css("background", "");
});

$("tr.secondline").hover(function()
{
    $(this).css("background", "lightgrey");

    if ($(this).prev().hasClass("firstline"))
    {
        $(this).prev().css("background", "lightgrey");
    }
}, function()
{
    $(this).css("background", "");
    $(this).prev().css("background", "");
});

$("#bildmotivedialog").dialog({
    autoOpen : false,
    height : 600,
    width : 1000,
    modal : true,
    buttons : {
        "Ok" : function()
        {
            var motive = '';

            $("input").each(function(index)
            {
                if ($(this).attr('name').substr(0, 5) == 'motiv' && $(this).attr('type') == 'checkbox'
                        && $(this).prop('checked') == true)
                {
                    motive += $(this).attr('name').substring(5) + '|';
                }
            });

            $.ajax({
                url : "/inc/ofa_UpdateBildMotive.php?bildid=" + $('input[name=bildid]').val()
                        + "&motive=" + motive
            }).done(function(data)
            {
                var b = parseInt($('input[name=bildid]').val());
                bild_showInformation(b);
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

$("#bildinfodialog").dialog({
    autoOpen : false,
    height : 180,
    width : 700,
    modal : true,
    buttons : {
        "Ok" : function()
        {
            $.ajax({
                url : "/inc/ofa_SetInfo.php?key=bildinfo&value=" + $('input[name=bild_info]').val()
            }).done(function(data)
            {
            }).fail(function(jqXHR, textStatus)
            {
                console.log("Database access failed: " + textStatus);
            });

            var x = $('input[name=bild_info]').val();

            if (x == "")
                $("#bildinfo").text("");
            else
                $("#bildinfo").text("(" + $('input[name=bild_info]').val() + ")");

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

function bild_showInformation(bildid)
{
    var output = "";

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetBildInfo.php?id=" + bildid
    }).done(function(data)
    {
        output += data.nummer + '\n';
        output += data.bilddaten + '\n';
        output += data.aufnahmedatum + '\n';
        output += data.geodaten + '\n';
        output += '<i>' + data.polygon + '</i>\n';
        output += data.motivliste + '\n';
        output += data.serieliste + '\n';
        output += data.zusatzinfo + '\n';

        $("#bildinformation").html(output);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_showInformation: Database access failed: " + textStatus);
    });

    $(".fancybox").fancybox();
}

function bild_editMotive(bildid)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetBildMotive.php?bildid=" + bildid + "&search=1"
    }).done(function(data)
    {
        if (data.motive == '++NEU++')
        {
            bild_showInformation(bildid);
        }
        else
        {
            $('input[name=bildid]').val(bildid);
            $('#bildmotive').html(data.bildmotive);
            $('#motive').html(data.motive);
            $("#bildmotivedialog").dialog("open");
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_editMotive: Database access failed: " + textStatus);
    });
}

function bild_motivOnClick(bildid, motivid)
{
    var motive = '';

    $("input").each(function(index) {
        if ($(this).attr('name').substr(0, 5) == 'motiv' && $(this).attr('type') == 'checkbox'
                && $(this).prop('checked') == true)
        {
            if (motivid == parseInt($(this).attr('name').substring(5)))
            {
                $(this).prop('checked', false);
            }
            else
            {
                motive += $(this).attr('name').substring(5) + '|';
            }
        }
    });

    $.ajax({
        url : "/inc/ofa_UpdateBildMotive.php?bildid=" + bildid + "&motive=" + motive
    }).done(function(msg)
    {
        $.ajax({
            dataType : "json",
            url : "/inc/ofa_GetBildMotive.php?bildid=" + bildid + "&search=0"
        }).done(function(data)
        {
            $('#bildmotive').html(data.bildmotive);
        }).fail(function(jqXHR, textStatus)
        {
            console.log("Database access failed: " + textStatus);
        });
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_motivOnClick: Database access failed: " + textStatus);
    });
}

function bild_setDefaultMotiv(bildid, motivid)
{
    $.ajax({
        url : "/inc/ofa_SetDefaultMotiv.php?bildid=" + bildid + "&motivid=" + motivid
    }).done(function(msg)
    {
        bild_showInformation(bildid);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_setDefaultMotiv: Database access failed: " + textStatus);
    });
}

function bild_deleteGeodaten(bildid, datei)
{
    $.ajax({
        url : "/inc/ofa_DeleteGeodaten.php?datei=" + datei
    }).done(function(msg)
    {
        bild_showInformation(bildid);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_deleteGeodaten: Database access failed: " + textStatus);
    });
}

var breite = 0;
var laenge = 0;

function bild_copyGeodaten(b, l)
{
    breite = b;
    laenge = l;
}

function bild_updateGeodaten(bildid, datei)
{
    $.ajax({
        url : "/inc/ofa_UpdateGeodaten.php?datei=" + datei + "&breite=" + breite + "&laenge=" + laenge
    }).done(function(msg)
    {
        bild_showInformation(bildid);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_updateGeodaten: Database access failed: " + textStatus);
    });
}

function bild_addToSerie(bildid)
{
    $.ajax({
        url : "/inc/ofa_AddBildToSerie.php?serieid=" + $('select[name=serieid]').val() + "&bildid=" + bildid
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log('bild_addToSerie() ERROR. bildid=' + bildid);
        console.log("bild_addToSerie: Database access failed: " + textStatus);
    });
}

function bild_addAllToSerie()
{
    console.log($(".firstline"));
    bild_addToSerieRecursive($(".firstline"), 0);
}

function bild_addToSerieRecursive(bilder, bild_no)
{
    if (bild_no >= bilder.length) {
        return;
    }

    console.log('bild_addToSerieRecursive(): bild_no=' + bild_no + '. id=' + bilder[bild_no].id);

    $.ajax({
        url : "/inc/ofa_AddBildToSerie.php?serieid=" + $('select[name=serieid]').val() + "&bildid=" + bilder[bild_no].id
    }).done(function(data)
    {
        bild_no++;

        setTimeout(function() {
            bild_addToSerieRecursive(bilder, bild_no);
        }, 1);
    }).fail(function(jqXHR, textStatus)
    {
        console.log('bild_addToSerieRecursive() ERROR. bild_no=' + bild_no);
        console.log("bild_addToSerieRecursive: Database access failed: " + textStatus);
    });
}

function bild_editInfo()
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetInfo.php?key=bildinfo"
    }).done(function(data)
    {
        $('input[name=bild_info]').val(data.value);
        $("#bildinfodialog").dialog("open");
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_editMotive: Database access failed: " + textStatus);
    });
}

function bild_addInfo(bildid)
{
    $.ajax({
        url : "/inc/ofa_UpdateBildInfo.php?bildid=" + bildid
    }).done(function(msg)
    {
        bild_showInformation(bildid);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_addInfo: Database access failed: " + textStatus);
    });
}

function bild_showBildImportGrid()
{
    $('#frame_bild_import')[0].contentWindow.bildimport_fillGrid();

    $('#bild_import').height($(window).height() - 70);
    $('#bild_normal').hide();
    $('#bild_import').show();
}

function bild_showBilderGrid()
{
    $('#frame_bild_show')[0].contentWindow.bild_fillGrid(
        $("[name='bildtyp']")[0].value,
        $("[name='jahr']")[0].value,
        $("[name='ortid']")[0].value,
        $("[name='landid']")[0].value,
        $("[name='nummer_von']")[0].value,
        $("[name='nummer_bis']")[0].value,
        $("[name='suchtext']")[0].value,
        $("[name='wertung_min']")[0].value,
        $("[name='countperpage']")[0].value
    );

    $('#bild_show').height($(window).height() - 70);
    $('#bild_normal').hide();
    $('#bild_show').show();
}

function bild_hideBilderGrid()
{
    $('#bild_normal').show();
    $('#bild_show').hide();
}

function bild_hideBildImportGrid()
{
    $('#bild_normal').show();
    $('#bild_import').hide();
}

function bild_playBilder(runtype)
{
    var url = "/inc/ofa_ControlMedia.php?type=pictures"
        + "&bildtyp=" + $("[name='bildtyp']")[0].value
        + "&jahr=" + $("[name='jahr']")[0].value
        + "&ortid=" + $("[name='ortid']")[0].value
        + "&landid=" + $("[name='landid']")[0].value
        + "&nummer_von=" + $("[name='nummer_von']")[0].value
        + "&nummer_bis=" + $("[name='nummer_bis']")[0].value
        + "&suchtext=" + $("[name='suchtext']")[0].value
        + "&wertung_min=" + $("[name='wertung_min']")[0].value
        + "&countperpage=" + $("[name='countperpage']")[0].value
        + "&runtype=" + runtype;

    $.ajax({
        url: url
    }).done(function(data)
    {
        ;
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_playBilder(): " + textStatus);
        console.log(url);
    });
}

function bild_playBild(nummer)
{
    var url = "/inc/ofa_ControlMedia.php?type=pictures"
        + "&bildtyp=0"
        + "&jahr=0"
        + "&ortid=0"
        + "&landid=0"
        + "&nummer_von=" + nummer
        + "&nummer_bis=" + nummer
        + "&suchtext="
        + "&wertung_min=0"
        + "&countperpage=0"
        + "&runtype=" + 3;

    $.ajax({
        url: url
    }).done(function(data)
    {
        ;
    }).fail(function(jqXHR, textStatus)
    {
        console.log("bild_playBilder(): " + textStatus);
        console.log(url);
    });
}
