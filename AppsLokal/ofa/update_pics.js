var fs              = require('fs');
var path            = require("path");
var colors          = require('colors');
var piexif          = require("piexifjs");
var ArgumentParser  = require('argparse').ArgumentParser;
var recursive       = require('recursive-readdir');
var database        = require('../libs/lib_database.js');
var config          = require('../configs/ofa_config.json');


var parser = new ArgumentParser({
    add_help:        false,
    description:    'update_pics'
});

parser.add_argument(
    '-d'    // Directory
);

var args = parser.parse_args();
console.log(args);

if (args.d == null) {
    console.log(colors.yellow('update_pics | Missing -d directory'));
    process.exit();
}

if (fs.existsSync(args.d) == false) {
    console.log(colors.yellow('update_pics | Directory does not exist: ' + args.d));
    process.exit();
}


var root_dir = args.d;
var file_list = [];

var db_connection = database.connect();

function readDirectory(dir)
{
    file_list = [];

    recursive(dir, function (err, files) {
        if (err) {
            console.log('update_pics | readDirectory(): ' + err.message);
            finish();
        }

        files.sort();

        for (var i = 0; i < files.length; i++) {
            if (files[i].toLowerCase().slice(-4) == '.jpg') {

                try {
                    // if (parseInt(path.basename(files[i])) < 100000 || parseInt(path.basename(files[i])) > 10000000) {
                        // Dias, Tickets
                        file_list.push(files[i]);
                    // }
                } catch(e) {
                    console.log(colors.yellow('update_pics | readDirectory(): ' + e.message));
                    console.log(colors.yellow('update_pics | readDirectory(): ' + files[i] + ' / ' + path.basename(files[i])));
                    process.exit();
                }
            }
        };

        // console.log(files);

        if (file_list.length > 0) {
            console.log('update_pics | readDirectory(): Anzahl Dateien: ' + file_list.length);
            readDatabase(0);
        } else {
            console.log(colors.yellow('update_pics | readDirectory(): Keine Dateien gefunden.'));
        }
    });
}

var updated = 0;

function readDatabase(file_no)
{
    if (file_no >= file_list.length) {
        console.log('update_pics | readDatabase(): Anzahl Dateien updated: ' + updated);
        finish();
        return;
    }

    var nummer = parseInt(path.basename(file_list[file_no]));
    var bild_sql = 'SELECT b.datum, bd.Aufnahmedatum FROM ofa_bild b LEFT JOIN ofa_bilddaten bd ON (bd.BildNr=b.datei) WHERE b.nummer="' + nummer + '" ';

    db_connection.query(bild_sql, function (err, result) {
        if (err) {
            // throw err;
            console.log(colors.yellow('update_pics | readDatabase(): ERROR: ' + err.message));
            console.log(bild_sql);
            process.exit();
        }

        if (result == null || result.length == 0) {
            console.log(colors.yellow('update_pics | readDatabase(): Datei ' + file_list[file_no] + ' NICHT in Datenbank vorhanden.'));

            file_no++;
            readDatabase(file_no);
        } else {
            var date_original = result[0].datum + ' 12:00:00';

            if (result[0].Aufnahmedatum != null && result[0].Aufnahmedatum != '') {
                date_original = result[0].Aufnahmedatum;
            }

            updateExif(file_no, date_original);
        }
    });
}

function updateExif(file_no, date_original)
{
    // console.log(colors.green('update_pics | updateExif(): path=' + file_list[file_no] + ', date_original=' + date_original));

    try {
        var jpeg = fs.readFileSync(file_list[file_no]);
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
            updated++;
            fs.writeFileSync(file_list[file_no], Buffer.from(piexif.insert(piexif.dump(exif), data), 'binary'));
        }
    } catch(e) {
        console.log(e);
        console.log(colors.yellow('update_pics | updateExif(): ' + e.message));
        console.log(colors.yellow('update_pics | updateExif(): ' + file_list[file_no]));
    }

    file_no++;

    setTimeout(function() {
        readDatabase(file_no);
    }, 1);
}

function finish()
{
    console.log('update_pics | finish(): DB connection closed');
    database.disconnect(db_connection);
    process.exit();
}

readDirectory(root_dir);
