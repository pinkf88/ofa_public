var fs              = require('fs-extra');
var path            = require('path');
var dir             = require('node-dir');
var colors          = require('colors');
var ArgumentParser  = require('argparse').ArgumentParser;
var tools           = require('../libs/lib_tools.js');
var config          = require('../configs/ofa_config.json');


const PFAD_SERIE = config.apps.clean_dirs.path_serie;
const PFAD_SERIE_BACKUP = config.apps.clean_dirs.path_serie_backup;
const PFAD_OFA = config.apps.clean_dirs.path_ofa;
const PFAD_OFA_BACKUP = config.apps.clean_dirs.path_ofa_backup;
const PFAD_WEB_PIC = config.apps.clean_dirs.path_web_pic;
const PFAD_WEB_LARGE = config.apps.clean_dirs.path_web_large;
const SUFFIX_Z0 = config.apps.convert.suffix_z0;


var parser = new ArgumentParser({
    add_help: true,
    description: 'upload'
});

parser.add_argument(
    '-s',
    {
        help: 'Start number.'
    }
);

parser.add_argument(
    '-e',
    {
        help: 'End number.'
    }
);

parser.add_argument(
    '--serie',
    {
        help: 'Verzeichnis Serie.'
    }
);

parser.add_argument(
    '-test',
    {
        help: 'Test.',
        action: 'store_true'
    }
);

var args = parser.parse_args();
var number_start = -1;
var number_end = -1;
var pfad_serie = '';

if (args.test == null || args.test == false) {
    if (args.s != null) {
        number_start = parseInt(args.s);
    }

    if (args.e != null) {
        number_end = parseInt(args.e);
    }

    if (args.dir != null) {
        pfad_serie = args.serie;
    }
} else {
    number_start = 2106841;
    number_end = 2106841;
    args.serie = '2021/2021_LigurienToskanaGardasee/';
}

var numbers = [];

if (number_start > 10000000 || number_end > 10000000) {
    console.log('Ungültige Nummer(n).');
    process.exit();
}

for (var number = number_start; number <= number_end; number++) {
    numbers.push(number);
}

if (args.test == null || args.test == false) {
    console.log((number_end - number_start + 1) + ' Dateien löschen? Taste drücken. Ansonsten CTRL+c.');

    process.stdin.once('data', function () {
        clean_dirs();
        process.exit();
    });
} else {
    clean_dirs();
    process.exit();
}

function clean_dirs()
{
    var pfad_serie = PFAD_SERIE + args.serie;

    cleanDirectory(pfad_serie, 'z0', 'jpg');
    cleanDirectory(pfad_serie, '_hd', 'jpg');

    var pfad_serie_backup = PFAD_SERIE_BACKUP + args.serie;

    cleanDirectory(pfad_serie_backup, 'z0', 'jpg');
    cleanDirectory(pfad_serie_backup, '_hd', 'jpg');

    var dirs = [];

    for (var i = 0; i < numbers.length; i++) {
        var sub_path = tools.get_subpfad(numbers[i]);

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

    for (var i = 0; i < numbers.length; i++) {
        var sub_path = tools.get_subpfad1000(numbers[i]);

        if (dirs.indexOf(sub_path) == -1) {
            dirs.push(sub_path);
        }
    }

    console.log(dirs);

    for (var i = 0; i < dirs.length; i++) {
        var web_path = PFAD_WEB_PIC + dirs[i];
        cleanDirectory(web_path, '', 'jpg');

        web_path = PFAD_WEB_LARGE + dirs[i];
        cleanDirectory(web_path, '', 'jpg');
    }
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

        if (numbers.indexOf(number) != -1) {
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
