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
});

function album_showInformation(albumid)
{
    var output = "";

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetAlbumInfo.php?albumid=" + albumid
    }).done(function(data)
    {
        output += data.bilddaten + '\n';
        output += data.albumdaten + '\n';

        $("#albuminformation").html(output);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
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
        console.log("Database access failed: " + textStatus);
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
        url : "/inc/ofa_ControlMedia.php?type=track&trackid=" + trackid + "&runmode=" + runmode + "&roomid=" + roomid
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

var no_track_running_counter = 0;

function album_controlTrack(todo, param = '')
{
    var url = 'inc/ofa_ControlMedia.php?type=album&';

    switch (todo) {
        case 'new':
            url = 'inc/ofa_ControlMedia.php?type=manage&audio_new';
            break;

        case 'update':
            url = 'inc/ofa_ControlMedia.php?type=manage&audio_update';
            break;

        case 'info':
        case 'pause':
        case 'stop':
            url += todo;
            break;

        case 'next':
        case 'previous':
            url += 'goto=' + todo;
            break;

        case 'url':
            url += 'goto=' + param;
            break;

        default:
            return;
    }

    $.ajax({
        url : url
    }).done(function(data)
    {
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
            $("#album_info").html(data);
        }

        if ($('#control_cover').length) {
            if ($('.running_track_title') != undefined && $('.running_track_title')[0] != undefined && $('.running_track_title')[0].id != undefined) {
                $.ajax({
                    dataType : "json",
                    url : "/inc/ofa_GetTrackInfo.php?trackid=" + $('.running_track_title')[0].id
                }).done(function(data)
                {
                    $('#control_cover').html('<img class="control_cover" src="' + data.trackdata.bildurl + '">');
                }).fail(function(jqXHR, textStatus)
                {
                    console.log("Database access failed: url=" + url + ' / testStatus=' + textStatus);
                });
            }
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: url=" + url + ' / testStatus=' + textStatus);
    });
}

function album_showAlbumInfo(info)
{
    var html = '';
    
    if (info.title == undefined) {
        html = '<b>' + info + '</b>';
    } else {
        html = '<p><b>' + info.title + '</b><br>' + info.artist + '<br><i>' + info.album + '</i></p>';

        if (info.next_tracks != undefined && info.next_tracks.length > 0) {
            for (var i = 0; i < info.next_tracks.length; i++) {
                html += '<p><a href="javascript:album_controlTrack(\'url\', \'' + info.next_tracks[i].url + '\');">'
                    + info.next_tracks[i].list_no + ' | '+ info.next_tracks[i].title + '</a></p>';
            }
        }

        $('#control_cover').html('<img class="control_cover" src="' + info.image.url + '">');
    }

    $("#album_info").html(html);
}

function album_picAdded(albumid)
{
    // var url = 'https://' + window.location.hostname + '/inc/ofa_PicAdded.php?albumid=' + albumid;
    var url = '/inc/ofa_PicAdded.php?albumid=' + albumid;

    $.ajax({
        type: 'GET',
        url: url
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log("album_picAdded(): Database access failed: " + textStatus);
    });
}
