function control_sortArtist()
{
    $('#sort_artist').html('Künstler');
    $('#sort_year').html('<a href="javascript:control_sortYear()">Jahr</a>');

    $('#alben_sort_artist').show();
    $('#alben_sort_year').hide();
}

function control_sortYear()
{
    $('#sort_artist').html('<a href="javascript:control_sortArtist()">Künstler</a>');
    $('#sort_year').html('Jahr');
    
    $('#alben_sort_year').show();
    $('#alben_sort_artist').hide();
}

function control_showTracks(albumid)
{
    if ($('#' + albumid).html() == '') {
        $.ajax({
            type:       'GET',
            dataType:   'json',
            url:        '/inc/ofa_ControlMedia.php?type=album&albumid=' + albumid.replace('year_tracks_', '').replace('artist_tracks_', '')
        }).done(function(tracks)
        {
            if (tracks != 'ERROR') {
                album_startTimer();
            }

            var html = '';
            // console.log(tracks);

            for (var i = 0; i < tracks.length; i++) {
                html += '<p class="media_album_track">'
                    + '<a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 0);"><span class="ui-icon ui-icon-play ui-icon-blue"></span></a> | '
                    + '<a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 1);"><span class="ui-icon ui-icon-seek-end ui-icon-blue"></span></a> | '
                    + '<a href="javascript:control_addToRunningTracks(2, \'' + tracks[i].musicbrainz_trackid + '\');\"><span class="ui-icon ui-icon-plus ui-icon-blue"></span></a>'
                    + '&nbsp;&nbsp;&nbsp;<a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 0);">'
                    + tracks[i].list_no + ' | ' + tracks[i].title;
                    
                if (tracks[i].artist != undefined && tracks[i].artist != '' && tracks[i].artist != tracks[i].albumartist) {
                    html += ' (' + tracks[i].artist + ')';
                }
                    
                html += ' | ' + tracks[i].duration + '</a></p>';
            }

            $('#' + albumid).append(html);
        }).fail(function(jqXHR, textStatus)
        {
            console.log("control_showTracks: ERROR " + textStatus);
        });
    } else {
        $('#' + albumid).html('');
    }
}

