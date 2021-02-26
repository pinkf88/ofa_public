var album_data_all = null;

$(function() {
    setTimeout( function() {
        $('#control_fancybox-overlay').hide();
    }, 15000);

    $.ajax({
        url : '/inc/ofa_GetAllMusic.php'
    }).done(function(data)
    {
        album_data_all = JSON.parse(data);
        playlist_buildMenuArtists();

        $('#sp_albumliste').height($(window).height() - 60);
        $('#sp_trackliste').height($(window).height() - 60);
        $('#sp_playliste').height($(window).height() - 120);
        $('#playliste').sortable();
        $('#playliste').disableSelection();
        $('.jspVerticalBar').css('width', '10px');

        playlist_listPlaylists();

        $('#playlist_save_button').click(function() {
            savePlaylist($('#playlist_save_input').val());
            $('#playlist_save_popup').css("display", "none");
        });

        $('#playlist_cancel_button').click(function() {
            $('#playlist_save_popup').css("display", "none");
        });

        $(window).resize(function() {
            $('#sp_albumliste').height($(window).height() - 60);
            $('#sp_trackliste').height($(window).height() - 60);
            $('#sp_playliste').height($(window).height() - 120);
            $('.scroll-pane').jScrollPane();
        });

        $('#control_fancybox-overlay').hide();
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR ofa_playlist function()' + textStatus);
        $('#control_fancybox-overlay').hide();
    });
});

function playlist_showTrackInformation(trackid)
{
    var output = "";

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetTrackInfo.php?trackid=" + trackid
    }).done(function(data)
    {
        output += data.bilddaten + '\n';
        output += data.trackdaten + '\n';

        $("#albuminformation").html(output);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("playlist_showTrackInformation(): textStatus=" + textStatus + '. trackid=' + trackid);
    });

    $(".fancybox").fancybox();
}

var last_artist_entry = null;
var first_letter = '';

function playlist_buildMenuArtists()
{
    // console.log(album_data_all);
    for (var i = 0; i < album_data_all.length; i++) {
        var artist_names = (Object.keys(album_data_all[i])[0]).split('|');

        if (first_letter != artist_names[1].substr(0, 1).toUpperCase()) {
            first_letter = artist_names[1].substr(0, 1).toUpperCase();
            $('<li id="' + (-i) + '" class="artist_liste"><div><b>' + first_letter + '</b></div></li>').appendTo("#artistliste");
        }

        $('<li id="' + i + '" class="artist_liste"><div>' + artist_names[0] + '</div></li>').appendTo("#artistliste");
    }

    $("li.artist_liste").mouseenter(function()
    {
        if (last_artist_entry != $(this)) {
            if (last_artist_entry) {
                last_artist_entry.css("background", "");
            }

            last_artist_entry = $(this);

            if (parseInt(last_artist_entry[0].id) >= 0) {
                last_artist_entry.css("background", "lightgrey");
                last_artist_entry.addClass("hasFocus");

                setTimeout(function() {
                    if (last_artist_entry.hasClass("hasFocus")) {
                        last_artist_entry.removeClass("hasFocus")
                        playlist_buildMenuAlbums(last_artist_entry[0].id);
                    }
                }, 800);
            }
        }
    });
}

var last_album_entry = null;

function playlist_buildMenuAlbums(artist_id)
{
    $("#trackliste").html('');

    var albums = album_data_all[artist_id][Object.keys(album_data_all[artist_id])[0]];
    // console.log('playlist_buildMenuAlbums(): artist_id =' + artist_id);

    var html = '';

    for (var j = 0; j < albums.length; j++) {
        var album = Object.keys(albums[j])[0];
        var album_data = albums[j][album];
        var album_id = album_data.musicbrainz_albumid;


        var jahr = " (";

        if (album_data.originalyear != '0') {
            jahr += album_data.originalyear;
        }

        if (album_data.year != album_data.originalyear) {
            if (album_data.originalyear != '0') {
                jahr += "/";
            }

            jahr += album_data.year;
        }

        jahr += ")";

        if (jahr == " ()") {
            jahr = "";
        }


        html += '<li class="album_liste" id="' + album_id + '"><div>'
            + '<a href="javascript:playlist_addAlbumToPlaylist(\'' + album_id + '\');"><span class="ui-icon ui-icon-plusthick"></span></a> | '
            + '<a href="javascript:album_playAlbum(\'' + album_id + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
            + album + jahr + '</div></li>';
    }

    $('#albumliste').html(html);

    $("li.album_liste").mouseenter(function()
    {
        if (last_album_entry != $(this)) {
            if (last_album_entry) {
                last_album_entry.css("background", "");
            }

            last_album_entry = $(this);
            last_album_entry.css("background", "lightgrey");
            last_album_entry.addClass("hasFocus");

            setTimeout(function() {
                if (last_album_entry.hasClass("hasFocus")) {
                    last_album_entry.removeClass("hasFocus")
                    album_showInformation(last_album_entry[0].id);
                    playlist_buildMenuTracks(artist_id, last_album_entry[0].id);
                }
            }, 800);
        }
    });

    playlist_redrawScrollbars();
}

