// https://www.npmjs.com/package/ssh2-sftp-client

var fs              = require('fs-extra');
var dir             = require('node-dir');
var Jimp            = require('jimp');
let Client          = require('ssh2-sftp-client');
var colors          = require('colors');
var ArgumentParser  = require('argparse').ArgumentParser;
var database        = require('../libs/lib_database.js');
var config          = require('../../configs/ofa_config.json');


const PFAD_SOURCE_MAIN = config.apps.convert.path_source_main;
const SUFFIX_Z0 = config.apps.convert.suffix_z0;
const MAX_ANZAHL = 100000;

var parser = new ArgumentParser({
    add_help: true,
    description: 'upload'
});

parser.add_argument(
    '-d',
    {
        help: 'Source directory.',
        required: true
    }
);

parser.add_argument(
    '-l',
    {
        help: 'Location.',
        required: true
    }
);

parser.add_argument(
    '-n',
    {
        help: 'Number.',
        required: true
    }
);

parser.add_argument(
    '-b',
    {
        help: 'Beschreibung.'
    }
);

parser.add_argument(
    '-B',
    {
        help: 'Bemerkung.'
    }
);

parser.add_argument(
    '-a',
    {
        help: 'Anzahl.'
    }
);

var args = parser.parse_args();

var PFAD_SOURCE = PFAD_SOURCE_MAIN;

if (args.d != null) {
    PFAD_SOURCE += args.d + '/';
}

if (fs.existsSync(PFAD_SOURCE) == false) {
    console.log(colors.yellow('Pfad ' + PFAD_SOURCE + ' existiert nicht.'));
    return;
}

var PFAD_SOURCE_TEMP = PFAD_SOURCE_MAIN + 'temp/';

if (fs.existsSync(PFAD_SOURCE_TEMP == false)) {
    fs.mkdirSync(PFAD_SOURCE_TEMP);
}

var anzahl = MAX_ANZAHL;

if (args.a != null) {
    anzahl = parseInt(args.a);
}


var files_all = null;

try {
    files_all = dir.files(PFAD_SOURCE, { sync: true });
}
catch(err) {
    console.log(colors.yellow('Keine Dateien in ' + PFAD_SOURCE + ' vorhanden.'));
    console.log(err.message);
    return;
}

if (files_all == null || files_all.length < 1) {
    console.log(colors.yellow('Keine Dateien in ' + PFAD_SOURCE + ' vorhanden.'));
    return;
}

// console.log(files_all);
files_all.sort();

var files_jpg = files_all.filter(function(a)
{
    return !a.toUpperCase().includes(SUFFIX_Z0.toUpperCase() + '.JPG') && a.toUpperCase().includes('.JPG') && !a.includes('zz_black.jpg');
});

// console.log(files_jpg);
console.log(files_jpg.length + ' jpg-Dateien im Verzeichnis "' + PFAD_SOURCE + '".');

if (files_jpg.length < 1) {
    console.log(colors.yellow('Keine konvertierbaren Dateien vorhanden.'));
    return;
}

if (anzahl == MAX_ANZAHL) {
    anzahl = files_jpg.length;
}

var bild_json = {
    nummer: parseInt(args.n),
};

var bild_nummer = parseInt(args.n);

var bild_beschreibung = '';

if (args.b != null) {
    bild_beschreibung = args.b;
}

var bild_bemerkung = '';

if (args.B != null) {
    bild_bemerkung = args.B;
}

var bild_array = [];
var db_connection = database.connect();
let sftp_client = new Client();
var pic_location = args.l;

var ort_sql = 'SELECT id FROM ofa_ort WHERE ort="' + pic_location + '" ';

db_connection.query(ort_sql, function (err, result) {
    if (err) {
        console.log('upload.js: Wrong SQL: ' + ort_sql);
        // throw err;
        shutdown();
    }

    if (result == null || result.length == 0) {
        console.log(colors.yellow('Unknown location: ' + pic_location));
        shutdown();
    } else {
        bild_json.ort = pic_location;
        bild_json.ortid = result[0].id;

        check_nummer();
    }
});

function check_nummer()
{
    var bild_sql = 'SELECT id FROM ofa_bild WHERE nummer>=' + bild_nummer + ' AND nummer<=' + (bild_nummer + anzahl - 1) ;

    db_connection.query(bild_sql, function (err, result) {
        if (err) {
            console.log('upload.js: Wrong SQL: ' + bild_sql);
            // throw err;
            shutdown();
        }

        if (result == null || result.length == 0) {
            connect_remote();
        } else {
            console.log(colors.yellow('Problem: Existierende Bildnummer größer oder gleich ' + bild_nummer));
            shutdown();
        }
    });
}

