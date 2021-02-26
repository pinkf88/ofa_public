// npm install exif-parser
// npm install node-dir
// npm install mysql
// ALTER TABLE `ofa_bilddaten`  ADD `objektiv` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_german1_ci NULL  AFTER `Kamera`;
// Maximalblende löschen
// osmids Default-Value null

var dir             = require('node-dir');
var fs              = require('fs');
var exif_parser     = require('exif-parser');
var ArgumentParser  = require('argparse').ArgumentParser;
var database        = require('../libs/lib_database.js');


const db_tbl_bilddaten = 'ofa_bilddaten';


var parser = new ArgumentParser({
    add_help: true,
    description: 'serie-2-serie'
});

parser.add_argument(
    '--cam',
    {
        help: 'Kamera.',
        required: true
    }
);

parser.add_argument(
    '--year',
    {
        help: 'Jahr',
        required: false
    }
);

parser.add_argument(
    '--block',
    {
        help: 'Blocknummer: 1, 2,...',
        required: false
    }
);

parser.add_argument(
    '--date',
    {
        help: 'Datum (Verzeichnis).',
        required: true
    }
);

parser.add_argument(
    '--dir',
    {
        help: 'Basisverzeichnis.',
        required: false
    }
);

var args = parser.parse_args();
console.log(args);

var kameratyp = args.cam;

if (kameratyp != '5dii' && kameratyp != '6dii' && kameratyp != 'g7x' && kameratyp != 'g12' && kameratyp != 'ma') {
    console.log("Unbekannter Kameratyp");
    console.log("node readexif.js --cam [5dii|6dii|g7x|g12|ma] --year jahr --block [1|2|3] --date datum");
    return;
}

var aufnahmejahr = 0;

if (args.year != undefined) {
    aufnahmejahr = args.year;
}

var blocknr = 1;

if (args.block != undefined) {
    blocknr = args.block;
}

var datum = args.date;

var picsdirectory = 'Y:/BilderOriginale/';

if (args.dir != undefined) {
    picsdirectory = args.dir;
}

var bildnrpraefix = '' + aufnahmejahr;
var kameramodell = '';

if (kameratyp == '5dii') {
    bildnrpraefix += '0' + blocknr;
    kameramodell = 'Canon EOS 5D Mark II';
    picsdirectory += 'EOS5DII/';
} else if (kameratyp == 'g7x') {
    bildnrpraefix += '2' + blocknr;
    kameramodell = 'Canon PowerShot G7 X';
    picsdirectory += 'G7X/';
} else if (kameratyp == 'g12') {
    bildnrpraefix += '1' + blocknr;
    kameramodell = 'Canon PowerShot G12';
    picsdirectory += 'g12/';
} else if (kameratyp == '6dii') {
    bildnrpraefix += '3' + blocknr;
    kameramodell = 'Canon EOS 6D Mark II';
    picsdirectory += 'EOS6DII/';
} else if (kameratyp == 'ma') {
    bildnrpraefix += '4' + blocknr;
    kameramodell = 'FC2103';
    picsdirectory += 'MavicAir/';
} else {
    console.log('Unbekannte Kamera: ' + kameratyp);
    return;
}

if (aufnahmejahr > 0) {
    picsdirectory += aufnahmejahr + '/' + datum + '/';
} else {
    picsdirectory += datum + '/';
}

console.log('Kameratyp: ' + kameratyp + ' (' + kameramodell + ') | Aufnahmejahr: ' + aufnahmejahr + ' | Präfix: ' + bildnrpraefix + ' | ' +  picsdirectory);

// var picsdirectory = '../pics/';
var files_all = null;

try {
    files_all = dir.files(picsdirectory, { sync: true, recursive: false });
}
catch(err) {
    console.log(picsdirectory + ' nicht vorhanden.')
    console.log(err.message);
    return;
}

// console.log(files_all);
files_all.sort();

var files_jpg = files_all.filter(function(a)
{
    return a.toUpperCase().includes('.JPG') 
});

// console.log(files_jpg);
console.log(files_jpg.length + ' jpg-Dateien im Verzeichnis "' + picsdirectory + '".');

var db_connection = database.connect();

String.prototype.replaceAt = function(index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}

var bildnr_exif_lfdnr = 0;
var pic_anzahl = files_jpg.length;

