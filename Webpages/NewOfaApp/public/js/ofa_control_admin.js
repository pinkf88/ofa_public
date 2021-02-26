function control_admin(todo)
{
    var url = '/inc/ofa_ControlMedia.php?type=admin&' + todo;

    $.ajax({
        url : url
    }).done(function(data)
    {
        if (todo == 'info_iobroker') {
            var info_iobroker = JSON.parse(data);
            console.log (info_iobroker);

            var html = '';
            
            for (var i = 0; i < info_iobroker.length; i++) {
                html += '<p>';

                if (info_iobroker[i].val == true) {
                    html += '<span class="control_led control_led_green"></span>';
                } else {
                    html += '<span class="control_led control_led_red"></span>';
                }

                html +=  info_iobroker[i].id + '</p>';
            }

            $('#control_admin_info_iobroker').html(html);
        }
        /*
        if (todo == 'info') {
            album_showAlbumInfo(JSON.parse(data));
        } else {
            $('#album_info').html(data);
        }
        */
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_admin(): url=' + url + ' / testStatus=' + textStatus);
    });
}
