$('tr.firstline').hover(
    function () {
        $(this).css('background','lightgrey');

        if ($(this).next().hasClass('secondline')) {
            $(this).next().css('background','lightgrey');
        }
    }, 
    function () {
        $(this).css('background','');
        $(this).next().css('background','');
    }
);

$('tr.secondline').hover(
    function () {
        $(this).css('background','lightgrey');

        if ($(this).prev().hasClass('firstline')) {
            $(this).prev().css('background','lightgrey');
        }
    }, 
    function () {
        $(this).css('background','');
        $(this).prev().css('background','');
    }
);

function leben_move(lebenid, direction)
{
    var url = '/inc/ofa_UpdateLeben.php?lebenid=' + lebenid + "&direction=" + direction;

    $.ajax({
        dataType:   'json',
        url:        url
    }).done(function(data)
    {
        if (data.result == 1) {
            leben_move_ui(lebenid, direction);
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log('leben_move() Ajax Error', url, textStatus);
    });
}

function leben_move_ui(lebenid, direction)
{
    var obj = null;
    var twolines = false;

    if ($('#' + lebenid).attr('id') == $('#' + lebenid).next().attr('id')) {
        twolines = true;
    }

    if (direction == 1) {
        if ($('#' + lebenid).prev().attr('id') == $('#' + lebenid).prev().prev().attr('id')) {
            obj = $('#' + lebenid).prev().prev();

        } else {
            obj = $('#' + lebenid).prev();
        }

        if (twolines == true) {
            var line2 = $('#' + lebenid).next();
            obj.before($('#' + lebenid));
            obj.before(line2);
        } else {
            obj.before($('#' + lebenid));
        }
    } else {
        if (twolines == true) {
            if ($('#' + lebenid).next().next().attr('id') == $('#' + lebenid).next().next().next().attr('id')) {
                obj = $('#' + lebenid).next().next().next();

            } else {
                obj = $('#' + lebenid).next().next();
            }
        } else {
            if ($('#' + lebenid).next().attr('id') == $('#' + lebenid).next().next().attr('id')) {
                obj = $('#' + lebenid).next().next();
            } else {
                obj = $('#' + lebenid).next();
            }
        }

        if (twolines == true) {
            obj.after($('#' + lebenid).next());
            obj.after($('#' + lebenid));
        } else {
            obj.after($('#' + lebenid));
        }
    }
}
