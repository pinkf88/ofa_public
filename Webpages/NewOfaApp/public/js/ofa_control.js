const ECHO_SHOW_KELLER =        'G000MW0474340U70';
const ECHO_SHOW_WOHNZIMMER =    'G000RA11025706WK';
const ECHO_STUDIO_WOHNZIMMER =  'G2A0XL07039502L9';

const PLAYLIST_CATEGORIES = [
    'STUDIO_',
    'LIVE_',
    'JR_IN_CONCERT_',
    'SONSTIGE_'
];

const VIDEOID_KONZERTE = '44$13200';
const VIDEOID_SPIELFILME = '44$13412';
const VIDEOID_SERIEN = '44$13434';
const VIDEOID_DOKUMENTATIONEN = '44$13269';
const VIDEOID_COMEDY = '44$13433';
const VIDEOID_SPORT = '44$13461';
const VIDEOID_JRFILME = '44$13190';
const VIDEOID_TEMP = '44$13464';


$(function() {
    setTimeout( function() {
        $('#control_fancybox-overlay').hide();
    }, 15000);

    control_listPlaylists();

    $('#artist_search').on('input', function(e) {
        var input = $(this);
        var val = input.val().toLowerCase();

        if (input.data('lastval') != val) {
            input.data('lastval', val);
            // console.log(val);

            var elems = $('.media_album_artist_albums');
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

    $('#track_search').on('input', function(e) {
        var input = $(this);
        var val = input.val().toLowerCase();

        if (input.data('lastval_t') != val) {
            input.data('lastval_t', val);
            var vals = val.split(' ');
            // console.log(val);

            var elems = $('.media_track');
            // console.log(elems);

            for (var i = 0; i < elems.length; i++) {
                var hidden = true;

                if (val == '') {
                    hidden = false;
                } else {
                    var not_found = false;

                    for (var j = 0; j < vals.length; j++) {
                        if (elems[i].id.includes(vals[j]) == false) {
                            not_found = true;
                            break;
                        }
                    }

                    hidden = not_found;
                }

                elems[i].hidden = hidden;
            }

            if (val == '') {
                $('.media_track_firstletter').show();
                $('.tracks_sort_track_intro_firstletters').show();
            } else {
                $('.media_track_firstletter').hide();
                $('.tracks_sort_track_intro_firstletters').hide();
            }
        }
    });
    
    $('#serie_search').on('input', function(e) {
        var input = $(this);
        var val = input.val().toLowerCase();

        if (val.length > 2) {
            $('#media_serien').show();

            if (input.data('lastval_s') != val) {
                input.data('lastval_s', val);
                // console.log(val);

                var elems = $('.media_serien_serie');
                // console.log(elems);

                for (var i = 0; i < elems.length; i++) {
                    if (val == '' || elems[i].id.includes(val)) {
                        elems[i].hidden = false;
                    } else {
                        elems[i].hidden = true;
                    }
                }
            }
        } else {
            $('#media_serien').hide();
        }
    });

    $('#video_search').on('input', function(e) {
        var input = $(this);
        var val = input.val().toLowerCase();

        if (input.data('lastval_v') != val) {
            input.data('lastval_v', val);
            // console.log(val);

            var elems = $('.media_video');
            // console.log(elems);

            for (var i = 0; i < elems.length; i++) {
                if (val == '' || elems[i].id.includes(val)) {
                    elems[i].hidden = false;
                } else {
                    elems[i].hidden = true;
                }
            }
        }
    });

    $('.alben_sort_artist').show();
});

function control_listPlaylists()
{
    $.ajax({
        url :   '/inc/ofa_ControlMedia.php?type=playlist&list'
    }).done(function(data)
    {
        var playlists = JSON.parse(data).sort();

        for (var i = 0; i < playlists.length; i++) {
            var playlist_found = PLAYLIST_CATEGORIES.length - 1;
            var playlist = '';

            for (var j = 0; j < PLAYLIST_CATEGORIES.length; j++) {
                if (playlists[i].indexOf(PLAYLIST_CATEGORIES[j]) == 0) {
                    playlist_found = j;
                    break;
                }
            }

            var playlist_id = 'PLAYLIST_' + PLAYLIST_CATEGORIES[playlist_found];

            if (playlist_found == PLAYLIST_CATEGORIES.length - 1) {
                playlist = playlists[i];
            } else {
                playlist = playlists[i].substr(PLAYLIST_CATEGORIES[playlist_found].length);
            }

            var html = '<div class="media_playlist"><p>'
                + '<a href="javascript:control_playPlaylist(\'' + playlists[i] + '\', 1);"><span class="ui-icon ui-icon-play"></span></a> | '
                + '<a href="javascript:control_playPlaylist(\'' + playlists[i] + '\', 2);"><span class="ui-icon ui-icon-arrowthick-1-e"></span></a>'
                + '&nbsp;&nbsp;&nbsp;<a href="javascript:control_playPlaylist(\'' + playlists[i] + '\', 1);">' + playlist + '</a></p>';

            $('#' + playlist_id).append(html);
        }

        control_updateAlbums();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ERROR control_listPlaylists(): " + textStatus);
    });
}

function control_updateAlbums()
{
    $.ajax({
        url :   '/inc/ofa_ControlMedia.php?type=starlist&list'
    }).done(function(data)
    {
        var starlist = JSON.parse(data);

        for (var i = 0; i < starlist.length; i++) {
            $('#artist_star_' + starlist[i]).addClass('ui-icon-red');
            $('#year_star_' + starlist[i]).addClass('ui-icon-red');
            $('#artist_media_album_' + starlist[i]).addClass('bgcolor_lightyellow');
            $('#year_media_album_' + starlist[i]).addClass('bgcolor_lightyellow');
        }

        $.ajax({
            url :   '/inc/ofa_ControlMedia.php?type=flaglist&list'
        }).done(function(data)
        {
            var flaglist = JSON.parse(data);
    
            for (var i = 0; i < flaglist.length; i++) {
                $('#artist_flag_' + flaglist[i]).addClass('ui-icon-red');
                $('#year_flag_' + flaglist[i]).addClass('ui-icon-red');
                $('#artist_media_album_' + flaglist[i]).addClass('bgcolor_lightyellow');
                $('#year_media_album_' + flaglist[i]).addClass('bgcolor_lightyellow');
            }

            control_listVideolist();
        }).fail(function(jqXHR, textStatus)
        {
            console.log('ERROR control_updateAlbums(): ' + textStatus);
        });        
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_updateAlbums(): ' + textStatus);
    });    
}

function control_listVideolist()
{
    $.ajax({
        url :   '/inc/ofa_ControlMedia.php?type=videolist&list'
    }).done(function(data)
    {
        var videolist = JSON.parse(data);
        // console.log(videolist);
        
        var konzerte = findNode(VIDEOID_KONZERTE, videolist);
        var title_old = '';
        var html = '<h5 id="Konzerte" class="control">Konzerte</h5><hr class="control">';

        for (var i = 0; i < konzerte.children.length; i++) {
            html += '<div id="' + konzerte.children[i].title.toLowerCase() + '" class="media_video_artist_videos"><p class="media_video_artist">'
                + '<b>' + konzerte.children[i].title + '</b></div>';

            for (var j = 0; j < konzerte.children[i].children.length; j++) {
                html += '<div id="' + konzerte.children[i].children[j].title.toLowerCase() + '" class="media_video">'
                    + getVideoLink(konzerte.children[i].children[j].url, konzerte.children[i].children[j].title,
                        konzerte.children[i].children[j].duration, konzerte.children[i].children[j].resolution, konzerte.children[i].children[j].size);
            }
        }

        var spielfilme = findNode(VIDEOID_SPIELFILME, videolist);
        html += '<h5 id="Spielfilme" class="control">Spielfilme</h5><hr class="control">';

        for (var i = 0; i < spielfilme.children.length; i++) {
            html += '<div id="' + spielfilme.children[i].title.toLowerCase() + '" class="media_video_artist_videos"><p class="media_video_artist">'
                + '<b>' + spielfilme.children[i].title + '</b></div>';

            for (var j = 0; j < spielfilme.children[i].children.length; j++) {
                html += '<div id="' + spielfilme.children[i].children[j].title.toLowerCase() + '" class="media_video">'
                    + getVideoLink(spielfilme.children[i].children[j].url, spielfilme.children[i].children[j].title,
                        spielfilme.children[i].children[j].duration, spielfilme.children[i].children[j].resolution, spielfilme.children[i].children[j].size);
            }
        }

        var serien = findNode(VIDEOID_SERIEN, videolist);
        html += '<h5 id="Serien" class="control">Serien</h5><hr class="control">';

        for (var i = 0; i < serien.children.length; i++) {
            html += '<div id="' + serien.children[i].title.toLowerCase() + '" class="media_video_artist_videos"><p class="media_video_caption">'
                + serien.children[i].title + '</p></div>';

            var children = [];
            getChildren(findNode(serien.children[i].id, videolist), children);

            for (var j = 0; j < children.length; j++) {
                // console.log('children', children[j]);

                html += '<div id="' + children[j].title.toLowerCase() + '" class="media_video">';

                title = '';
                getTitle(children[j], serien.children[i]);

                if (title_old != title) {
                    title_old = title;

                    if (title != '') {
                        html += '<p class="media_video_subcaption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }
        }

        var dokumentationen = findNode(VIDEOID_DOKUMENTATIONEN, videolist);
        html += '<h5 id="Dokumentationen" class="control">Dokumentationen</h5><hr class="control">';

        for (var i = 0; i < dokumentationen.children.length; i++) {
            html += '<div id="' + dokumentationen.children[i].title.toLowerCase() + '" class="media_video_artist_videos"><p class="media_video_caption">'
                + dokumentationen.children[i].title + '</p></div>';

            var children = [];
            getChildren(findNode(dokumentationen.children[i].id, videolist), children);

            for (var j = 0; j < children.length; j++) {
                // console.log('children', children[j]);

                html += '<div id="' + children[j].title.toLowerCase() + '" class="media_video">';

                title = '';
                getTitle(children[j], dokumentationen.children[i]);

                if (title_old != title) {
                    title_old = title;

                    if (title != '') {
                        html += '<p class="media_video_subcaption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }
        }

        var comedy = findNode(VIDEOID_COMEDY, videolist);
        html += '<h5 id="Comedy" class="control">Comedy</h5><hr class="control">';

        for (var i = 0; i < comedy.children.length; i++) {
            html += '<div id="' + comedy.children[i].title.toLowerCase() + '" class="media_video_artist_videos"><p class="media_video_caption">'
                + comedy.children[i].title + '</p></div>';

            var children = [];
            getChildren(findNode(comedy.children[i].id, videolist), children);

            for (var j = 0; j < children.length; j++) {
                // console.log('children', children[j]);

                html += '<div id="' + children[j].title.toLowerCase() + '" class="media_video">';

                title = '';
                getTitle(children[j], comedy.children[i]);

                if (title_old != title) {
                    title_old = title;

                    if (title != '') {
                        html += '<p class="media_video_subcaption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }
        }

        var sport = findNode(VIDEOID_SPORT, videolist);
        html += '<h5 id="Sport" class="control">Sport</h5><hr class="control">';

        for (var i = 0; i < sport.children.length; i++) {
            html += '<div id="' + sport.children[i].title.toLowerCase() + '" class="media_video_artist_videos"><p class="media_video_caption">'
                + sport.children[i].title + '</p></div>';

            var children = [];
            getChildren(findNode(sport.children[i].id, videolist), children);

            for (var j = 0; j < children.length; j++) {
                // console.log('children', children[j]);

                html += '<div id="' + children[j].title.toLowerCase() + '" class="media_video">';

                title = '';
                getTitle(children[j], sport.children[i]);

                if (title_old != title) {
                    title_old = title;

                    if (title != '') {
                        html += '<p class="media_video_subcaption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }
        }

        var jrfilme = findNode(VIDEOID_JRFILME, videolist);
        html += '<h5 id="JR-Filme" class="control">JR-Filme</h5><hr class="control">';

        for (var i = 0; i < jrfilme.children.length; i++) {
            html += '<div id="' + jrfilme.children[i].title.toLowerCase() + '" class="media_video_artist_videos"><p class="media_video_caption">'
                + jrfilme.children[i].title + '</p></div>';

            var children = [];
            getChildren(findNode(jrfilme.children[i].id, videolist), children);

            for (var j = 0; j < children.length; j++) {
                // console.log('children', children[j]);

                html += '<div id="' + children[j].title.toLowerCase() + '" class="media_video">';

                title = '';
                getTitle(children[j], jrfilme.children[i]);

                if (title_old != title) {
                    title_old = title;

                    if (title != '') {
                        html += '<p class="media_video_subcaption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }
        }

        var temp = findNode(VIDEOID_TEMP, videolist);
        html += '<h5 id="Temp" class="control">Temp</h5><hr class="control">';

        for (var i = 0; i < temp.children.length; i++) {
            html += '<div id="' + temp.children[i].title.toLowerCase() + '" class="media_video">'
                + getVideoLink(temp.children[i].url, temp.children[i].title, temp.children[i].duration, temp.children[i].resolution, temp.children[i].size);
        }

        $('#media_videolist').append(html);

        $('html, body').animate({ scrollTop: $('body').offset().top - 100 }, 10);
        $('#control_fancybox-overlay').hide();

        control_admin('info_iobroker');
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_listVideolist(): ' + textStatus);
        $('#control_fancybox-overlay').hide();
        control_admin('info_iobroker');
    });    
}

function control_tab(tab)
{
    $('html, body').animate({ scrollTop: $('body').offset().top - 100 }, 10);

    $('#control_tab_albums').hide();
    $('#control_tab_tracks').hide();
    $('#control_tab_playlists').hide();
    $('#control_tab_pictures').hide();
    $('#control_tab_videos').hide();
    $('#control_tab_home').hide();
    $('#control_tab_admin').hide();
    $('#control_tab_' + tab).show();

    if (tab == 'albums' || tab == 'tracks' || tab == 'playlists') {
        $('#control_left_music').show();
    } else {
        $('#control_left_music').hide();
    }

    if (tab == 'pictures') {
        $('#control_left_pictures').show();
    } else {
        $('#control_left_pictures').hide();
    }

    if (tab == 'videos') {
        $('#control_left_video').show();
    } else {
        $('#control_left_video').hide();
    }
}
