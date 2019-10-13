var last_artistid = 0;
var last_albumid = '';
var last_trackid = 0;
var album_data_all = null;

$(function() {
    if ($('#leftside_album_tabelle').length) {
        $.ajax({
            url : 'inc/ofa_GetAllMusic.php'
        }).done(function(data)
        {
            album_data_all = JSON.parse(data);
            album_buildMenuArtists();

            if (Cookies.get('album_view') == 'undefined') {
                Cookies.set('album_view', 'liste');
            }

            album_changeView(Cookies.get('album_view'));

            $('#sp_albumliste').height($(window).height() - 60);
            $('#sp_trackliste').height($(window).height() - 60);
            $('#sp_playliste').height($(window).height() - 120);
            $('#playliste').sortable();
            $('#playliste').disableSelection();
            $('.jspVerticalBar').css('width', '10px');

            album_listPlaylists();

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
        }).fail(function(jqXHR, textStatus)
        {
            console.log("Database access failed: " + textStatus);
        });
    } else {
        album_listPlaylists();

        $("#artist_search").on("input", function(e) {
            var input = $(this);
            var val = input.val().toLowerCase();

            if (input.data("lastval") != val) {
                input.data("lastval", val);
                // console.log(val);

                var elems = $(".media_album_artist_albums");
                // console.log(elems);

                for (var i = 0; i < elems.length; i++) {
                    if (val == '' || elems[i].id.includes(val)) {
                        elems[i].hidden = false;
                    } else {
                        elems[i].hidden = true;
                    }
                }

                if (val == '') {
                    $('.media_album_firstletter').show();
                    $('.media_albums_intro_firstletters').show();
                } else {
                    $('.media_album_firstletter').hide();
                    $('.media_albums_intro_firstletters').hide();
                }
            }

        });
    }
});

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

function album_changeView(view)
{
    if (view == 'tabelle') {
        $("#album_view_change").html('<a href="javascript:album_changeView(\'liste\');">Liste</a>');
        $('#leftside_album_tabelle').show();
        $('#leftside_album_liste').hide();
        $('#rightside_top').show();

        Cookies.set('album_view', 'tabelle');
    } else {
        $("#album_view_change").html('<a href="javascript:album_changeView(\'tabelle\');">Tabelle</a>');
        $('#leftside_album_tabelle').hide();
        $('#leftside_album_liste').show();
        $('#rightside_top').hide();

        Cookies.set('album_view', 'liste');
    }
}

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

function album_showTrackInformation(trackid)
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
        console.log("album_showTrackInformation(): textStatus=" + textStatus + '. trackid=' + trackid);
    });

    $(".fancybox").fancybox();
}

var last_artist_entry = null;
var first_letter = '';

function album_buildMenuArtists()
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
                        album_buildMenuAlbums(last_artist_entry[0].id);
                    }
                }, 800);
            }
        }
    });
}

var last_album_entry = null;

function album_buildMenuAlbums(artist_id)
{
    $("#trackliste").html('');

    var albums = album_data_all[artist_id][Object.keys(album_data_all[artist_id])[0]];
    // console.log('album_buildMenuAlbums(): artist_id =' + artist_id);

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
            + '<a href="javascript:album_addAlbumToPlaylist(\'' + album_id + '\');"><span class="ui-icon ui-icon-plusthick"></span></a> | '
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
                    album_buildMenuTracks(artist_id, last_album_entry[0].id);
                }
            }, 800);
        }
    });

    redrawScrollbars();
}

var last_track_entry = null;

function album_buildMenuTracks(artist_id, album_id)
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
        console.log('album_buildMenuTracks(): tracks = null für artist_id=' + artist_id + ' / album_id=' + album_id);
    } else {
        var html = '';

        for (var k = 0; k < tracks.length; k++) {
            var title = tracks[k].title;

            if (tracks[k].genre == 'Live') {
                title = '<i>' + title + '</i>';
            }

            html += '<li class="track_liste" id="tlist' + tracks[k].trackid + '"><div id="' + tracks[k].trackid + '">'
                + '<a href="javascript:album_addTrackToPlaylist(\'' + tracks[k].trackid + '\');"><span class="ui-icon ui-icon-plusthick"></span></a> | '
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

                album_showTrackInformation($(this)[0].id.substr(5));
                cleanPlaylistBackground();
            }
        });

        redrawScrollbars();
    }
}

var album_timer = null;