var last_track_entry = null;

function playlist_buildMenuTracks(artist_id, album_id)
{
    var albums = album_data_all[artist_id][Object.keys(album_data_all[artist_id])[0]];
    var tracks = null;

    for (var j = 0; j < albums.length; j++) {
        var album = Object.keys(albums[j])[0];
        var album_data = albums[j][album];

        if (album_id == album_data.musicbrainz_albumid) {
            tracks = album_data.tracks;
            break;
        }
    }

    if (tracks == null) {
        console.log('playlist_buildMenuTracks(): tracks = null für artist_id=' + artist_id + ' / album_id=' + album_id);
    } else {
        var html = '';

        for (var k = 0; k < tracks.length; k++) {
            var title = tracks[k].title;

            if (tracks[k].studio == 0) {
                title = '<i>' + title + '</i>';
            }

            html += '<li class="track_liste" id="tlist' + tracks[k].trackid + '"><div id="' + tracks[k].trackid + '">'
                + '<a href="javascript:playlist_addTrackToPlaylist(\'' + tracks[k].trackid + '\');"><span class="ui-icon ui-icon-plusthick"></span></a> | '
                + '<a href="javascript:album_playTrack(\'' + tracks[k].trackid + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
                + title + '</div></li>';
        }

        $('#trackliste').html(html);

        $("li.track_liste").mouseenter(function()
        {
            if (last_track_entry != $(this)) {
                if (last_track_entry) {
                    last_track_entry.css("background", "");
                }

                last_track_entry = $(this);
                last_track_entry.css("background", "lightgrey");

                playlist_showTrackInformation($(this)[0].id.substr(5));
                cleanPlaylistBackground();
            }
        });

        playlist_redrawScrollbars();
    }
}

function playlist_redrawScrollbars()
{
    setTimeout(function() {
        $('.scroll-pane').jScrollPane();
        $('.jspVerticalBar').css('width', '10px');
    }, 200);
}

var last_playlist_entry = null;

function playlist_mouseEnter(elem)
{
    if (last_playlist_entry != elem) {
        if (last_playlist_entry) {
            last_playlist_entry.css("background", "");
        }

        last_playlist_entry = elem;
        last_playlist_entry.css("background", "lightgrey");

        playlist_showTrackInformation(elem[0].id.substr(5));
        // console.log(elem);
    }
}

