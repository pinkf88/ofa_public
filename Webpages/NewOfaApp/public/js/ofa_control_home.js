$(function() {
    $('#echo_volume_slider').slider({
        min:    0,
        max:    100,
        step:   1,
        value:  0,
        create: function() {
            $('#echo_volume').text($(this).slider('value'));
        },
        slide: function(event, ui) {
            $('#echo_volume').text(ui.value);
            control_echo('set_volume=' + ui.value);
        }
    });

    $('#echo_treble_slider').slider({
        min:    -6,
        max:    6,
        step:   1,
        value:  0,
        create: function() {
            $('#echo_treble').text($(this).slider('value'));
        },
        slide: function(event, ui) {
            $('#echo_treble').text(ui.value);
            control_echo('set_treble=' + ui.value);
        }
    });

    $('#echo_midrange_slider').slider({
        min:    -6,
        max:    6,
        step:   1,
        value:  0,
        create: function() {
            $('#echo_midrange').text($(this).slider('value'));
        },
        slide: function(event, ui) {
            $('#echo_midrange').text(ui.value);
            control_echo('set_midrange=' + ui.value);
        }
    });

    $('#echo_bass_slider').slider({
        min:    -6,
        max:    6,
        step:   1,
        value:  0,
        create: function() {
            $('#echo_bass').text($(this).slider('value'));
        },
        slide: function(event, ui) {
            $('#echo_bass').text(ui.value);
            control_echo('set_bass=' + ui.value);
        }
    });

    control_echo('get_all');
});

function control_setSlider(element, value)
{
    $('#' + element + '_slider').slider('value', value);
    $('#' + element).text(value);
}

function control_echo(todo, roomid = 1)
{
    var url = '/inc/ofa_ControlEcho.php?' + todo + '&roomid=' + roomid;

    $.ajax({
        dataType:   'json',
        url:        url
    }).done(function(data) {
        console.log('data', data, url);

        if (data.volume != undefined) {
            control_setSlider('echo_volume', data.volume);
        }

        if (data.treble != undefined) {
            control_setSlider('echo_treble', data.treble);
        }

        if (data.midrange != undefined) {
            control_setSlider('echo_midrange', data.midrange);
        }

        if (data.bass != undefined) {
            control_setSlider('echo_bass', data.bass);
        }
    }).fail(function(jqXHR, textStatus) {
        console.log('album_controlTrack(): Ajax Fehler', url, textStatus);
    });
}
