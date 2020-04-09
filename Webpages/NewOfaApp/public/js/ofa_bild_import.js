$(function()
{
    $("#bildimportgrid").sortable();
    $("#bildimportgrid").disableSelection();

    $('#bildimportgrid').sortable({
        update: function(event, ui)
        {
            var sorted_bild_nr = $('#bildimportgrid').sortable('toArray');
            var bilder = [];
        
            for (var i = 0; i < sorted_bild_nr.length; i++) {
                for (var j = 0; j < bildimport_json.bilder.length; j++) {
                    if (sorted_bild_nr[i].substr(2) == '' + bildimport_json.bilder[j].datei) {
                        bilder.push(bildimport_json.bilder[j]);
                        bildimport_json.bilder.splice(j, 1);
                        break;
                    }
                }
            }

            bildimport_json.bilder = bilder;

            bildimport_updateGrid();
        }
    });

    $("#beschreibungdialog").dialog({
        autoOpen:   false,
        height:     320,
        width:      700,
        modal:      true,
        buttons:    {
            "Ok": function()
            {
                bildimport_json.bilder[g_bildarray_pos].beschreibung = $('input[name=beschreibung]').val();
                bildimport_json.bilder[g_bildarray_pos].bemerkung = $('textarea[name=bemerkung]').val();

                $(this).dialog("close");
            },
            "Ok / Copy": function()
            {
                bildimport_json.bilder[g_bildarray_pos].beschreibung = $('input[name=beschreibung]').val();
                bildimport_json.bilder[g_bildarray_pos].bemerkung = $('textarea[name=bemerkung]').val();
                g_beschreibung = $('input[name=beschreibung]').val();
                g_bemerkung = $('textarea[name=bemerkung]').val();

                $(this).dialog("close");
            },
            "Paste": function()
            {
                $('input[name=beschreibung]').val(g_beschreibung);
                $('textarea[name=bemerkung]').val(g_bemerkung);
            },
            Cancel : function()
            {
                $(this).dialog("close");
            }
        },
        close : function()
        {
            bildimport_updateGrid();
        },
        open: function(event, ui)
        {
        }
    });
});

var bildimport_json = null;

function bildimport_fillGrid(serieid)
{
    $.ajax({
        dataType:   'json',
        cache:      false,
        url:        '/pics/temp/bilddaten.json'
    }).done(function(json)
    {
        bildimport_json = json;
        bildimport_updateGrid();
    }).fail(function(jqXHR, textStatus)
    {
        console.log('Could not load bilddaten.json.');
    });

    $(".fancybox").fancybox();
}

function bildimport_updateGrid()
{
    $('#bildimportgrid').empty();

    var html = '';

    for (var i = 0; i < bildimport_json.bilder.length; i++) {
        html += '<li id="id' + bildimport_json.bilder[i].datei + '" class="ui-state-default">'
            + (i + 1) + ' | ' + bildimport_json.bilder[i].datei + ' | ' + bildimport_json.bilder[i].datum
            + ' | <a href="javascript:bildimport_editBild(' + bildimport_json.bilder[i].datei + ');">Edit</a><br>'
            + '<a class="fancybox" rel="group" href="/pics/temp/' + bildimport_json.bilder[i].datei + '.jpg">'
            + '<img class="mini" src="/pics/temp/' + bildimport_json.bilder[i].datei + '.png"></a><br>'
            + bildimport_json.bilder[i].beschreibung + '<br><i>' + bildimport_json.bilder[i].bemerkung + '</i>'
            + '</li>';
    }

    $('#bildimportgrid').html(html);
}

var g_datei = 0;
var g_bildarray_pos = -1;
var g_beschreibung = '';
var g_bemerkung = '';

function bildimport_editBild(datei)
{
    g_datei = datei;

    for (var i = 0; i < bildimport_json.bilder.length; i++) {
        if (bildimport_json.bilder[i].datei == datei) {
            g_bildarray_pos = i;
            $('input[name=beschreibung]').val(bildimport_json.bilder[i].beschreibung);
            $('textarea[name=bemerkung]').val(bildimport_json.bilder[i].bemerkung);

            break;
        }
    }

    $("#beschreibungdialog").dialog("open");
}

function bild_hideBildImportGrid()
{
    parent.bild_hideBildImportGrid();
}

function bild_saveBildImportGrid()
{
    $.ajax({
        method:     'POST',
        dataType:   'text',
        url:        '/inc/ofa_ImportBilder.php',
        data:       'bilder=' + JSON.stringify(bildimport_json)
    }).done(function(data)
    {
        bild_hideBildImportGrid();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}