function playlist_addTrackToPlaylist(trackid)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetTrackInfo.php?trackid=" + trackid
    }).done(function(data)
    {
        var track = data.trackdata;
        var html = $('#playliste').html();
        var title = track.title;

        if (track.studio == 0) {
            title = '<i>' + title + '</i>';
        }

        html += '<li class="play_liste" id="track' + trackid + '"><div>'
            + '<a href="javascript:playlist_removeFromPlaylist(\'' + trackid + '\');"><span class="ui-icon ui-icon-minusthick"></span></a> | '
            + '<a href="javascript:album_playTrack(\'' + trackid + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
            +  title + '</div></li>';

        $('#playliste').html(html);

        $("li.play_liste").mouseenter(function()
        {
            playlist_mouseEnter($(this));
        });

        playlist_redrawScrollbars();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function playlist_addAlbumToPlaylist(albumid)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetAlbumInfo.php?albumid=" + albumid
    }).done(function(data)
    {
        var tracks = data.tracksdata;
        var html = $('#playliste').html();

        for (var i = 0; i < tracks.length; i++) {
            var title = tracks[i].title;

            if (tracks[i].studio == 0) {
                title = '<i>' + title + '</i>';
            }

            html += '<li class="play_liste" id="track' + tracks[i].trackid + '"><div>'
            + '<a href="javascript:playlist_removeFromPlaylist(\'' + tracks[i].trackid + '\');"><span class="ui-icon ui-icon-minusthick"></span></a> | '
            + '<a href="javascript:album_playTrack(\'' + tracks[i].trackid + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
            + title + '</div></li>';
        }

        $('#playliste').html(html);

        $("li.play_liste").mouseenter(function()
        {
            playlist_mouseEnter($(this));
        });

        playlist_redrawScrollbars();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function playlist_removeFromPlaylist(trackid)
{
    $('#track' + trackid).remove();
    playlist_redrawScrollbars();
    cleanPlaylistBackground();
}

function playlist_cleanPlaylist()
{
    $('#playliste').html('');
    playlist_redrawScrollbars();
    cleanPlaylistBackground();
}

function playlist_sortPlaylist()
{
    var tracks = $('.play_liste');

    tracks.sort(function (a, b) {
        var x = a.textContent;
        var y = b.textContent;

        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    });

    $('#playliste').html('');
    $('#playliste').append(tracks);

    $("li.play_liste").mouseenter(function()
    {
        playlist_mouseEnter($(this));
    });

    cleanPlaylistBackground();
}

function cleanPlaylistBackground()
{
    if (last_playlist_entry) {
        last_playlist_entry.css("background", "");
        last_playlist_entry = null;
    }
}

function playlist_listPlaylists(playlist_name = '')
{
    $.ajax({
        url : "/inc/ofa_ControlMedia.php?type=playlist&list"
    }).done(function(data)
    {
        var playlists = JSON.parse(data).sort();

        $('#playlist_list').html('');
        $('#playlist_list').append($("<option />").text('New'));

        for (var i = 0; i < playlists.length; i++) {
            $('#playlist_list').append($("<option />").val(playlists[i]).text(playlists[i]));
        }

        if (playlist_name != '') {
            $('#playlist_list').val(playlist_name).change();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function playlist_playPlaylist(runtype)
{
    console.log('playlist_playPlaylist()');
    var tracks = $('.play_liste');
    var trackids = '';

    for (var i = 0; i < tracks.length; i++) {
        if (i > 0) {
            trackids += ',';
        }

        trackids += '"' + tracks[i].id.substr(5) + '"';
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
}

function playlist_savePlaylist()
{
    var playlist_name = $('#playlist_list option:selected').text();

    if (playlist_name == 'New') {
        $('#playlist_save_input').val('');
        $('#playlist_save_popup').css("top", (window.innerHeight - 180) + "px");
        $('#playlist_save_popup').css("display", "block");
        $('#playlist_save_input').focus();
    } else {
        savePlaylist(playlist_name);
    }
}

function savePlaylist(playlist_name)
{
    // console.log('savePlaylist()');
    var tracks = $('.play_liste');
    var trackids = '';

    for (var i = 0; i < tracks.length; i++) {
        if (i > 0) {
            trackids += ',';
        }

        trackids += '"' + tracks[i].id.substr(5) + '"';
    }

    $.ajax({
        method: "POST",
        dataType : "text",
        url : "/inc/ofa_ControlMedia.php?type=playlist&save&playlist_name=" + playlist_name,
        data: "trackids=" + trackids
    }).done(function(data)
    {
        var msg = JSON.parse(data);
        album_showAlbumInfo(msg.info);
        playlist_listPlaylists(msg.playlist_name);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function playlist_loadPlaylist()
{
    $.ajax({
        url : "/inc/ofa_ControlMedia.php?type=playlist&read&playlist_name=" + $('#playlist_list option:selected').text()
    }).done(function(data)
    {
        // console.log(data);
        var tracks = JSON.parse(data);
        var html = '';

        for (var i = 0; i < tracks.length; i++) {
            var title = tracks[i].title;

            if (tracks[i].studio == 0) {
                title = '<i>' + title + '</i>';
            }

            html += '<li class="play_liste" id="track' + tracks[i].trackid + '"><div>'
            + '<a href="javascript:playlist_removeFromPlaylist(\'' + tracks[i].trackid + '\');"><span class="ui-icon ui-icon-minusthick"></span></a> | '
            + '<a href="javascript:album_playTrack(\'' + tracks[i].trackid + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
            +  title + '</div></li>';
        }

        $('#playliste').html(html);

        $("li.play_liste").mouseenter(function()
        {
            playlist_mouseEnter($(this));
        });

        playlist_redrawScrollbars();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function playlist_deletePlaylist()
{
    $.ajax({
        url: "/inc/ofa_ControlMedia.php?type=playlist&delete&playlist_name=" + $('#playlist_list option:selected').text()
    }).done(function(data)
    {
        var msg = JSON.parse(data);
        album_showAlbumInfo(msg.info);
        playlist_listPlaylists();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function playlist_copyPlaylist()
{
    var playlist_name = $('#playlist_list option:selected').text();

    $.ajax({
        url : "/inc/ofa_ControlMedia.php?type=playlist&copy&playlist_name=" + playlist_name,
    }).done(function(data)
    {
        var msg = JSON.parse(data);
        album_showAlbumInfo(msg.info);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}
