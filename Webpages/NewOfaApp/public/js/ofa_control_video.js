const VIDEOID_KONZERTE = '44$13200';
const VIDEOID_SPIELFILME = '44$13412';
const VIDEOID_SERIEN = '44$13434';
const VIDEOID_DOKUMENTATIONEN = '44$15638';
const VIDEOID_COMEDY = '44$13433';
const VIDEOID_SPORT = '44$13461';
const VIDEOID_JRFILME = '44$13190';
const VIDEOID_TEMP = '44$13464';


function getHtmlId(text)
{
    return 'video_' + text.toLowerCase().replace(/ /g, '_').replace(/:/g, '').replace(/\//g, '').replace(/&/g, '');
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
        var html = '<h5 id="Konzerte" class="control video_caption">Konzerte</h5><hr class="control video_caption">';
        var contents = '<p class="media_video_caption"><a href="javascript:control_scrollToCaption(\'Konzerte\')">Konzerte</a></p>';

        for (var i = 0; i < konzerte.children.length; i++) {
            html += '<div id="' + getHtmlId(konzerte.children[i].title) + '" class="media_video_artist_videos"><p class="media_video_artist video_caption">'
                + '<b>' + konzerte.children[i].title + '</b> <a href="javascript:control_scrollToCaption();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a></p>';

            contents += '<p class="media_video_captionsub1"><a href="javascript:control_scrollToCaption(\'' + getHtmlId(konzerte.children[i].title) + '\')">' + konzerte.children[i].title + '</a></p>';

            for (var j = 0; j < konzerte.children[i].children.length; j++) {
                html += '<div id="' + konzerte.children[i].children[j].title.toLowerCase() + '" class="media_video">'
                    + getVideoLink(konzerte.children[i].children[j].url, konzerte.children[i].children[j].title,
                        konzerte.children[i].children[j].duration, konzerte.children[i].children[j].resolution, konzerte.children[i].children[j].size);
            }

            html += '</div>';
        }

        var spielfilme = findNode(VIDEOID_SPIELFILME, videolist);
        html += '<h5 id="Spielfilme" class="control video_caption">Spielfilme</h5><hr class="control video_caption">';
        contents += '<p class="media_video_caption"><a href="javascript:control_scrollToCaption(\'Spielfilme\')">Spielfilme</a></p>';

        for (var i = 0; i < spielfilme.children.length; i++) {
            html += '<div id="' + getHtmlId(spielfilme.children[i].title) + '" class="media_video_artist_videos"><p class="media_video_artist video_caption">'
                + '<b>' + spielfilme.children[i].title + '</b> <a href="javascript:control_scrollToCaption();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a></p>';

            contents += '<p class="media_video_captionsub1"><a href="javascript:control_scrollToCaption(\'' + getHtmlId(spielfilme.children[i].title) + '\')">' + spielfilme.children[i].title + '</a></p>';

            for (var j = 0; j < spielfilme.children[i].children.length; j++) {
                html += '<div id="' + spielfilme.children[i].children[j].title.toLowerCase() + '" class="media_video">'
                    + getVideoLink(spielfilme.children[i].children[j].url, spielfilme.children[i].children[j].title,
                        spielfilme.children[i].children[j].duration, spielfilme.children[i].children[j].resolution, spielfilme.children[i].children[j].size);
            }

            html += '</div>';
        }

        var serien = findNode(VIDEOID_SERIEN, videolist);
        html += '<h5 id="Serien" class="control video_caption">Serien</h5><hr class="control video_caption">';
        contents += '<p class="media_video_caption"><a href="javascript:control_scrollToCaption(\'Serien\')">Serien</a></p>';

        for (var i = 0; i < serien.children.length; i++) {
            html += '<div id="' + getHtmlId(serien.children[i].title) + '" class="media_video_artist_videos"><p class="media_video_artist video_caption">'
                + serien.children[i].title + ' <a href="javascript:control_scrollToCaption();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a></p>';

            contents += '<p class="media_video_captionsub1"><a href="javascript:control_scrollToCaption(\'' + getHtmlId(serien.children[i].title) + '\')">' + serien.children[i].title + '</a></p>';

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
                        html += '<p class="media_video_subcaption video_caption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }

            html += '</div>';
        }

        var dokumentationen = findNode(VIDEOID_DOKUMENTATIONEN, videolist);
        html += '<h5 id="Dokumentationen" class="control video_caption">Dokumentationen</h5><hr class="control video_caption">';
        contents += '<p class="media_video_caption"><a href="javascript:control_scrollToCaption(\'Dokumentationen\')">Dokumentationen</a></p>';

        for (var i = 0; i < dokumentationen.children.length; i++) {
            html += '<div id="' + getHtmlId(dokumentationen.children[i].title) + '" class="media_video_artist_videos"><p class="media_video_artist video_caption">'
                + dokumentationen.children[i].title + ' <a href="javascript:control_scrollToCaption();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a></p>';

            contents += '<p class="media_video_captionsub1"><a href="javascript:control_scrollToCaption(\'' + getHtmlId(dokumentationen.children[i].title) + '\')">' + dokumentationen.children[i].title + '</a></p>';

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
                        html += '<p class="media_video_subcaption video_caption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }

            html += '</div>';
        }

        var comedy = findNode(VIDEOID_COMEDY, videolist);
        html += '<h5 id="Comedy" class="control video_caption">Comedy</h5><hr class="control video_caption">';
        contents += '<p class="media_video_caption"><a href="javascript:control_scrollToCaption(\'Comedy\')">Comedy</a></p>';

        for (var i = 0; i < comedy.children.length; i++) {
            html += '<div id="' + getHtmlId(comedy.children[i].title) + '" class="media_video_artist_videos"><p class="media_video_artist video_caption">'
                + comedy.children[i].title + ' <a href="javascript:control_scrollToCaption();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a></p>';

            contents += '<p class="media_video_captionsub1"><a href="javascript:control_scrollToCaption(\'' + getHtmlId(comedy.children[i].title) + '\')">' + comedy.children[i].title + '</a></p>';

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
                        html += '<p class="media_video_subcaption video_caption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }

            html += '</div>';
        }

        var sport = findNode(VIDEOID_SPORT, videolist);
        html += '<h5 id="Sport" class="control video_caption">Sport</h5><hr class="control video_caption">';
        contents += '<p class="media_video_caption"><a href="javascript:control_scrollToCaption(\'Sport\')">Sport</a></p>';

        for (var i = 0; i < sport.children.length; i++) {
            html += '<div id="' + getHtmlId(sport.children[i].title) + '" class="media_video_artist_videos"><p class="media_video_artist video_caption">'
                + sport.children[i].title + ' <a href="javascript:control_scrollToCaption();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a></p>';

            contents += '<p class="media_video_captionsub1"><a href="javascript:control_scrollToCaption(\'' + getHtmlId(sport.children[i].title) + '\')">' + sport.children[i].title + '</a></p>';

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
                        html += '<p class="media_video_subcaption video_caption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }

            html += '</div>';
        }

        var jrfilme = findNode(VIDEOID_JRFILME, videolist);
        html += '<h5 id="JR-Filme" class="control video_caption">JR-Filme</h5><hr class="control video_caption">';
        contents += '<p class="media_video_caption"><a href="javascript:control_scrollToCaption(\'JR-Filme\')">JR-Filme</a></p>';

        for (var i = 0; i < jrfilme.children.length; i++) {
            html += '<div id="jr_' + getHtmlId(jrfilme.children[i].title) + '" class="media_video_artist_videos"><p class="media_video_artist video_caption">'
                + jrfilme.children[i].title + ' <a href="javascript:control_scrollToCaption();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a></p>';

            contents += '<p class="media_video_captionsub1"><a href="javascript:control_scrollToCaption(\'jr_' + getHtmlId(jrfilme.children[i].title) + '\')">' + jrfilme.children[i].title + '</a></p>';

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
                        html += '<p class="media_video_subcaption video_caption">' + title + '</p>';
                    }
                }

                html += getVideoLink(children[j].url, children[j].title, children[j].duration, children[j].resolution, children[j].size);
            }

            html += '</div>';
        }

        var temp = findNode(VIDEOID_TEMP, videolist);
        html += '<h5 id="Temp" class="control video_caption">Temp</h5><hr class="control video_caption">';
        contents += '<p class="media_video_caption"><a href="javascript:control_scrollToCaption(\'Temp\')">Temp</a></p>';

        for (var i = 0; i < temp.children.length; i++) {
            html += '<div id="' + getHtmlId(temp.children[i].title) + '" class="media_video">'
                + getVideoLink(temp.children[i].url, temp.children[i].title, temp.children[i].duration, temp.children[i].resolution, temp.children[i].size);
        }

        $('#media_videocontents').append(contents);
        $('#media_videolist').append(html);

        $('html, body').animate({ scrollTop: $('body').offset().top - 100 }, 10);
        control_overlayOff();
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_listVideolist(): ' + textStatus);
    });
}

