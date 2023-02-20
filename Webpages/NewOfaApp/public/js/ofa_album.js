var last_albumid = '';

$("tr.firstline").hover(function()
{
    $(this).css("background", "lightgrey");
}, function()
{
    $(this).css("background", "");
});

$("tr.firstline").mouseenter(function()
{
    $("#" + this.id).addClass("hasFocus");

    if (last_albumid != this.id) {
        last_albumid = this.id;

        setTimeout(function() {
            if ($("#" + last_albumid).hasClass("hasFocus"))
            {
                album_showInformation(last_albumid);
            }
        }, 800);
    }
});

$("tr.firstline").mouseleave(function()
{
    if (this.id != last_albumid) {
        $("#" + this.id).removeClass("hasFocus");
    }

    $('#feedbackinformation').html('');
});

function album_showInformation(albumid)
{
    var output = "";

    $.ajax({
        url:        "/inc/ofa_GetAlbumInfo.php?albumid=" + albumid
    }).done(function(data)
    {
        var json = {};

        try {
            json = JSON.parse(data);
        } catch(err) {
            console.log('album_showInformation(): ERROR=' + err.message);
            console.log(data);
        }

        output += json.bilddaten + '\n';
        output += json.albumdaten + '\n';

        $("#albuminformation").html(output);
    }).fail(function(jqXHR, textStatus)
    {
        console.log('album_showInformation(): Ajax ERROR=' + textStatus);
    });

    $(".fancybox").fancybox();
}

var album_timer = null;

function album_startTimer()
{
    if (album_timer) {
        clearInterval(album_timer);
    }

    album_timer = setInterval(function() {
        album_controlTrack('info');
    }, 4000);
}

var owners = ["Alle", "JR", "EP"];