if (pic_anzahl > 0 ) {
    read_exif(0);
} else {
    finish();
}

function read_exif(pic_no)
{
    if (pic_no >= pic_anzahl) {
        finish();
        return;
    }

    var jpg_data = fs.readFileSync(files_jpg[pic_no]);

    var parser = exif_parser.create(jpg_data);
    parser.enablePointers(true);
    parser.enableBinaryFields(true);
    parser.enableSimpleValues(false);
    var exif_data_1 = parser.parse();

    parser = exif_parser.create(jpg_data);
    parser.enableSimpleValues(true);
    var exif_data_2 = parser.parse();

    if (exif_data_1.tags.Model == kameramodell) {
        // Bildnummer
        var file = files_jpg[pic_no];
        var bildnr = '';

        if (file.toLowerCase().includes('img_')) {
            bildnr = bildnrpraefix + file.substr(file.toLowerCase().indexOf("img_") + 4, 4);
        } else {
            bildnr = bildnrpraefix + file.substr(file.toLowerCase().indexOf("dji_") + 4, 4);
        }

        var sql = 'SELECT bd.BildNr FROM ' + db_tbl_bilddaten + ' bd WHERE bd.BildNr=' + bildnr + ';';

        console.log(sql);

        db_connection.query(sql, function (err, result) {
            if (err) throw err;

            var kamera = '';
            var makernote = '';
            var objektiv = '';
            var aufnahmedatum = '';
            var exposuretime = 0.0;
            var belichtungszeit = '';
            var blende = '';
            var iso = '';
            var brennweite = '';
            var brennweite35mm = '';
            var blitz = '';
            var laenge = '';
            var breite = '';
            var hoehe = '';
            var film = 'DIGITAL';

            // Kamera
            kamera = exif_data_1.tags.Model;

            if (kamera == 'FC2103') {
                kamera = 'DJI Mavic Air';
                brennweite35mm = '24 mm';
                objektiv = '24 mm f/2.8 (KB)';
            }

            // Aufnahmedatum
            aufnahmedatum = exif_data_1.tags.CreateDate;
            aufnahmedatum = aufnahmedatum.replaceAt(4, '-');
            aufnahmedatum = aufnahmedatum.replaceAt(7, '-');

            // Belichtungszeit
            exposuretime = exif_data_2.tags.ExposureTime;

            if (exposuretime < 1) {
                belichtungszeit = '1/' + Math.round(1.0 / exposuretime) + ' s';
            } else {
                belichtungszeit = Math.round(exposuretime) + ' s';
            }

            // Blende
            blende = 'f/' + exif_data_2.tags.FNumber;

            // Brennweite
            brennweite = exif_data_2.tags.FocalLength + ' mm';

            // Objektiv
            if ('MakerNote' in exif_data_1.tags) {
                makernote = exif_data_1.tags.MakerNote.toString().replace(/[^\x20-\x7E]+/g, '');

                if (makernote.includes('EF100-400mm f/4.5-5.6L IS II USM')) {
                    objektiv = 'Canon EF 100-400mm f/4.5-5.6L IS II USM';
                } else if (makernote.includes('EF24-105mm f/4L IS II USM')) {
                    objektiv = 'Canon EF 24-105mm f/4L IS II USM';
                } else if (makernote.includes('EF24-105mm f/4L IS USM')) {
                    objektiv = 'Canon EF 24-105mm f/4L IS USM';
                } else if (makernote.includes('EF50mm f/1.8')) {
                    objektiv = 'Canon EF 50mm f/1.8';
                } else if (makernote.includes('28-75mm')) {
                    objektiv = 'Tamron SP AF 28-75mm F/2.8 XR Di LD Aspherical (IF) Macro';
                } else if (makernote.includes('12-24mm F4 DG HSM | Art')) {
                    objektiv = 'Sigma 12-24mm F4 DG HSM Art';
                } else if (makernote.includes('PowerShot G7 X')) {
                    objektiv = '24–100mm f/1.8–2.8 (KB)';
                    brennweite35mm = (Math.round(exif_data_2.tags.FocalLength * 27.2) / 10.0) + ' mm';
                } else if (makernote.includes('PowerShot G12')) {
                    objektiv = '28–140mm f/2.8–4.5 (KB)';
                    brennweite35mm = (Math.round(exif_data_2.tags.FocalLength * 45.90163934) / 10.0) + ' mm';
                } else if (makernote.includes('Canon EOS 5D Mark II') || makernote.includes('Canon EOS 6D Mark II')) {
                    if (blende == 'f/0') {
                        objektiv = 'Walimex Pro 12mm f/2,8 Fish-Eye';
                        brennweite = '12 mm';
                        blende = '';
                    } else {
                        objektiv = '';
                        console.log(makernote);
                    }
                } else {
                    // objektiv = '';
                    // console.log(makernote);
                }
            } else {
                // objektiv = '';
            }

            // ISO
            iso = exif_data_2.tags.ISO;

            // Blitz
            blitz = exif_data_2.tags.Flash;
            
            // Länge
            if ('GPSLongitude' in exif_data_2.tags) {
                laenge = '' + Math.round(exif_data_2.tags.GPSLongitude * 10000.0);
            } else {
                laenge = '0';
            }

            // Breite
            if ('GPSLatitude' in exif_data_2.tags) {
                breite = '' + Math.round(exif_data_2.tags.GPSLatitude * 10000.0);
            } else {
                breite = '0';
            }

            // Höhe
            if ('GPSAltitude' in exif_data_2.tags) {
                hoehe = '' + Math.round(exif_data_2.tags.GPSAltitude);
            } else {
                hoehe = '0';
            }

            console.log((bildnr_exif_lfdnr + 1) + ' | ' + bildnr + ' | ' + kamera + ' | ' + objektiv + ' | ' + aufnahmedatum + ' | ' + belichtungszeit
                + ' | ' + blende + ' | ' + iso + ' | ' + blitz + ' | ' + laenge + ' | ' + breite + ' | ' + hoehe);

            bildnr_exif_lfdnr++;
            var sql = '';

            if (result == null || result.length == 0) {
                // Neuer Datensatz
                // console.log('Neuer Eintrag.');

                sql = 'INSERT INTO ' + db_tbl_bilddaten + ' '
                    + '(BildNr, Aufnahmedatum, Kameradatum, Kamera, objektiv, Zeit, Blende, ISO, Blitz, Brennweite, Brennweite35mm, Laenge, Breite, Hoehe, Film) '
                    + 'VALUES ("' + bildnr + '", "' + aufnahmedatum + '", "' + aufnahmedatum + '", "' + kamera + '", "' + objektiv + '", '
                    + '"' + belichtungszeit + '", "' + blende + '", "' + iso + '", "' + blitz + '", "' + brennweite + '", "' + brennweite35mm + '", '
                    + '"' + laenge + '", "' + breite + '", "' + hoehe + '", "' + film + '");';

            } else {
                // Datensatz existiert bereits
                // console.log('Existierender Eintrag.');

                sql = 'UPDATE ' + db_tbl_bilddaten + ' SET '
                    + 'Aufnahmedatum="' + aufnahmedatum + '", '
                    + 'Kameradatum="' + aufnahmedatum + '", '
                    + 'Kamera="' + kamera + '", '
                    + 'objektiv="' + objektiv + '", '
                    + 'Zeit="' + belichtungszeit + '", '
                    + 'Blende="' + blende + '", '
                    + 'ISO="' + iso + '", '
                    + 'Blitz="' + blitz + '", '
                    + 'Brennweite="' + brennweite + '", '
                    + 'Brennweite35mm="' + brennweite35mm + '", '
                    + 'Laenge="' + laenge + '", '
                    + 'Breite="' + breite + '", '
                    + 'Hoehe="' + hoehe + '", '
                    + 'Film="' + film + '" '
                    + 'WHERE BildNr=' + bildnr + ';';
            }

            // console.log(sql);

            db_connection.query(sql, function (err, result) {
                if (err) throw err;
                console.log(bildnr + " | Result: insertId=" + result.insertId + ' / changedRows=' + result.changedRows);

                read_exif(pic_no + 1);
            });
        });
    } else {
        console.log(bildnr + ' | ' + exif_data_1.tags.Model + ' falsch.');
        finish();
    }
}

function finish()
{
    console.log('finish(): DB connection closed');
    database.disconnect(db_connection);
}
