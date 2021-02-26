
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
    return '<a href="javascript:control_playVideo(\'' + url + '\');"><span class="ui-icon ui-icon-play"></span></a> | '
        + '<a href="javascript:control_seekVideo(\'' + url + '\');"><span class="ui-icon ui-icon-seek-next"></span></a>'
        + '&nbsp;&nbsp;&nbsp;<a href="javascript:control_playVideo(\'' + url + '\');">'
        + '<b>' + title + '</b></a> (' + duration + ' / ' + resolution + ' Pixel / ' + numberWithPoints(size) + ' Bytes)</div>';
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
        url : url
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
