var dir = require('node-dir');
var fs = require('fs');
var mysql = require('mysql');
var Jimp = require('jimp');
var config = require('./ofa_config.json');


const PFAD_SOURCE_MAIN = config.convert.path_source_main;
const PFAD_OFA = config.convert.path_ofa;
const PFAD_OFA_BU = config.convert.path_ofa_backup;
const PFAD_HD = config.convert.path_hd;
const SUFFIX_Z0 = config.convert.suffix_z0;

const args = process.argv;
// console.log(args);

var PFAD_SOURCE = PFAD_SOURCE_MAIN;

if (args[2] != null) {
    PFAD_SOURCE += args[2] + '/';
}

if (fs.existsSync(PFAD_HD) == false) {
    fs.mkdirSync(PFAD_HD);
}

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
    console.log('Keine konvertierbaren Dateien vorhanden.');
    return;
}

var zeler = 0;
var files_count = files_jpg.length;

var bild_array = [];

var con = mysql.createConnection({
    host: config.database.host,
    user: config.database.user,
    password: config.database.password,
    database: config.database.name
});

function get_subpfad(nummer, jahr=0)
{
    if (nummer > 10000000)
    {
        // Ticket
        return 'tickets/' + jahr + '/';
    }

    var s1 = "" + (Math.floor(nummer / 1000) * 1000);
    var s2 = "" + (Math.floor(nummer / 100) * 100);
    
    return s1.padStart(6, '0') + '/' + s2.padStart(6, '0') + '/';
}

function get_subpfad1000(nummer, jahr=0)
{
    if (nummer > 10000000)
    {
        // Ticket
        return 'tickets/' + jahr + '/';
    }

    var s1 = "" + (Math.floor(nummer / 1000) * 1000);
    
    return s1.padStart(6, '0') + '/';
}

function check_db(file) {
    var file_temp = file;
    var p20 = false;

    if (file.startsWith('go')) {
        // go3_2017_30018945
        file_temp = file.replace('_300', '_3');
    } else  if (file.startsWith('p20')) {
        p20 = true;
    }

    var datei = file_temp.replace(SUFFIX_Z0 + '.jpg', '').replace('5dii', '').replace('6dii', '')
        .replace('g12', '').replace('g7x', '').replace('ma','').replace('go3','').replace('gxx', '').replace('p20','').replace(/_/g, '');
    // console.log(datei);
    
    var bild_sql = '';
    var bnummer = 0;

    if (p20) {
        datei = datei.substr(2);
    }

    if (datei.startsWith('dia'))
    {
        bnummer = parseInt(datei.replace('dia', ''));
        bild_sql = 'SELECT nummer, datei FROM ofa_bild WHERE nummer="' + bnummer + '" ';
    }
    else if (datei.startsWith('scan'))
    {
        bnummer = parseInt(datei.replace('scan', ''));
        bild_sql = 'SELECT nummer, datei FROM ofa_bild WHERE datei="' + bnummer + '" ';
    }
    else
    {
        bild_sql = 'SELECT nummer FROM ofa_bild WHERE datei="' + datei + '" ';
    }

    con.query(bild_sql, function (err, result) {
        if (err) throw err;

        if (result == null || result.length == 0) {
            console.log('Datei ' + file + ' NICHT in Datenbank vorhanden.');
        } else {
            var nummer = '' + result[0].nummer;
            nummer = nummer.padStart(6, '0');
            
            // Datensatz existiert bereits
            var bild = {
                file: file,
                nummer: nummer
            };

            bild_array.push(bild);
        }

        zeler++;

        // console.log(zeler + ' / '+ files_count);

        if (zeler >= files_count) {
            console.log("In Datenbank gefundene Bilder: " + bild_array.length);

            if (bild_array.length > 0) {
                convert_bild(0);
            } else {
                finish();
            }
        }
    });
}

const SCALE_TYPE_1000 = 1;
const SCALE_TYPE_HD = 2;
const SCALE_TYPE_200 = 3;