// type:
// 1: Album
// 2: Track
function control_addToRunningTracks(source, id)
{
    $.ajax({
        type : "GET",
        url : "/inc/ofa_ControlMedia.php?type=running&source=" + source + "&id=" + id + "&roomid=1"
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

function control_scrollToLetter(letter = '')
{
    if (letter == '') {
        $('html, body').animate({ scrollTop: $("#alben_sort_artist_intro_firstletters").offset().top - 100 }, 1000);
    } else {
        $('html, body').animate({ scrollTop: $("#" + letter).offset().top - 100 }, 1000);
    }
}

function control_scrollToYear(year = '')
{
    if (year == '') {
        $('html, body').animate({ scrollTop: $("#alben_sort_year_intro_originalyears").offset().top - 100 }, 1000);
    } else {
        $('html, body').animate({ scrollTop: $("#" + year).offset().top - 100 }, 1000);
    }
}

function control_scrollToTrackLetter(letter = '')
{
    if (letter == '') {
        $('html, body').animate({ scrollTop: $("#tracks_sort_track_intro_firstletters").offset().top - 100 }, 1000);
    } else {
        $('html, body').animate({ scrollTop: $("#track_" + letter).offset().top - 100 }, 1000);
    }
}

function control_playPlaylist(playlist_name, runtype)
{
    console.log('control_playPlaylist()');

    $.ajax({
        url : "/inc/ofa_ControlMedia.php?type=playlist&read&playlist_name=" + playlist_name
    }).done(function(data)
    {
        // console.log(data);
        var tracks = JSON.parse(data);
        var trackids = '';

        for (var i = 0; i < tracks.length; i++) {
            if (i > 0) {
                trackids += ',';
            }

            trackids += '"' + tracks[i].trackid + '"';
        }

        $.ajax({
            method: "POST",
            dataType : "text",
            url : "/inc/ofa_ControlMedia.php?type=playlist&play&runtype=" + runtype,
            data: "trackids=" + trackids
        }).done(function(data)
        {
            if (data != 'ERROR') {
                album_startTimer();
            }
        }).fail(function(jqXHR, textStatus)
        {
            console.log("Database access failed: " + textStatus);
        });
    });
}

function control_playMusic(runtype)
{
    // console.log($("#control_person").val());
    var year_from = parseInt($("#control_year_from").val());
    var year_to = parseInt($("#control_year_to").val());

    if (year_to != 0 && year_to < year_from) {
        year_from = year_to;
        year_to = parseInt($("#control_year_from").val());
    }

    var url = "/inc/ofa_ControlMedia.php?type=audio"
        + "&roomid=" + $("#control_room").val()
        + "&personid=" + $("#control_person").val()
        + "&artistid=" + $("#control_artist").val()
        + "&music=" + $("#control_music").val()
        + "&runtype=" + runtype
        + "&year_from=" + year_from
        + "&year_to=" + year_to;

    $.ajax({
        url: url
    }).done(function(data)
    {
        if (data != 'ERROR') {
            album_startTimer();
        }

        $("#lastinfo").html(data);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("control_playRandom(): Database access failed: " + textStatus);
        console.log(url);
    });
}

function control_showAlbums(artistid, mode)
{
    if ($('#albums_' + artistid).html() == '') {
        var url = '/inc/ofa_GetAlbums.php?artistid=' + artistid;

        $.ajax({
            url: url
        }).done(function(data)
        {
            if (data != 'ERROR') {
                album_startTimer();
            }
            
            $('#albums_' + artistid).append(data);
        }).fail(function(jqXHR, textStatus)
        {
            console.log("control_showAlbums: ERROR " + textStatus);
        });
    } else {
        $('#albums_' + artistid).html('');
    }
}

function control_flagAlbum(albumid)
{
    var func = 'add';

    if ($('#artist_flag_' + albumid).hasClass('ui-icon-red')) {
        $('#artist_flag_' + albumid).removeClass('ui-icon-red');
        $('#year_flag_' + albumid).removeClass('ui-icon-red');

        if ($('#artist_star_' + albumid).hasClass('ui-icon-red') == false) {
            $('#artist_media_album_' + albumid).removeClass('bgcolor_lightyellow');
            $('#year_media_album_' + albumid).removeClass('bgcolor_lightyellow');
        }

        func = 'remove';
    } else {
        $('#artist_flag_' + albumid).addClass('ui-icon-red');
        $('#year_flag_' + albumid).addClass('ui-icon-red');
        $('#artist_media_album_' + albumid).addClass('bgcolor_lightyellow');
        $('#year_media_album_' + albumid).addClass('bgcolor_lightyellow');
    }

    $.ajax({
        url :   '/inc/ofa_ControlMedia.php?type=flaglist&' + func + '&albumid=' + albumid
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_flagAlbum(): ' + textStatus);
    });
}

function control_starAlbum(albumid, year)
{
    var func = 'add';

    if ($('#artist_star_' + albumid).hasClass('ui-icon-red')) {
        $('#artist_star_' + albumid).removeClass('ui-icon-red');
        $('#year_star_' + albumid).removeClass('ui-icon-red');

        if ($('#artist_flag_' + albumid).hasClass('ui-icon-red') == false) {
            $('#artist_media_album_' + albumid).removeClass('bgcolor_lightyellow');
            $('#year_media_album_' + albumid).removeClass('bgcolor_lightyellow');
        }
        
        func = 'remove';
    } else {
        $('#artist_star_' + albumid).addClass('ui-icon-red');
        $('#year_star_' + albumid).addClass('ui-icon-red');
        $('#artist_media_album_' + albumid).addClass('bgcolor_lightyellow');
        $('#year_media_album_' + albumid).addClass('bgcolor_lightyellow');
    }

    $.ajax({
        url :   '/inc/ofa_ControlMedia.php?type=starlist&' + func + '&albumid=' + albumid + '&year=' + year
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_starAlbum(): ' + textStatus);
    });    
}
