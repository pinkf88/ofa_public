var dir         = require('node-dir');
var fs          = require('fs');
var Jimp        = require('jimp');
var piexif      = require('piexifjs');
var tools       = require('../libs/lib_tools.js');
var database    = require('../libs/lib_database.js');
var config      = require('../configs/ofa_config.json');


const PFAD_SOURCE_MAIN = config.apps.convert.path_source_main;
const PFAD_OFA = config.apps.convert.path_ofa;
const PFAD_OFA_BU = config.apps.convert.path_ofa_backup;
const PFAD_HD = config.apps.convert.path_hd;
const SUFFIX_Z0 = config.apps.convert.suffix_z0;

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

db_connection = database.connect();

function check_db(file)
{
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

    db_connection.query(bild_sql, function (err, result) {
        if (err) {
            // throw err;
            console.log('convert.js | check_db(): ERROR: ' + err.message);
            console.log(bild_sql);
            process.exit();
        }

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
                setTimeout( function () {
                    convert_bild(0);
                }, 10);
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
                .write(PFAD_OFA + tools.get_subpfad(nummer) + nummer + '.jpg', function (err) {
                    if (err) throw err;

                    fs.copyFileSync(PFAD_OFA + tools.get_subpfad(nummer) + nummer + '.jpg', PFAD_OFA_BU + tools.get_subpfad(nummer) + nummer + '.jpg');
                    scale_bild(bild_no, file, nummer, SCALE_TYPE_HD);
                });
        }

        if (scale_type == SCALE_TYPE_HD) {
            bild.scaleToFit(1920, 1080, Jimp.RESIZE_BICUBIC)
                .quality(95)
                .write(PFAD_HD + nummer + '_hd.jpg', function (err) {
                    if (err) throw err;

                    readDatabase(PFAD_HD + nummer + '_hd.jpg', bild_no, file, nummer);
                });
        }

        if (scale_type == SCALE_TYPE_200) {
            bild.scaleToFit(200, 200, Jimp.RESIZE_BICUBIC)
                .write(PFAD_OFA + tools.get_subpfad(nummer) + nummer + '.png', function (err) {
                    if (err) throw err;

                    fs.copyFileSync(PFAD_OFA + tools.get_subpfad(nummer) + nummer + '.png', PFAD_OFA_BU + tools.get_subpfad(nummer) + nummer + '.png');
                    console.log(PFAD_OFA + tools.get_subpfad(nummer) + nummer + SUFFIX_Z0 + '.jpg');
                    fs.copyFileSync(PFAD_SOURCE + file, PFAD_OFA + tools.get_subpfad(nummer) + nummer + SUFFIX_Z0 + '.jpg');
                    fs.copyFileSync(PFAD_SOURCE + file, PFAD_OFA_BU + tools.get_subpfad(nummer) + nummer + SUFFIX_Z0 + '.jpg');
                    fs.unlinkSync(PFAD_SOURCE + file);

                    if (bild_no < bild_array.length - 1) {
                        setTimeout( function() {
                            convert_bild(bild_no + 1);
                        }, 10);
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

    if (fs.existsSync(PFAD_OFA + tools.get_subpfad1000(nummer)) == false) {
        fs.mkdirSync(PFAD_OFA + tools.get_subpfad1000(nummer));
    }

    if (fs.existsSync(PFAD_OFA + tools.get_subpfad(nummer)) == false) {
        fs.mkdirSync(PFAD_OFA + tools.get_subpfad(nummer));
    }

    if (fs.existsSync(PFAD_OFA_BU + tools.get_subpfad1000(nummer)) == false) {
        fs.mkdirSync(PFAD_OFA_BU + tools.get_subpfad1000(nummer));
    }

    if (fs.existsSync(PFAD_OFA_BU + tools.get_subpfad(nummer)) == false) {
        fs.mkdirSync(PFAD_OFA_BU + tools.get_subpfad(nummer));
    }

    var scale_type = SCALE_TYPE_1000;

    if (fs.existsSync(PFAD_SOURCE + file.replace(SUFFIX_Z0, ''))) {
        // console.log('>>>>> ' + PFAD_OFA + tools.get_subpfad(nummer) + nummer + '.jpg');
        fs.copyFileSync(PFAD_SOURCE + file.replace(SUFFIX_Z0, ''), PFAD_OFA + tools.get_subpfad(nummer) + nummer + '.jpg');
        fs.copyFileSync(PFAD_OFA + tools.get_subpfad(nummer) + nummer + '.jpg', PFAD_OFA_BU + tools.get_subpfad(nummer) + nummer + '.jpg');
        fs.unlinkSync(PFAD_SOURCE + file.replace(SUFFIX_Z0, ''));

        scale_type = SCALE_TYPE_HD;
    }

    setTimeout(function () {
        scale_bild(bild_no, file, nummer, scale_type);
    }, 1);
}

function readDatabase(path, bild_no, file, nummer)
{
    var bild_sql = 'SELECT b.datum, bd.Aufnahmedatum FROM ofa_bild b LEFT JOIN ofa_bilddaten bd ON (bd.BildNr=b.datei) WHERE b.nummer="' + nummer + '" ';

    db_connection.query(bild_sql, function (err, result) {
        if (err) {
            // throw err;
            console.log(colors.yellow('update_pics | readDatabase(): ERROR: ' + err.message));
            console.log(bild_sql);
            finish();
        }

        if (result == null || result.length == 0) {
            console.log(colors.yellow('update_pics | readDatabase(): Datei ' + file_list[file_no] + ' NICHT in Datenbank vorhanden.'));
            finish();
        } else {
            var date_original = result[0].datum + ' 12:00:00';

            if (result[0].Aufnahmedatum != null && result[0].Aufnahmedatum != '') {
                date_original = result[0].Aufnahmedatum;
            }

            updateExif(path, bild_no, file, nummer, date_original);
        }
    });
}

function updateExif(path, bild_no, file, nummer, date_original)
{
    // console.log(colors.green('update_pics | updateExif(): path=' + file_list[file_no] + ', date_original=' + date_original));

    try {
        var jpeg = fs.readFileSync(path);
        var data = jpeg.toString("binary");
        var exif = piexif.load(data);

        // console.log(exif.Exif);

        var update1 = false;

        try {
        
            if (exif.Exif[piexif.ExifIFD.DateTimeOriginal] != date_original.replace(/-/g, ":")) {
                update1 = true;            
            }
        } catch (e) {
            update1 = true;
        }
        
        // console.log (exif.Exif[piexif.ExifIFD.DateTimeOriginal], date_original.replace(/-/g, ":"), update1);
        
        if (update1 == true) {
            exif.Exif[piexif.ExifIFD.DateTimeOriginal] = date_original.replace(/-/g, ":");  // "2010:10:10";
        }
        
        var update2 = false;
        
        try {
            if (exif['0th'][piexif.ImageIFD.DateTime] != date_original.replace(/-/g, ":")) {
                update2 = true;            
            }
        } catch (e) {
            update2 = true;
        }
        
        // console.log(exif['0th'][piexif.ImageIFD.DateTime], date_original.replace(/-/g, ":"), update2);
        
        if (update2 == true) {
            exif['0th'][piexif.ImageIFD.DateTime] = date_original.replace(/-/g, ":");         // "2010:10:10";
        }
        
        // console.log(exif);

        if (update1 == true || update2 == true) {
            fs.writeFileSync(path, Buffer.from(piexif.insert(piexif.dump(exif), data), 'binary'));
        }
    } catch(e) {
        console.log(e);
        console.log(colors.yellow('update_pics | updateExif(): ' + e.message));
        console.log(colors.yellow('update_pics | updateExif(): ' + path));
    }

    setTimeout(function() {
        scale_bild(bild_no, file, nummer, SCALE_TYPE_200);
    }, 1);
}

function finish()
{
    console.log('finish(): DB connection closed');
    database.disconnect(db_connection);
    process.exit();
}

for (var i = 0; i < files_jpg.length; i++) {
    var file = files_jpg[i].replace(/^.*[\\\/]/, '');

    check_db(file);
}
