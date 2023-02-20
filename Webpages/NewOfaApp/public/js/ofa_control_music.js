function control_sortArtist()
{
    $('#sort_artist').html('Künstler');
    $('#sort_year').html('<a href="javascript:control_sortYear()">Jahr</a>');

    $('#alben_sort_artist').show();
    $('#alben_sort_year').hide();
    $('#feedbackinformation').html('');
}

function control_sortYear()
{
    $('#sort_artist').html('<a href="javascript:control_sortArtist()">Künstler</a>');
    $('#sort_year').html('Jahr');
    
    $('#alben_sort_year').show();
    $('#alben_sort_artist').hide();
    $('#feedbackinformation').html('');
}

function control_showTracksMore(albumid)
{
    $('#track_info_' + albumid).show();
}

function control_showTracks(albumid, doit = false)
{
    if (doit == false) {
        $('#feedbackinformation').html('');
    }
    
    if ($('#' + albumid).html() == '' || doit == true) {
        var musicbrainz_albumid = albumid.replace('year_tracks_', '').replace('artist_tracks_', '');
        var url = '/inc/ofa_ControlMedia.php?type=album&albumid=' + musicbrainz_albumid;

        $.ajax({
            type:       'GET',
            dataType:   'json',
            url:        url
        }).done(function(tracks_info)
        {
            if (tracks_info != 'ERROR') {
                album_startTimer();
            }

            var html = '';
            var tracks = tracks_info.tracks;
            // console.log(tracks);

            for (var i = 0; i < tracks.length; i++) {
                html += '<p class="media_album_track">'
                    + '<span class=\"span_icon\"><a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 0);"><span class="ui-icon ui-icon-play ui-icon-blue"></span></a></span>'
                    + '<span class=\"span_icon\"><a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 1);"><span class="ui-icon ui-icon-seek-end ui-icon-blue"></span></a></span>'
                    + '<span class=\"span_icon\"><a href="javascript:control_addToRunningTracks(2, \'' + tracks[i].musicbrainz_trackid + '\');\"><span class="ui-icon ui-icon-plus ui-icon-blue"></span></a></span>'
                    + '<a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 0);">'
                    + '<span class="media_album_artist_track_number">' + tracks[i].list_no + '</span>'
                    + '<span class="media_album_artist_track_title">' + tracks[i].title;
                    
                if (tracks[i].artist != undefined && tracks[i].artist != '' && tracks[i].artist != tracks[i].albumartist) {
                    html += ' (' + tracks[i].artist + ')';
                }
                    
                html += '</span>' + tracks[i].duration + '</a></p>';
            }

            var info = tracks_info.info;

            if (info != null && info != undefined && info.genres != undefined && info.genres != null) {
                html += '<p id="track_more_' + musicbrainz_albumid + '" class="media_album_track_more">'
                    + '<a href="javascript:control_showTracksMore(\'' + musicbrainz_albumid + '\')">Mehr Infos</a>&nbsp;&nbsp;&nbsp;'
                    + '<a href="javascript:control_updateAlbum(\'' + albumid + '\', \'' + musicbrainz_albumid + '\')">Update Album</a>'
                    + '</p>';

                html += '<div id="track_info_' + musicbrainz_albumid + '" class="media_album_track_info">'

                if (info.genres != undefined && info.genres != null) {
                    html += '<p class="media_album_track_more_genres"><b>Genres:</b> ' + info.genres.slice(1, -1).replace(/\|/g, " | ") + '</p>';
                }

                if (info.styles != undefined && info.styles != null) {
                    html += '<p class="media_album_track_more_styles"><b>Style:</b> ' + info.styles.slice(1, -1).replace(/\|/g, " | ") + '</p>';
                }

                html += '<p class="media_album_track_more_extraartists">';
                
                for (var i in info.extraartists) {
                    var extraartist = info.extraartists[i].split('|');
                    html += extraartist[0] + ' <i>(' + extraartist[1] + ')</i><br>';
                }
                
                html += '</p>';

                html += '<p class="media_album_track_more_videos">';
                
                for (var i in info.videos) {
                    var video = info.videos[i].split('|');
                    html += '<a href=' + video[0] + ' target="_blank">' + video[1] + '</a><br>';
                }

                html += '</p>';

                html += '</div>';
            } else {
                html += '<p id="track_more_' + musicbrainz_albumid + '" class="media_album_track_more">'
                    + '<a href="javascript:control_updateAlbum(\'' + albumid + '\', \'' + musicbrainz_albumid + '\')">Update Album</a>'
                    + '</p>';
            }

            $('#' + albumid).html(html);
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
    $('#feedbackinformation').html('');

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
    $('#feedbackinformation').html('');

    if (letter == '') {
        $('html, body').animate({ scrollTop: $("#alben_sort_artist_intro_firstletters").offset().top - 100 }, 1000);
    } else {
        $('html, body').animate({ scrollTop: $("#" + letter).offset().top - 100 }, 1000);
    }
}

function control_scrollToYear(year = '')
{
    $('#feedbackinformation').html('');

    if (year == '') {
        $('html, body').animate({ scrollTop: $("#alben_sort_year_intro_originalyears").offset().top - 100 }, 1000);
    } else {
        $('html, body').animate({ scrollTop: $("#" + year).offset().top - 100 }, 1000);
    }
}

function control_scrollToTrackLetter(letter = '')
{
    $('#feedbackinformation').html('');

    if (letter == '') {
        $('html, body').animate({ scrollTop: $("#tracks_sort_track_intro_firstletters").offset().top - 100 }, 1000);
    } else {
        $('html, body').animate({ scrollTop: $("#track_" + letter).offset().top - 100 }, 1000);
    }
}

function control_playPlaylist(playlist_name, runtype)
{
    $('#feedbackinformation').html('');

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
    $('#feedbackinformation').html('');

    // console.log($("#control_person").val());
    var year_from = parseInt($("#control_year_from").val());
    var year_to = parseInt($("#control_year_to").val());

    if (year_to != 0 && year_to < year_from) {
        year_from = year_to;
        year_to = parseInt($("#control_year_from").val());
    }

    var no_compilations = 0;

    if ($('#no_compilations').is(':checked')) {
        no_compilations = 1;
    }

    var rarely_played = 0;

    if ($('#rarely_played').is(':checked')) {
        rarely_played = 1;
    }

    var url = "/inc/ofa_ControlMedia.php?type=audio"
        + "&roomid=" + $("#control_room").val()
        + "&personid=" + $("#control_person").val()
        + "&artistid=" + $("#control_artist").val()
        + "&music=" + $("#control_music").val()
        + "&runtype=" + runtype
        + "&year_from=" + year_from
        + "&year_to=" + year_to
        + "&no_compilations=" + no_compilations
        + "&rarely_played=" + rarely_played;

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
    $('#feedbackinformation').html('');

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
    $('#feedbackinformation').html('');

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
    $('#feedbackinformation').html('');
    
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

function control_updateAlbum(albumid, musicbrainz_albumid)
{
    var url = '/inc/ofa_ControlMedia.php?type=album&update&albumid=' + musicbrainz_albumid;

    $.ajax({
        type:   'GET',
        url:    url
    }).done(function(data)
    {
        console.log('album_updateData(): ', data);
        $('#feedbackinformation').html(data);

        if (data == 'OK') {
            control_showTracks(albumid, true);
        } else {
            var html = '<a href="javascript:control_updateAlbum(\'' + albumid + '\', \'' + musicbrainz_albumid + '\')">Update Album</a>'
                + '&nbsp;&nbsp;&nbsp;'
                + '<a href="https://musicbrainz.org/release/' + musicbrainz_albumid + '" target="_blank">MusicBrainz</a>';

            $('#track_more_' + musicbrainz_albumid).html(html);
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("album_updateData(): Database access failed: " + textStatus);
        $('#feedbackinformation').html('ERROR');
    });
}
