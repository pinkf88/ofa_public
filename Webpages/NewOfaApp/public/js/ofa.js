function runden(x, n)
{
    if (n < 1 || n > 14)
        return false;

    var e = Math.pow(10, n);
    var k = (Math.round(x * e) / e).toString();

    if (k.indexOf('.') == -1)
        k += '.';

    k += e.toString().substring(1);

    return k.substring(0, k.indexOf('.') + n + 1);
}

function split(val)
{
    return val.split( / \s*/ );
}

function extractLast(term)
{
    return split(term).pop();
}

var bildform_ortid = 0;
var bildform_motive;

$(function()
{
    if ((this.location.pathname.indexOf('/bild/edit/') < 0) && (this.location.pathname.indexOf('/bild/add') < 0))
        return;

    bildform_ortid = $("select[name*='ortid']").val();

    if (bildform_ortid > 0)
        bild_getMotive(bildform_ortid);

    $("select[name*='ortid']").change(function()
    {
        bildform_ortid = $("select[name*='ortid']").val();
        bild_getMotive(bildform_ortid);
    });

    $("input[name*='beschreibung']")
        .bind("keydown", function(event)
        {
            if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active)
            {
                event.preventDefault();
            }
        })
        .autocomplete(
        {
            minLength: 0,
            source: function(request, response)
            {
                // delegate back to autocomplete, but extract the last term
                response($.ui.autocomplete.filter(bildform_motive, extractLast(request.term)));
            },
            focus: function()
            {
                // prevent value inserted on focus
                return false;
            },
            select: function(event, ui)
            {
                var terms = split(this.value);
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push(ui.item.value);
                // add placeholder to get the comma-and-space at the end
                terms.push("");
                this.value = terms.join(" ");
                return false;
            }
        });

    $("textarea[name*='bemerkung']")
        .bind("keydown", function(event)
        {
            if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active)
            {
                event.preventDefault();
            }
        })
        .autocomplete(
        {
            minLength: 0,
            source: function(request, response)
            {
                // delegate back to autocomplete, but extract the last term
                response($.ui.autocomplete.filter(bildform_motive, extractLast(request.term)));
            },
            focus: function()
            {
                // prevent value inserted on focus
                return false;
            },
            select: function(event, ui)
            {
                var terms = split(this.value);
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push(ui.item.value);
                // add placeholder to get the comma-and-space at the end
                terms.push("");
                this.value = terms.join(" ");
                return false;
            }
        });
});

function bild_getMotive(ortid)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetOrtMotive.php?ortid=" + ortid
    }).done(function(data)
    {
        bildform_motive = data.motive;
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function leben_move(lebenid, direction)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_UpdateLeben.php?lebenid=" + lebenid + "&direction=" + direction
    }).done(function(data)
    {
        if (data.result == 1)
        {
            leben_move_ui(lebenid, direction);
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function leben_move_ui(lebenid, direction)
{
    var obj = null;
    var twolines = false;

    if ($("#" + lebenid).attr('id') == $("#" + lebenid).next().attr('id'))
    {
        twolines = true;
    }

    if (direction == 1)
    {
        if ($("#" + lebenid).prev().attr('id') == $("#" + lebenid).prev().prev().attr('id'))
        {
            obj = $("#" + lebenid).prev().prev();

        }
        else
        {
            obj = $("#" + lebenid).prev();
        }

        if (twolines == true)
        {
            var line2 = $("#" + lebenid).next();
            obj.before($("#" + lebenid));
            obj.before(line2);
        }
        else
        {
            obj.before($("#" + lebenid));
        }
    }
    else
    {
        if (twolines == true)
        {
            if ($("#" + lebenid).next().next().attr('id') == $("#" + lebenid).next().next().next().attr('id'))
            {
                obj = $("#" + lebenid).next().next().next();

            }
            else
            {
                obj = $("#" + lebenid).next().next();
            }
        }
        else
        {
            if ($("#" + lebenid).next().attr('id') == $("#" + lebenid).next().next().attr('id'))
            {
                obj = $("#" + lebenid).next().next();

            }
            else
            {
                obj = $("#" + lebenid).next();
            }
        }

        if (twolines == true)
        {
            obj.after($("#" + lebenid).next());
            obj.after($("#" + lebenid));
        }
        else
        {
            obj.after($("#" + lebenid));
        }
    }
}
