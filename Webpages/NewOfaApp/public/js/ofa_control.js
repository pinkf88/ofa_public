const ECHO_SHOW_KELLER =        'G000MW0474340U70';
const ECHO_SHOW_WOHNZIMMER =    'G000RA11025706WK';
const ECHO_STUDIO_WOHNZIMMER =  'G2A0XL07039502L9';

const PLAYLIST_CATEGORIES = [
    'STUDIO_',
    'LIVE_',
    'JR_IN_CONCERT_',
    'SONSTIGE_'
];


$(function() {
    setTimeout( function() {
        control_overlayOff();
    }, 15000);

    control_tab('home');

    $('.navbar-collapse ul li a:not(.dropdown-toggle)').bind('click touchstart', function () {
        $('.navbar-toggle').click();
    });

    $('dt').click(function() {
		$(this).next('dd').slideToggle('fast');
		$(this).children('a').toggleClass('menu_closed menu_open');
	});
    
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

        if (val.length > 0) {
            $('.media_videocontents').hide();
        } else {
            $('.media_videocontents').show();
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

        control_updateStarFlagList();
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ERROR control_listPlaylists(): " + textStatus);
    });
}

function control_updateStarFlagList()
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

            control_overlayOff();
            control_listVideolist();
        }).fail(function(jqXHR, textStatus)
        {
            console.log('ERROR control_updateStarFlagList(): ' + textStatus);
        });        
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_updateStarFlagList(): ' + textStatus);
    });    
}

function control_overlayOn()
{
    $('#control_fancybox-overlay').show();
}

function control_overlayOff()
{
    $('#control_fancybox-overlay').hide();
}

function control_tab(tab)
{
    $('html, body').animate({ scrollTop: $('body').offset().top - 100 }, 10);
    $("#tooltip").hide();

    $('#control_tab_albums').hide();
    $('#control_tab_tracks').hide();
    $('#control_tab_playlists').hide();
    $('#control_tab_pictures').hide();
    $('#control_tab_videos').hide();
    $('#control_tab_analytics').hide();
    $('#control_tab_home').hide();
    $('#control_tab_admin').hide();
    $('#control_tab_' + tab).show();

    if (tab == 'home') {
        control_runHome();
    }

    if (tab == 'tracks') {
        if ($('#control_tracks').html() == '') {
            control_overlayOn();

            $.ajax({
                url :   '/inc/ofa_ControlTracks.php'
            }).done(function(html)
            {
                $('#control_tracks').html(html);

                setTimeout(function() {
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

                    control_overlayOff();
                }, 5000);
            }).fail(function(jqXHR, textStatus)
            {
                console.log('ERROR control_tab(): ' + textStatus);
                control_overlayOff();
            });                
        }
    }

    // $(this).next('dd').slideToggle('fast');
    // $(this).children('a').toggleClass('menu_closed menu_open');

    if (tab == 'albums' || tab == 'tracks' || tab == 'playlists') {
		$('#menu_control_music_content').show();
		$('#menu_control_music_link').toggleClass('menu_closed menu_open');
    }

    if (tab == 'pictures') {
		$('#menu_control_pictures_content').show();
		$('#menu_control_pictures_link').toggleClass('menu_closed menu_open');
    }

    if (tab == 'videos') {
		$('#menu_control_videos_content').show();
		$('#menu_control_videos_link').toggleClass('menu_closed menu_open');
    }
}
