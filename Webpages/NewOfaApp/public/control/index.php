<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>CONTROL - OneForAll</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="/css/bootstrap.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/bootstrap-theme.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/jquery-ui.theme.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/js/fancybox/jquery.fancybox.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/jquery.dropdown.css" rel="stylesheet" type="text/css">
<link href="/css/ofa.css" media="screen" rel="stylesheet" type="text/css">
<link href="/img/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/ofa.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/js.cookie.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.js"></script>                
<script type="text/javascript" src="/js/jquery.dropdown.js"></script>
<script type="text/javascript" src="/js/jquery.flot.js"></script>
<script type="text/javascript" src="/js/ofa_album.js"></script>
<script type="text/javascript" src="/js/ofa_control.js"></script>
<script type="text/javascript" src="/js/ofa_control_music.js"></script>
<script type="text/javascript" src="/js/ofa_control_picture.js"></script>
<script type="text/javascript" src="/js/ofa_control_video.js"></script>
<script type="text/javascript" src="/js/ofa_control_home.js"></script>
<script type="text/javascript" src="/js/ofa_control_analytics.js"></script>
<script type="text/javascript" src="/js/ofa_control_admin.js"></script>
<script type="text/javascript" src="/js/ofa_serie.js"></script>
</head>
<body class="control">
<div id="control_fancybox-overlay" class="fancybox-overlay fancybox-overlay-fixed" style="display: block; width: auto; height: auto;"></div>
<nav class="navbar navbar-inverse navbar-fixed-top navbar-control" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/control/">CONTROL</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="javascript:control_tab('home')">Home</a></li>
                <li><a href="javascript:control_tab('albums')">Alben</a></li>
                <li><a href="javascript:control_tab('tracks')">Tracks</a></li>
                <li><a href="javascript:control_tab('playlists')">Playlisten</a></li>
                <li><a href="javascript:control_tab('pictures')">Bilder</a></li>
                <li><a href="javascript:control_tab('videos')">Videos</a></li>
                <li><a href="javascript:control_tab('analytics')">Analytics</a></li>
                <li><a href="javascript:control_tab('admin')">Admin</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<div class="control_left">
    <dl class="control_left_menu">
        <dt class="control_left_menu"><a href="#" class="dimgray menu_closed">Admin</a></dt>
        <dd class="control_left_menu">
            <select name="control_room" id="control_room" class="lang">
                <option value="1" selected="selected">Wohnzimmmer</option>
                <option value="2">Elkes Zimmer</option>
                <option value="3">Schlafzimmer</option>
            </select>
            <p class="control_left_menu_admin"><a href="javascript:control_admin('restart_kodi');">Restart Kodi</a></p>
            <p class="control_left_menu_admin"><a href="javascript:control_admin('restart_echostudio');">Restart Echo Studio</a></p>
            <p class="control_left_menu_admin"><a href="javascript:control_admin('restart_webmedia');">Restart Webmedia</a></p>
            <p class="control_left_menu_admin"><a href="javascript:control_admin('restart_webhome');">Restart Webhome</a></p>
            <p class="control_left_menu_admin"><a href="javascript:control_admin('restart_iobroker');">Restart ioBroker</a></p>
        </dd>
        <dt class="control_left_menu"><a id="menu_control_music_link" href="#" class="dimgray menu_closed">Musik</a></dt>
        <dd id="menu_control_music_content" class="control_left_menu">
            <p>
                <a href="javascript:album_controlTrack('info');"><span class="ui-icon ui-icon-info"></span></a> |
                <a href="javascript:album_controlTrack('previous');"><span class="ui-icon ui-icon-seek-prev"></span></a> |
                <a href="javascript:album_controlTrack('stop');"><span class="ui-icon ui-icon-stop"></span></a> |
                <a href="javascript:album_controlTrack('pause');"><span class="ui-icon ui-icon-pause"></span></a> |
                <a href="javascript:album_controlTrack('next');"><span class="ui-icon ui-icon-seek-next"></span></a> |
                <a href="javascript:album_controlTrack('vol_down');"><span class="ui-icon ui-icon-circle-minus"></span></a> |
                <a href="javascript:album_controlTrack('vol_up');"><span class="ui-icon ui-icon-circle-plus"></span></a> |
                <a href="javascript:album_controlTrack('vol_mute');"><span class="ui-icon ui-icon-circle-close"></span></a>
            </p>
            <div class="control_album">
                <div id="control_cover" class="control_cover"></div>
                <div id="control_album_info" class="control_album_info"><span id="album_info"></span></div>
            </div>
        </dd>
        <dt class="control_left_menu"><a id="menu_control_pictures_link" href="#" class="dimgray menu_closed">Bilder</a></dt>
        <dd id="menu_control_pictures_content" class="control_left_menu">
            <p>
                <a href="javascript:serie_controlBild('info');"><span class="ui-icon ui-icon-info"></span></a> |
                <a href="javascript:serie_controlBild('previous');"><span class="ui-icon ui-icon-seek-prev"></span></a> |
                <a href="javascript:serie_controlBild('stop');"><span class="ui-icon ui-icon-stop"></span></a> |
                <a href="javascript:serie_controlBild('pause');"><span class="ui-icon ui-icon-pause"></span></a> |
                <a href="javascript:serie_controlBild('next');"><span class="ui-icon ui-icon-seek-next"></span></a> | <b>Bilder</b>
            </p>
            <div class="control_series">
                <div id="control_picture" class="control_picture"></div>
                <div id="control_picture_info" class="control_pictrue_info"><span id="serie_info"></span></div>
            </div>
        </dd>
        <dt class="control_left_menu"><a id="menu_control_videos_link" href="#" class="dimgray menu_closed">Videos</a></dt>
        <dd id="menu_control_videos_content" class="control_left_menu">
            <p>
                <a href="javascript:control_video('info');"><span class="ui-icon ui-icon-info"></span></a> |
                <a href="javascript:control_video('stop');"><span class="ui-icon ui-icon-stop"></span></a> |
                <a href="javascript:control_video('pause');"><span class="ui-icon ui-icon-pause"></span></a> | <b>Video</b>
            </p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('control_tab_videos')"><b>Video Kategorien</b></a></p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('Konzerte')">Konzerte</a></p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('Spielfilme')">Spielfilme</a></p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('Serien')">Serien</a></p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('Dokumentationen')">Dokumentationen</a></p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('Comedy')">Comedy</a></p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('Sport')">Sport</a></p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('JR-Filme')">JR-Filme</a></p>
            <p class="control_left_video"><a href="javascript:control_scrollToCaption('Temp')">Temp</a></p>
        </dd>
        <dt class="control_left_menu"><a href="#" class="dimgray menu_closed">Infos</a></dt>
        <dd class="control_left_menu">
            <div class="control_left_info">
                <p class="control_left_info"><span class="ui-icon ui-icon-info ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Info Update</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-seek-prev ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Previous</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-stop ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Stop</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-pause ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Pause</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-seek-next ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Next</p>
            </div>
            <div class="control_left_info">
                <p class="control_left_info"><span class="ui-icon ui-icon-play ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Play</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-arrowthick-1-e ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Play Random</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-circle-triangle-e ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Play Long</p>
            </div>
            <div class="control_left_info">
                <p class="control_left_info"><span class="ui-icon ui-icon-play ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Play</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-arrowthick-1-e ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Play Random</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-seek-end ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Start Playing</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-plusthick ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Add</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-flag ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Mark</p>
                <p class="control_left_info"><span class="ui-icon ui-icon-star ui-icon-gray"></span>&nbsp;&nbsp;&nbsp;Best Of</p>
            </div>
            <div class="control_left_info">
            </div>
        </dd>
    </dl>
    <div id="control_left_feedback" class="control_left_feedback">
        <span id="feedbackinformation"></span>
    </div>
</div>
<?php
include_once "../inc/ofa_DbConsts.php";
include_once "../inc/ofa_Database.php";
?>
<div class="control_lists">
<?php
include_once "./control_home.php";
include_once "./control_albums.php";
include_once "./control_tracks.php";
include_once "./control_playlists.php";
include_once "./control_pictures.php";
include_once "./control_videos.php";
include_once "./control_analytics.php";
include_once "./control_admin.php";
?>
</div>
</body>
</html>
