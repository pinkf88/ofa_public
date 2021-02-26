// https://www.npmjs.com/package/ssh2-sftp-client

var fs              = require('fs-extra');
var dir             = require('node-dir');
var colors          = require('colors');
var ArgumentParser  = require('argparse').ArgumentParser;


var parser = new ArgumentParser({
    add_help: true,
    description: 'serie-2-serie'
});

parser.add_argument(
    '-d',
    {
        help: 'Source directory.',
        required: true
    }
);

parser.add_argument(
    '-a',
    {
        help: 'Anzahl. a=3 bedeutet jedes 3. Bild wird behalten.',
        required: true
    }
);

var args = parser.parse_args();

var PFAD_SOURCE = args.d;

if (fs.existsSync(PFAD_SOURCE) == false) {
    console.log(colors.yellow('Pfad ' + PFAD_SOURCE + ' existiert nicht.'));
    return;
}

var anzahl = parseInt(args.a);

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

var files_jpg = files_all.filter(function(a) {
    return a.toUpperCase().includes('.JPG');
});

// console.log(files_jpg);
console.log(files_jpg.length + ' jpg-Dateien im Verzeichnis "' + PFAD_SOURCE + '".');

if (files_jpg.length < 1) {
    console.log(colors.yellow('Keine jpg-Dateien vorhanden.'));
    return;
}

var j = 1;

for (var i = 0; i < files_jpg.length; i++) {
    if (j == anzahl) {
        j = 1;
    } else {
        fs.removeSync(files_jpg[i]);
        j++;
    }
}