function scale_bild(bild_no, file, nummer, scale_type)
{
    // console.log('scale_bild(): ' + bild_no + ' / ' + file + ' / ' + nummer + ' / ' + scale_type);

    Jimp.read(PFAD_SOURCE + file, function (err, bild) {
        if (err) throw err;

        if (scale_type == SCALE_TYPE_1000) {
            bild.scaleToFit(1000, 1000, Jimp.RESIZE_BICUBIC)
                .quality(95)
                .write(PFAD_OFA + get_subpfad(nummer) + nummer + '.jpg', function (err) {
                    if (err) throw err;

                    fs.copyFileSync(PFAD_OFA + get_subpfad(nummer) + nummer + '.jpg', PFAD_OFA_BU + get_subpfad(nummer) + nummer + '.jpg');
                    scale_bild(bild_no, file, nummer, SCALE_TYPE_HD);
                });
        }

        if (scale_type == SCALE_TYPE_HD) {
            bild.scaleToFit(1920, 1080, Jimp.RESIZE_BICUBIC)
                .quality(95)
                .write(PFAD_HD + nummer + '_hd.jpg', function (err) {
                    if (err) throw err;

                    scale_bild(bild_no, file, nummer, SCALE_TYPE_200);
                });
        }

        if (scale_type == SCALE_TYPE_200) {
            bild.scaleToFit(200, 200, Jimp.RESIZE_BICUBIC)
                .write(PFAD_OFA + get_subpfad(nummer) + nummer + '.png', function (err) {
                    if (err) throw err;

                    fs.copyFileSync(PFAD_OFA + get_subpfad(nummer) + nummer + '.png', PFAD_OFA_BU + get_subpfad(nummer) + nummer + '.png');
                    console.log(PFAD_OFA + get_subpfad(nummer) + nummer + SUFFIX_Z0 + '.jpg');
                    fs.copyFileSync(PFAD_SOURCE + file, PFAD_OFA + get_subpfad(nummer) + nummer + SUFFIX_Z0 + '.jpg');
                    fs.copyFileSync(PFAD_SOURCE + file, PFAD_OFA_BU + get_subpfad(nummer) + nummer + SUFFIX_Z0 + '.jpg');
                    fs.unlinkSync(PFAD_SOURCE + file);

                    if (bild_no < bild_array.length - 1) {
                        convert_bild(bild_no + 1);
                    } else {
                        finish();
                    }
                });
        }
    });
}

function convert_bild(bild_no)
{
    var file = bild_array[bild_no].file;
    var nummer = bild_array[bild_no].nummer;
    console.log('convert_bild(): ' + bild_no + ' / ' + file + ' / ' + nummer);

    if (fs.existsSync(PFAD_OFA + get_subpfad1000(nummer)) == false) {
        fs.mkdirSync(PFAD_OFA + get_subpfad1000(nummer));
    }

    if (fs.existsSync(PFAD_OFA + get_subpfad(nummer)) == false) {
        fs.mkdirSync(PFAD_OFA + get_subpfad(nummer));
    }

    if (fs.existsSync(PFAD_OFA_BU + get_subpfad1000(nummer)) == false) {
        fs.mkdirSync(PFAD_OFA_BU + get_subpfad1000(nummer));
    }

    if (fs.existsSync(PFAD_OFA_BU + get_subpfad(nummer)) == false) {
        fs.mkdirSync(PFAD_OFA_BU + get_subpfad(nummer));
    }

    var scale_type = SCALE_TYPE_1000;

    if (fs.existsSync(PFAD_SOURCE + file.replace(SUFFIX_Z0, ''))) {
        console.log('>>>>> ' + PFAD_OFA + get_subpfad(nummer) + nummer + '.jpg');
        fs.copyFileSync(PFAD_SOURCE + file.replace(SUFFIX_Z0, ''), PFAD_OFA + get_subpfad(nummer) + nummer + '.jpg');
        fs.copyFileSync(PFAD_OFA + get_subpfad(nummer) + nummer + '.jpg', PFAD_OFA_BU + get_subpfad(nummer) + nummer + '.jpg');
        fs.unlinkSync(PFAD_SOURCE + file.replace(SUFFIX_Z0, ''));

        scale_type = SCALE_TYPE_HD;
    }

    scale_bild(bild_no, file, nummer, scale_type);
}

function finish()
{
    console.log('finish(): DB connection closed');
    con.destroy();
}

for (var i = 0; i < files_jpg.length; i++) {
    var file = files_jpg[i].replace(/^.*[\\\/]/, '');

    check_db(file);
}
