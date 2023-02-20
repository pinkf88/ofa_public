<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>OneForAll - Bilder</title>
<link href="/css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/jquery.jscrollpane.css" media="screen" rel="stylesheet" type="text/css">
<link href="/js/fancybox/jquery.fancybox.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/bootstrap-theme.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/style.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/ofa.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="/js/ofa_bild_import.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="/js/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.js"></script>
</head>
<body class="body_bild_import">
<div>
<ul id="bildimportgrid">
</ul>
</div>
<div style="clear: both"></div>
<div style="padding-top: 15px;">
<p><a href="javascript:bild_hideBildImportGrid();">Schließen</a> | <a href="javascript:bild_saveBildImportGrid();">Speichern und schließen</a></p>
</div>

<div id="beschreibungdialog" title="Beschreibung / Bemerkung">
<form>
<fieldset>
<p class="dialoglabel" ><label for="beschreibung">Beschreibung</label></p>
<input id="beschreibung" class="beschreibung" type="text" class="text ui-widget-content ui-corner-all" name="beschreibung"></input>
<p class="dialoglabel" ><label for="beschreibung">Bemerkung</label></p>
<textarea id="bemerkung" class="bemerkung" class="text ui-widget-content ui-corner-all" name="bemerkung"></textarea>
</fieldset>
</form>
</div>
</body>
</html>