function findNode(id, node)
{
    if (node.id === id) {
        return node;
    }

    if (node.children) {
        for (var i = 0; i < node.children.length; i++) {
            const child = findNode(id, node.children[i]);

            if (child) {
                return child;
            }
        }
    }

    return false;
}

function getChildren(node, children)
{
    if (node.id.includes('@') == true) {
        children.push(node);
    } else {
        if (node.children != undefined) {
            for (var i = 0; i < node.children.length; i++) {
                getChildren(findNode(node.children[i].id, node), children);
            }
        }
    }
}

var title = '';

function getTitle(node, node_root)
{
    if (node.parentID != node_root.id) {
        var node_parent = findNode(node.parentID, node_root);

        if (node_parent.title != '_Serien') {
            if (title != '') {
                title = ' | ' + title;
            }

            title = node_parent.title + title;
        }

        getTitle(node_parent, node_root, title);
    }
}

function numberWithPoints(x)
{
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function getVideoLink(url, title, duration, resolution, size)
{
    var extension = (url.substring(url.lastIndexOf('.') + 1, url.length) || url).toLowerCase();

    var video_link = '<a href="javascript:control_playVideo(\'' + url + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
        + '<a href="javascript:control_seekVideo(\'' + url + '\');"><span class="ui-icon ui-icon-seek-next"></span></a>';

    if (extension == 'mp4' || extension == 'mov') {
        video_link += ' | <a href="' + url + '" target="_blank"><span class="ui-icon ui-icon-video"></span></a>';
    } else {
        video_link += ' | <a href="' + url + '" target="_blank"><span class="ui-icon ui-icon-disk"></span></a>';
    }

    var kbit_per_seconds = '-1';

    try {
        var a = duration.split(':');
        var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
        kbit_per_seconds = Math.round((+size) * 8 / 1024 / seconds);
    } catch(err) {
        console.log('getVideoLink(): title=' + title + ' / duration=' + duration);
    }

    video_link += '&nbsp;&nbsp;&nbsp;<a href="javascript:control_playVideo(\'' + url + '\');">'
        + '<b>' + title + '</b></a> (' + duration + ' / '  + kbit_per_seconds + ' kBit/s / '+ extension + ' / ' + resolution + ' Pixel / ' + numberWithPoints(size) + ' Bytes)</div>';

    return video_link;
}

function control_playVideo(url)
{
    $.ajax({
        type: 'GET',
        url : '/inc/ofa_ControlMedia.php?type=videolist&play&url=' + url
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_playVideo(): ' + textStatus);
    });
}

function control_seekVideo(url)
{
    $.ajax({
        type: 'GET',
        url : '/inc/ofa_ControlMedia.php?type=videolist&seek&url=' + url
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_seekVideo(): ' + textStatus);
    });
}

function control_video(todo)
{
    var url = '/inc/ofa_ControlMedia.php?type=videolist&';

    switch (todo) {
        case 'info':
        case 'pause':
        case 'stop':
            url += todo;
            break;

        default:
            return;
    }

    $.ajax({
        url:    url
    }).done(function(data)
    {
        /*
        if (todo == 'info') {
            album_showAlbumInfo(JSON.parse(data));
        } else {
            $("#album_info").html(data);
        }
        */
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ERROR control_video(): url=" + url + ' / testStatus=' + textStatus);
    });
}

function control_scrollToCaption(caption = '')
{
    if (caption == '') {
        $('html, body').animate({ scrollTop: $("#control_tab_videos").offset().top - 100 }, 1000);
    } else {
        $('html, body').animate({ scrollTop: $("#" + caption).offset().top - 100 }, 1000);
    }
}