function startAlbumTimer()
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
        type : "GET",
        url : "inc/ofa_UpdateAlbumOwner.php?albumid=" + albumid + "&ownerid=" + ownerid
    }).done(function(data)
    {
        if (data != 'ERROR') {
            startAlbumTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_playAlbum(albumid)
{
    var roomid = 1;
    
    if ($("[name='roomid']")[0] != undefined) {
        $("[name='roomid']")[0].value;
    }

    $.ajax({
        type : "GET",
        url : "inc/ofa_ControlMedia.php?type=album&albumid=" + albumid + "&roomid=" + roomid
    }).done(function(data)
    {
        if (data != 'ERROR') {
            startAlbumTimer();
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
        type : "GET",
        url : "inc/ofa_ControlMedia.php?type=album&artist=" + artist + "&runtype=" + runtype + "&roomid=" + roomid
    }).done(function(data)
    {
        if (data != 'ERROR') {
            startAlbumTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("album_playArtist(): Database access failed: " + textStatus);
    });
}

function album_playTrack(trackid)
{
    var roomid = 1;
    
    if ($("[name='roomid']")[0] != undefined) {
        $("[name='roomid']")[0].value;
    }


    $.ajax({
        type : "GET",
        url : "inc/ofa_ControlMedia.php?type=track&trackid=" + trackid + "&roomid=" + roomid
    }).done(function(data)
    {
        if (data != 'ERROR') {
            startAlbumTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function redrawScrollbars()
{
    setTimeout(function() {
        $('.scroll-pane').jScrollPane();
        $('.jspVerticalBar').css('width', '10px');
    }, 200);
}

var no_track_running_counter = 0;

function album_controlTrack(todo)
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
            // startAlbumTimer();   warum?
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

var last_playlist_entry = null;

function playlistMouseEnter(elem)
{
    if (last_playlist_entry != elem) {
        if (last_playlist_entry) {
            last_playlist_entry.css("background", "");
        }

        last_playlist_entry = elem;
        last_playlist_entry.css("background", "lightgrey");

        album_showTrackInformation(elem[0].id.substr(5));
        // console.log(elem);
    }
}

function album_addTrackToPlaylist(trackid)
{
    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetTrackInfo.php?trackid=" + trackid
    }).done(function(data)
    {
        var track = data.trackdata;
        var html = $('#playliste').html();
        var title = track.title;

        if (track.genre == 'Live') {
            title = '<i>' + title + '</i>';
        }

        html += '<li class="play_liste" id="track' + trackid + '"><div>'
            + '<a href="javascript:album_removeFromPlaylist(\'' + trackid + '\');"><span class="ui-icon ui-icon-minusthick"></span></a> | '
            + '<a href="javascript:album_playTrack(\'' + trackid + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
            +  title + '</div></li>';

        $('#playliste').html(html);

        $("li.play_liste").mouseenter(function()
        {
            playlistMouseEnter($(this));
        });

        redrawScrollbars();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_addAlbumToPlaylist(albumid)
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

            if (tracks[i].genre == 'Live') {
                title = '<i>' + title + '</i>';
            }

            html += '<li class="play_liste" id="track' + tracks[i].trackid + '"><div>'
            + '<a href="javascript:album_removeFromPlaylist(\'' + tracks[i].trackid + '\');"><span class="ui-icon ui-icon-minusthick"></span></a> | '
            + '<a href="javascript:album_playTrack(\'' + tracks[i].trackid + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
            + title + '</div></li>';
        }

        $('#playliste').html(html);

        $("li.play_liste").mouseenter(function()
        {
            playlistMouseEnter($(this));
        });

        redrawScrollbars();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_removeFromPlaylist(trackid)
{
    $('#track' + trackid).remove();
    redrawScrollbars();
    cleanPlaylistBackground();
}

function album_cleanPlaylist()
{
    $('#playliste').html('');
    redrawScrollbars();
    cleanPlaylistBackground();
}

function album_sortPlaylist()
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
        playlistMouseEnter($(this));
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

function album_listPlaylists(playlist_name = '')
{
    $.ajax({
        url : "inc/ofa_ControlMedia.php?type=playlist&list"
    }).done(function(data)
    {
        var playlists = JSON.parse(data).sort();

        if ($('#leftside_album_tabelle').length) {
            $('#playlist_list').html('');
            $('#playlist_list').append($("<option />").text('New'));

            for (var i = 0; i < playlists.length; i++) {
                $('#playlist_list').append($("<option />").val(playlists[i]).text(playlists[i]));
            }

            if (playlist_name != '') {
                $('#playlist_list').val(playlist_name).change();
            }
        } else {
            for (var i = 0; i < playlists.length; i++) {
                var html = '<div class="media_playlist"><p>' + playlists[i] + '</p>'
                    + '<p>'
                    + '<a href="javascript:album_playPlaylistByName(\'' + playlists[i] + '\', 1);">Play all</a> | '
                    + '<a href="javascript:album_playPlaylistByName(\'' + playlists[i] + '\', 2);">Play random</a>'
                    + '</p>';

                $('#media_playlists').append(html);
            }
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_playPlaylist(runtype)
{
    console.log('album_playPlaylist()');
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
        url : "inc/ofa_ControlMedia.php?type=playlist&play&runtype=" + runtype,
        data: "trackids=" + trackids
    }).done(function(data)
    {
        if (data != 'ERROR') {
            startAlbumTimer();
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_playPlaylistByName(playlist_name, runtype)
{
    console.log('album_playPlaylistByName()');

    $.ajax({
        url : "inc/ofa_ControlMedia.php?type=playlist&read&playlist_name=" + playlist_name
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
            url : "inc/ofa_ControlMedia.php?type=playlist&play&runtype=" + runtype,
            data: "trackids=" + trackids
        }).done(function(data)
        {
            if (data != 'ERROR') {
                startAlbumTimer();
            }
        }).fail(function(jqXHR, textStatus)
        {
            console.log("Database access failed: " + textStatus);
        });
    });
}

function album_savePlaylist()
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
        url : "inc/ofa_ControlMedia.php?type=playlist&save&playlist_name=" + playlist_name,
        data: "trackids=" + trackids
    }).done(function(data)
    {
        var msg = JSON.parse(data);
        album_showAlbumInfo(msg.info);
        album_listPlaylists(msg.playlist_name);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_loadPlaylist()
{
    $.ajax({
        url : "inc/ofa_ControlMedia.php?type=playlist&read&playlist_name=" + $('#playlist_list option:selected').text()
    }).done(function(data)
    {
        // console.log(data);
        var tracks = JSON.parse(data);
        var html = '';

        for (var i = 0; i < tracks.length; i++) {
            var title = tracks[i].title;

            if (tracks[i].genre == 'Live') {
                title = '<i>' + title + '</i>';
            }

            html += '<li class="play_liste" id="track' + tracks[i].trackid + '"><div>'
            + '<a href="javascript:album_removeFromPlaylist(\'' + tracks[i].trackid + '\');"><span class="ui-icon ui-icon-minusthick"></span></a> | '
            + '<a href="javascript:album_playTrack(\'' + tracks[i].trackid + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
            +  title + '</div></li>';
        }

        $('#playliste').html(html);

        $("li.play_liste").mouseenter(function()
        {
            playlistMouseEnter($(this));
        });

        redrawScrollbars();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_deletePlaylist()
{
    $.ajax({
        url: "inc/ofa_ControlMedia.php?type=playlist&delete&playlist_name=" + $('#playlist_list option:selected').text()
    }).done(function(data)
    {
        var msg = JSON.parse(data);
        album_showAlbumInfo(msg.info);
        album_listPlaylists();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_copyPlaylist()
{
    var playlist_name = $('#playlist_list option:selected').text();

    $.ajax({
        url : "inc/ofa_ControlMedia.php?type=playlist&copy&playlist_name=" + playlist_name,
    }).done(function(data)
    {
        var msg = JSON.parse(data);
        album_showAlbumInfo(msg.info);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function album_scrollToLetter(letter = '')
{
    if (letter == '') {
        $('html, body').animate({ scrollTop: $("#media_albums_intro_firstletters").offset().top - 235 }, 2000);
    } else {
        $('html, body').animate({ scrollTop: $("#" + letter).offset().top - 235 }, 2000);
    }
}

function album_showAlbumInfo(info)
{
    var html = '';
    
    if (info.title == undefined) {
        html = '<b>' + info + '</b>';
    } else {
        html = '<b>' + info.title + '</b><br>' + info.artist + '<br><i>' + info.album + '</i>';
        $('#control_cover').html('<img class="control_cover" src="' + info.image.url + '">');
    }

    $("#album_info").html(html);
}
