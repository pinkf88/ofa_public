var fs              = require('fs-extra');
var path            = require('path');
var dir             = require('node-dir');
var colors          = require('colors');
var ArgumentParser  = require('argparse').ArgumentParser;
var config          = require('../../configs/ofa_config.json');


const PFAD_SERIE = config.apps.clean_dirs.path_serie;
const PFAD_OFA = config.apps.clean_dirs.path_ofa;
const PFAD_OFA_BACKUP = config.apps.clean_dirs.path_ofa_backup;
const PFAD_WEB_MINI = config.apps.clean_dirs.path_web_mini;
const PFAD_WEB_PIC = config.apps.clean_dirs.path_web_pic;
const PFAD_WEB_LARGE = config.apps.clean_dirs.path_web_large;

var parser = new ArgumentParser({
    add_help: true,
    description: 'upload'
});

parser.add_argument(
    '-s',
    {
        help: 'Start number.',
        required: true
    }
);

parser.add_argument(
    '-e',
    {
        help: 'End number.',
        required: true
    }
);

parser.add_argument(
    '--serie',
    {
        help: 'Verzeichnis Serie.',
        required: true
    }
);

var args = parser.parse_args();
var number_start = args.s;
var number_end = args.e;

if (number_start > 10000000 || number_end > 10000000) {
    console.log('Ungültige Nummer(n).');
    return;
}

var pfad_serie = PFAD_SERIE + args.serie;

cleanDirectory(pfad_serie, 'z0', 'jpg');
cleanDirectory(pfad_serie, '_hd', 'jpg');

var dirs = [];

for (var number = number_start; number <= number_end; number++) {
    var sub_path = get_subpfad(number);

    if (dirs.indexOf(sub_path) == -1) {
        dirs.push(sub_path);
    }
}

// console.log(dirs);

for (var i = 0; i < dirs.length; i++) {
    var ofa_path = PFAD_OFA + dirs[i];
    cleanDirectory(ofa_path, 'z0', 'jpg');
    cleanDirectory(ofa_path, 'z0', 'gif');
    cleanDirectory(ofa_path, 'z0', 'png');
    cleanDirectory(ofa_path, '', 'jpg');
    cleanDirectory(ofa_path, '', 'gif');
    cleanDirectory(ofa_path, '', 'png');

    ofa_path = PFAD_OFA_BACKUP + dirs[i];
    cleanDirectory(ofa_path, 'z0', 'jpg');
    cleanDirectory(ofa_path, 'z0', 'gif');
    cleanDirectory(ofa_path, 'z0', 'png');
    cleanDirectory(ofa_path, '', 'jpg');
    cleanDirectory(ofa_path, '', 'gif');
    cleanDirectory(ofa_path, '', 'png');
}

dirs.length = 0;

for (var number = number_start; number <= number_end; number++) {
    var sub_path = get_subpfad1000(number);

    if (dirs.indexOf(sub_path) == -1) {
        dirs.push(sub_path);
    }
}

console.log(dirs);

for (var i = 0; i < dirs.length; i++) {
    var web_path = PFAD_WEB_MINI + dirs[i];
    cleanDirectory(web_path, '_m', 'jpg');

    web_path = PFAD_WEB_PIC + dirs[i];
    cleanDirectory(web_path, '', 'jpg');

    web_path = PFAD_WEB_LARGE + dirs[i];
    cleanDirectory(web_path, '', 'jpg');
}

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

function cleanDirectory(pfad, suffix, extension)
{
    if (fs.existsSync(pfad) == false) {
        console.log(colors.yellow('Pfad ' + pfad + ' existiert nicht.'));
        return;
    }

    var files_all = null;

    try {
        files_all = dir.files(pfad, { sync: true });
    }
    catch(err) {
        console.log(colors.yellow('Keine Dateien in ' + pfad + ' vorhanden.'));
        console.log(err.message);
        return;
    }

    if (files_all == null || files_all.length < 1) {
        console.log(colors.yellow('Keine Dateien in ' + pfad + ' vorhanden.'));
        return;
    }

    // console.log(files_all);
    files_all.sort();

    var files_jpg = files_all.filter(function(a) {
        return a.toLowerCase().includes(suffix.toLowerCase() + '.' + extension.toLowerCase());
    });

    // console.log(files_jpg);

    if (files_jpg.length < 1) {
        console.log(colors.yellow('Keine löschbaren Dateien mit Suffix ' + suffix + ' und Extension ' + extension + ' in ' + pfad + ' vorhanden.'));
        return;
    }

    console.log(files_jpg.length + ' jpg-Dateien im Verzeichnis "' + pfad + '".');

    var counter = 0;

    for (var i = 0; i < files_jpg.length; i++) {
        var number = parseInt(path.basename(files_jpg[i], path.extname(files_jpg[i])));

        if (number >= number_start && number <= number_end) {
            fs.removeSync(files_jpg[i]);
            console.log(colors.green(files_jpg[i] + ' gelöscht.'));
            counter++;
        }
    }

    if (suffix == '') {
        console.log(colors.green(counter + ' Dateien ohne Suffix und mit Extension ' + extension + ' in ' + pfad + ' gelöscht.'));
    } else {
        console.log(colors.green(counter + ' Dateien mit Suffix ' + suffix + ' und Extension ' + extension + ' in ' + pfad + ' gelöscht.'));
    }
}
