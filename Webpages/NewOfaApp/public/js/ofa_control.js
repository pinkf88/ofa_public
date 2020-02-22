var last_artistid = 0;
var last_albumid = '';
var last_trackid = 0;
var album_data_all = null;

$(function() {
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

function album_showTracks(albumid)
{
    if ($('#tracks_' + albumid).html() == '') {
        $.ajax({
            type : "GET",
            dataType : "json",
            url : "inc/ofa_ControlMedia.php?type=album&albumid=" + albumid
        }).done(function(tracks)
        {
            if (tracks != 'ERROR') {
                startAlbumTimer();
            }

            var html = '';
            // console.log(tracks);

            for (var i = 0; i < tracks.length; i++) {
                html += '<p><i><a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 0);">'
                    + tracks[i].list_no + ' | ' + tracks[i].title + ' | ' + tracks[i].duration + '</a></i>&nbsp;&nbsp;&nbsp;'
                    + '<a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 0);">Play</a> | '
                    + '<a href="javascript:album_playTrack(\'' + tracks[i].musicbrainz_trackid + '\', 1);">Start playing</a>'
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