function connect_remote()
{
    sftp_client.connect({
        host:       config.piwebserver.host,
        port:       22,
        username:   config.piwebserver.user,
        password:   config.piwebserver.password
    }).then(() => {
        sftp_client.rmdir(config.piwebserver.root_dir + 'pics/temp/', true).then((data) => {
            console.log(colors.yellow(data));

            sftp_client.mkdir(config.piwebserver.root_dir + 'pics/temp/', true).then((data) => {
                console.log(colors.yellow(data));
        
                fs.emptyDir('/tmp/some/dir').then(() => {
                    console.log('upload.js | Delete all files in ' + PFAD_SOURCE_TEMP + '.');
                    check_db(0);
                }).catch(err => {
                    console.error(err);
                    shutdown();
                });
            }).catch(err => {
                console.error(err);
                shutdown();
            });
        }).catch(err => {
            console.error(err);
            shutdown();
        });
    }).then(data => {
        console.log(colors.green('upload.js | sftp_client.connect(): Get data.'));

        if (data != undefined) {
            console.log(data);
        }
    }).catch(err => {
        // throw err;
        console.log(colors.yellow('upload.js | sftp_client.connect(): ERROR: ' + err.message));
        shutdown();
    });
}

function check_db(file_no)
{
    if (file_no >= files_jpg.length || file_no >= anzahl) {
        finish();
        return;
    }

    var file = files_jpg[file_no].replace(/^.*[\\\/]/, '');

    var file_temp = file;
    var p20 = false;

    if (file.startsWith('go')) {
        // go3_2017_30018945
        file_temp = file.replace('_300', '_3');
    } else  if (file.startsWith('p20')) {
        p20 = true;
    }

    var bild_nr = file_temp.replace('.jpg', '').replace('5dii', '').replace('6dii', '')
        .replace('g12', '').replace('g7x', '').replace('ma','').replace('go3','').replace('gxx', '').replace('p20','').replace(/_/g, '');
    
    console.log('upload.js | check_db(): ' + file_no + ' / ' + bild_nr);

    if (p20) {
        bild_nr = bild_nr.substr(2);
    }

    var bild_sql = 'SELECT Aufnahmedatum, Laenge, Breite FROM ofa_bilddaten WHERE BildNr="' + bild_nr + '" ';
    var last_datum = '2020-01-01';

    db_connection.query(bild_sql, function (err, result) {
        if (err) {
            // throw err;
            console.log(colors.yellow('upload.js | check_db(): ERROR: ' + err.message));
            console.log(bild_sql);
            shutdown();
        }

        var bild = {
            datei:          bild_nr,
            beschreibung:   bild_beschreibung,
            bemerkung:      bild_bemerkung,
            datum:          last_datum,
        };

        if (result == null || result.length == 0) {
            console.log(colors.yellow('Datei ' + file + ' NICHT in Datenbank vorhanden.'));
        } else {
            bild.datum = result[0].Aufnahmedatum.substr(0, 10);
            last_datum = bild.datum;
            bild.laenge = result[0].Laenge;
            bild.breite = result[0].Breite;
        }

        bild_array.push(bild);

        scale_bild(file_no, files_jpg[file_no], bild_nr);
    });
}


function scale_bild(file_no, file, nummer)
{
    console.log('upload.js | scale_bild(): ' + file_no + ' / ' + file);

    Jimp.read(file, function (err, bild) {
        if (err) {
            // throw err;
            console.log(colors.yellow('upload.js | scale_bild(): Jimp.read ERROR: ' + err.message));
            shutdown();
        }

        bild.scaleToFit(200, 200, Jimp.RESIZE_BICUBIC)
            .write(PFAD_SOURCE_TEMP + nummer + '.png', function (err) {
                if (err) {
                    // throw err;
                    console.log(colors.yellow('upload.js | scale_bild(): Jimp.read ERROR: ' + err.message));
                    shutdown();
                }
        
                fs.copyFileSync(file, PFAD_SOURCE_TEMP + nummer + '.jpg');
                
                upload_bild(file_no, nummer);
            });
    });
}

function upload_bild(file_no, nummer)
{
    try {
        sftp_client.put(PFAD_SOURCE_TEMP + nummer + '.png', config.piwebserver.root_dir + 'pics/temp/' + nummer + '.png').then((data) => {
            console.log(colors.yellow(data));

            sftp_client.put(PFAD_SOURCE_TEMP + nummer + '.jpg', config.piwebserver.root_dir + 'pics/temp/' + nummer + '.jpg').then((data) => {
                console.log(colors.yellow(data));

                file_no++;
                check_db(file_no);
            });
        });
    } catch(err) {
        // throw err;
        console.log(colors.yellow('upload.js | upload_bild(): ' + err.message));
        shutdown();
    }
}

function finish()
{
    // console.log(bild_array);

    if (bild_array.length > 0) {
        bild_json.bilder = bild_array;

        fs.writeFile(PFAD_SOURCE_TEMP + 'bilddaten.json', JSON.stringify(bild_json, null, 4), (err) => {
            if (err) {
                // throw err;
                console.log(colors.yellow('upload.js | finish(): writeFile ERROR: ' + err.message));
                shutdown();
            }

            sftp_client.put(PFAD_SOURCE_TEMP + 'bilddaten.json', config.piwebserver.root_dir + 'pics/temp/bilddaten.json').then((data) => {
                console.log(colors.yellow(data));
                shutdown();
            });
        });
    }
}

function shutdown()
{
    console.log('shutdown(): DB connection closed');
    database.disconnect(db_connection);
    sftp_client.end();
    process.exit();
}
