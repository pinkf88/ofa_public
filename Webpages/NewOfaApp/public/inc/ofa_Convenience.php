<?php
function ofa_getRootPfadBilder()
{
//    if (strpos(php_uname(), 'DiskStation') === false)
//        return '/home/jr/ofa/Bilder/';

    return '/media/vol2/BilderBearbeitet/Bilder/';
}

function ofa_getBildPfad($nummer, $ticket, $jahr)
{
    $pfad = '/images/';
    $subpfad = '';
    $ret = array();

    if ($ticket == 1) {
        $subpfad = 'tickets/' . $jahr . '/' . $nummer;
    } else if ($nummer >= 1000000) {
        $subpfad = (floor($nummer / 1000) * 1000) . '/' . (floor($nummer / 100) * 100) . '/' . $nummer;
    } else {
        $subpfad = str_pad('' . (floor($nummer / 1000) * 1000), 6, '0', STR_PAD_LEFT) . '/' . str_pad('' . (floor($nummer / 100) * 100), 6, '0', STR_PAD_LEFT) . '/' . str_pad('' . $nummer, 6, '0', STR_PAD_LEFT);
    }

    $ret["filepfad"] = ofa_getRootPfadBilder() . $subpfad . '.gif';
    $ret['extension'] = 'gif';

    if (file_exists(ofa_getRootPfadBilder() . $subpfad . '.png') || file_exists(ofa_getRootPfadBilder() . $subpfad . '.gif')) {
        if (file_exists(ofa_getRootPfadBilder() . $subpfad . '.png')) {
            $ret["filepfad"] = ofa_getRootPfadBilder() . $subpfad . '.png';
            $ret['extension'] = 'png';
        }

        $ret["pfad"] = $pfad . $subpfad;
/*
        $imginfo = getimagesize(ofa_getRootPfadBilder() . $subpfad . '.jpg');
        $ret["breite"] = $imginfo[0];
        $ret["hoehe"] = $imginfo[1];
*/
    } else {
        $ret["pfad"] = ''; // ofa_getRootPfadBilder() . $subpfad . '.gif';
    }

    return $ret;
}

function ofa_replace($subject)
{
    $search  = array('"');
    $replace = array('');

    $subject = preg_replace( "/\r|\n/", "", $subject);

    return str_replace($search, $replace, $subject);
}

function date_mysql2german($datum)
{
		$d = explode("-", $datum);

        return sprintf("%02d.%02d.%04d", $d[2], $d[1], $d[0]);
}
?>
