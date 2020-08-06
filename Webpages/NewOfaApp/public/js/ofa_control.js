$(function() {
    control_listPlaylists();

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
});

function control_listPlaylists()
{
    $.ajax({
        url : "/inc/ofa_ControlMedia.php?type=playlist&list"
    }).done(function(data)
    {
        var playlists = JSON.parse(data).sort();

        for (var i = 0; i < playlists.length; i++) {
            var html = '<div class="media_playlist"><p><b><a href="javascript:control_playPlaylist(\'' + playlists[i] + '\', 1);">' + playlists[i] + '</a></b>'
                + '&nbsp;&nbsp;&nbsp;<a href="javascript:control_playPlaylist(\'' + playlists[i] + '\', 1);">Play</a> | '
                + '<a href="javascript:control_playPlaylist(\'' + playlists[i] + '\', 2);">Play random</a>'
                + '</p>';

            $('#media_playlists').append(html);
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function control_showTracks(albumid)
{
    if ($('#tracks_' + albumid).html() == '') {
        $.ajax({
            type : "GET",
            dataType : "json",
            url : "/inc/ofa_ControlMedia.php?type=album&albumid=" + albumid
        }).done(function(tracks)
        {
            if (tracks != 'ERROR') {
                album_startTimer();
            }

            var html = '';
            // console.log(tracks);

            for (var i = 0; i < tracks.length; i++) {
                html += '<p><i><a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 0);">'
                    + tracks[i].list_no + ' | ' + tracks[i].title + ' | ' + tracks[i].duration + '</a></i>&nbsp;&nbsp;&nbsp;'
                    + '<a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 0);">Play</a> | '
                    + '<a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 1);">Start playing</a> | '
                    + '<a href="javascript:control_addToRunningTracks(2, \'' + tracks[i].musicbrainz_trackid + '\');\">Add</a>'
                    + '</p>';
            }

            $('#tracks_' + albumid).append(html);
        }).fail(function(jqXHR, textStatus)
        {
            console.log("Database access failed: " + textStatus);
        });
    } else {
        $('#tracks_' + albumid).html('');
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
        $('html, body').animate({ scrollTop: $("#media_albums_intro_firstletters").offset().top - 235 }, 2000);
    } else {
        $('html, body').animate({ scrollTop: $("#" + letter).offset().top - 235 }, 2000);
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

    if (year_to < year_from) {
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

function control_playPictures(runtype)
{
    var url = "/inc/ofa_ControlMedia.php?type=pictures"
        + "&bildtyp=1"
        + "&jahr=" + $("#control_year").val()
        + "&landid=" + $("#control_country").val()
        + "&ortid=" + $("#control_location").val()
        + "&nummer_von="
        + "&nummer_bis="
        + "&wertung_min=0"
        + "&countperpage=1000000"
        + "&suchtext=" + $("#control_search").val()
        + "&runtype=" + runtype;

    // console.log(url);

    $.ajax({
        url: url
    }).done(function(data)
    {
        if (data != 'ERROR') {
            startSeriesTimer();
        }

        $("#lastinfo").html(data);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("control_playPictures(): Database access failed: " + textStatus);
        console.log(url);
    });
}