function album_setOwner(albumid, ownerid)
{
    var html = '';

    for (var i = 0; i < owners.length; i++) {
        console.log(i + ': ' + owners[i]);

        if (ownerid == i) {
            html += '<b>' + owners[i] + '</b>';
        } else {
            html += '<a href="javascript:album_setOwner(\'' + albumid + '\', '  + i + ');">' + owners[i] + '</a>';
        }
    
        if (i < owners.length - 1) {
            html += ' | ';
        }
    }
    
    $("#owner_" + albumid).html(html);

    $.ajax({
        type: 'GET',
        url : "/inc/ofa_UpdateAlbumOwner.php?albumid=" + albumid + "&ownerid=" + ownerid
    }).done(function(data)
    {
        if (data != 'ERROR') {
            album_startTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ERROR album_setOwner(): " + textStatus);
    });
}

function album_setRating(albumid, rating)
{
    var html = '';

    for (var i = 0; i <= 5; i++) {
        if (rating == i) {
            html += '<b>' + i + '</b>';
        } else {
            html += '<a href="javascript:album_setRating(\'' + albumid + '\', '  + i + ');">' + i + '</a>';
        }
    
        if (i < 5) {
            html += ' | ';
        }
    }
    
    $("#rating_" + albumid).html(html);

    $.ajax({
        type: 'GET',
        url : "/inc/ofa_UpdateAlbumRating.php?albumid=" + albumid + "&rating=" + rating
    }).done(function(data)
    {
        if (data != 'ERROR') {
            album_startTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ERROR album_setRating(): " + textStatus);
    });
}

function album_setStudioLive(albumid, studio)
{
    var html = '';

    if (studio == 1) {
        html = '<a href="javascript:album_setStudioLive(\'' +  albumid + '\', 0);">Studio</a>';
    } else {
        html = '<a href="javascript:album_setStudioLive(\'' +  albumid + '\', 1);">Live</a>';
    }
    
    $("#studio_" + albumid).html(html);

    $.ajax({
        type: 'GET',
        url : "/inc/ofa_UpdateStudioLive.php?id=" + albumid + "&studio=" + studio
    }).done(function(data)
    {
        if (data != 'ERROR') {
            album_startTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ERROR album_setStudioLive(): " + textStatus);
    });
}

function album_setCompilation(albumid, compilation)
{
    var html = '';

    if (compilation == 1) {
        html = '<a href="javascript:album_setCompilation(\'' +  albumid + '\', 0);">Comp.</a>';
    } else {
        html = '<a href="javascript:album_setCompilation(\'' +  albumid + '\', 1);">Reg.</a>';
    }
    
    $("#compilation_" + albumid).html(html);

    $.ajax({
        type: 'GET',
        url : "/inc/ofa_UpdateCompilation.php?id=" + albumid + "&compilation=" + compilation
    }).done(function(data)
    {
        if (data != 'ERROR') {
            album_startTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ERROR album_setCompilation(): " + textStatus);
    });
}

function album_playAlbum(albumid, runtype = 0)
{
    var roomid = 1;
    
    if ($("[name='roomid']")[0] != undefined) {
        $("[name='roomid']")[0].value;
    }

    $.ajax({
        type: 'GET',
        url : "/inc/ofa_ControlMedia.php?type=album&albumid=" + albumid + "&runtype=" + runtype + "&roomid=" + roomid
    }).done(function(data)
    {
        if (data != 'ERROR') {
            album_startTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_playArtist(artist, runtype)
{
    var roomid = 1;
    
    if ($("[name='roomid']")[0] != undefined) {
        $("[name='roomid']")[0].value;
    }

    $.ajax({
        type: 'GET',
        url : "/inc/ofa_ControlMedia.php?type=album&artist=" + artist + "&runtype=" + runtype + "&roomid=" + roomid
    }).done(function(data)
    {
        if (data != 'ERROR') {
            album_startTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("album_playArtist(): Database access failed: " + textStatus);
    });
}

function album_playTrack(trackid, runmode = 0)
{
    var roomid = 1;
    
    if ($("[name='roomid']")[0] != undefined) {
        $("[name='roomid']")[0].value;
    }

    $.ajax({
        type: 'GET',
        url : '/inc/ofa_ControlMedia.php?type=track&trackid=' + trackid + '&runmode=' + runmode + '&roomid=' + roomid
    }).done(function(data)
    {
        if (data != 'ERROR') {
            album_startTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR album_playTrack(): ' + textStatus);
    });
}

var no_track_running_counter = 0;

function album_controlTrack(todo, param = '')
{
    var roomid = 1;
    
    if ($("[name='roomid']")[0] != undefined) {
        $("[name='roomid']")[0].value;
    }
    
    var url1 = '/inc/ofa_ControlMedia.php?type=album&';

    switch (todo) {
        case 'new':
            url1 = '/inc/ofa_ControlMedia.php?type=manage&audio_new';
            break;

        case 'update':
            url1 = '/inc/ofa_ControlMedia.php?type=manage&audio_update';
            break;

        case 'info':
        case 'pause':
        case 'stop':
            url1 += todo;
            break;

        case 'next':
        case 'previous':
            url1 += 'goto=' + todo;
            break;

        case 'url':
            url1 += 'goto=' + param;
            break;

        case 'vol_down':
        case 'vol_up':
        case 'vol_mute':
            url1 = '/inc/ofa_ControlMedia.php?type=audio&direction=' + todo + '&roomid=' + roomid;
            break;

        default:
            return;
    }

    $.ajax({
        url: url1
    }).done(function(data) {
        if (data == 'No track running.') {
            if (album_timer) {
                no_track_running_counter++;

                if (no_track_running_counter > 10) {
                    no_track_running_counter = 0;
                    clearInterval(album_timer);
                    album_timer = null;
                }
            }
        } else {
            // album_startTimer();   warum?
        }

        if (todo == 'info') {
            album_showAlbumInfo(JSON.parse(data));
        } else {
            album_showAlbumInfo(data);
        }

        if ($('#control_cover').length) {
            if ($('.running_track_title') != undefined && $('.running_track_title')[0] != undefined && $('.running_track_title')[0].id != undefined) {
                var url2 = '/inc/ofa_GetTrackInfo.php?trackid=' + $('.running_track_title')[0].id;

                $.ajax({
                    dataType:   'json',
                    url:        url2
                }).done(function(data) {
                    // if ($('#control_cover').is(':visible') == true) {
                        $('#control_cover').html('<img class="control_cover" src="' + data.trackdata.bildurl + '">');
                    // }

                    // if ($('#home_cover').is(':visible') == true) {
                        $('#home_cover').html('<img class="home_cover" src="' + data.trackdata.bildurl + '">');
                    // }
                }).fail(function(jqXHR, textStatus) {
                    console.log('album_controlTrack(): Ajax Fehler', url2, textStatus);
                });
            }
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log('album_controlTrack(): Ajax Fehler', url1, textStatus);
    });
}

var old_info = {};

function album_showAlbumInfo(info)
{
    if (JSON.stringify(old_info) != JSON.stringify(info)) {
        old_info = info;

        var html = '';
        
        if (info.title == undefined) {
            html = '<b>' + info + '</b>';
        } else {
            html = '<p><b>' + info.title + '</b><br>' + info.artist + '<br><i>' + info.album + '</i></p>';

            if (info.duration != undefined && info.duration != '') {
                html += '<p>' + info.duration + ' | ' ;

                if (info.studio == 1) {
                    html += 'Studio | ';
                } else if (info.studio == 0) {
                    html += 'Live | ';
                }

                html += info.count_play + ' || ' + info.mean_volume + ' / ' + info.max_volume + '</p>';
            }

            if (info.next_tracks != undefined && info.next_tracks.length > 0) {
                for (var i = 0; i < info.next_tracks.length; i++) {
                    html += '<p><a href="javascript:album_controlTrack(\'url\', \'' + info.next_tracks[i].url + '\');">'
                        + info.next_tracks[i].list_no + ' | '+ info.next_tracks[i].title + '</a></p>';
                }
            }

            // if ($('#control_cover').is(':visible') == true) {
                $('#control_cover').html('<img class="control_cover" src="' + info.image.url + '">');
            // }

            // if ($('#home_cover').is(':visible') == true) {
                $('#home_cover').html('<img class="home_cover" src="' + info.image.url + '">');
            // }
        }

        $('#album_info').html(html);
        $('#home_album_info').html(html);

        if (typeof control_echo === "function") {
            control_echo('get_all');
        }    
    }
}

function album_updateData(albumid)
{
    var url = '/inc/ofa_ControlMedia.php?type=album&update&albumid=' + albumid;

    $.ajax({
        type:   'GET',
        url:    url
    }).done(function(data)
    {
        console.log('album_updateData(): ', data);
        $('#feedbackinformation').html(data);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("album_updateData(): Database access failed: " + textStatus);
    });
}
