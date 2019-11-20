var dir = require('node-dir');
var fs = require('fs');
var mysql = require('mysql');
var Jimp = require('jimp');
var database    = require('../../libs/lib_database.js');
var config = require('../../configs/ofa_config.json');


const PFAD_SOURCE_MAIN = config.apps.pics_2_db.path_source_main;
const SUFFIX_Z0 = config.apps.pics_2_db.suffix_z0;

const USE = 'node pics_2_db.js pics_path pic_number pic_date pic_location pic_description pic_remark. E.g. node pics_2_db.js bilder 1801001 2019-10-21 München Olympiapark "Im Hintergrund das BMW-Hochhaus."' 

const args = process.argv;
// console.log(args);

var PFAD_SOURCE = PFAD_SOURCE_MAIN;

if (args[2] == null) {
    console.log('Path missing (can be "". Correct use: ' + USE);
    return;
} else {
    // pics_path
    PFAD_SOURCE += args[2] + '/';
}

console.log(PFAD_SOURCE);

var files_all = null;

try {
    files_all = dir.files(PFAD_SOURCE, { sync: true });
}
catch(err) {
    console.log(PFAD_SOURCE + ' nicht vorhanden.')
    // console.log(err.message);
    return;
}

if (files_all == null || files_all.length < 1) {
    console.log('Keine Dateien vorhanden.');
    return;
}

// console.log(files_all);
files_all.sort();

var files_jpg = files_all.filter(function(a)
{
    return a.toUpperCase().includes(SUFFIX_Z0.toUpperCase() + '.JPG');
});

// console.log(files_jpg);
console.log(files_jpg.length + ' jpg-Dateien im Verzeichnis "' + PFAD_SOURCE + '".');

if (files_jpg.length < 1) {
    console.log('Keine Dateien vorhanden.');
    return;
}

var pic_number = 0;

if (args[3] == null) {
    console.log('Picture start number missing. Correct use: ' + USE);
    return;
} else {
    // pic_number
    pic_number = parseInt(args[3], 10);

    if (pic_number <= 0) {
        console.log('Invalid picture start number (' + pic_number + '). Correct use: ' + USE);
        return;
    }
}

var pic_date = '';

if (args[4] == null) {
    console.log('Picture date (e.g. 2019-10-21) missing. Correct use: ' + USE);
    return;
} else {
    // pic_date
    pic_date = args[4];
}

var pic_description = '';

if (args[6] != null) {
    pic_description = args[6];
}

var pic_remark = '';

if (args[7] != null) {
    pic_remark = args[7];
}

db_connection = database.connect();

var pic_location_id = 0;
var pic_location = '';

if (args[5] == null) {
    console.log('Picture location missing. Correct use: ' + USE);
    finish;
} else {
    // pic_location
    pic_location = args[5];

    var ort_sql = 'SELECT id FROM ofa_ort WHERE ort="' + pic_location + '" ';

    db_connection.query(ort_sql, function (err, result) {
        if (err) {
            console.log('check_and_add_db(): Wrong SQL: ' + ort_sql);
            throw err;
            finish();
        }

        if (result == null || result.length == 0) {
            console.log('Invalid location (' + pic_location + '). Correct use: ' + USE);
            finish();
        } else {
            pic_location_id = result[0].id;

            check_and_add_db(0);
        }
    });
}

function check_and_add_db(file_no) {
    if (file_no >= files_jpg.length) {
        finish();
        return;
    }

    var file = files_jpg[file_no].replace(/^.*[\\\/]/, '');
    var p20 = false;

    if (file.startsWith('go')) {
        // go3_2017_30018945
        file = file.replace('_300', '_3');
    } else if (file.startsWith('p20')) {
        p20 = true;
    }

    var datei = file.replace(SUFFIX_Z0 + '.jpg', '').replace('5dii', '').replace('6dii', '')
        .replace('g12', '').replace('g7x', '').replace('ma','').replace('go3','').replace('gxx', '').replace('p20','').replace(/_/g, '');
    // console.log(datei);

    if (p20) {
        datei = datei.substr(2);
    }

    var bild_select_sql = 'SELECT nummer FROM ofa_bild WHERE datei="' + datei + '" ';

    db_connection.query(bild_select_sql, function (err, result) {
        if (err) {
            console.log('check_and_add_db(): Wrong SQL: ' + bild_select_sql);
            throw err;
            finish();
        }

        if (result == null || result.length == 0) {
            var sql_insert_bild = 'INSERT INTO ofa_bild '
                + '(nummer, datei, datum, ortid, beschreibung, bemerkung) '
                + 'VALUES ("' + pic_number + '", "' + datei + '", "' + pic_date + '", "' + pic_location_id + '", "' + pic_description + '", "' + pic_remark + '");';

            db_connection.query(sql_insert_bild, function (err, result) {
                if (err) {
                    console.log('check_and_add_db(): Wrong SQL: ' + sql_insert_bild);
                    throw err;
                    finish();
                }

                // console.log(bildnr + " | Result: insertId=" + result.insertId + ' / changedRows=' + result.changedRows);
                pic_number++;
                check_and_add_db(file_no + 1);
            });
        } else {
            console.log('Datei ' + files_jpg[file_no] + ' schon in Datenbank vorhanden.');
            finish();
        }
    });
}

function finish()
{
    console.log('finish(): DB connection closed');
    database.disconnect(db_connection);
    process.exit();
}
