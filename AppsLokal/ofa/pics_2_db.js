var ArgumentParser  = require('argparse').ArgumentParser;
var recursive       = require('recursive-readdir');
var path            = require('path');
var sizeOf          = require('image-size');
var colors          = require('colors');
var database        = require('../libs/lib_database.js');
var config          = require('../configs/ofa_config.json');


var parser = new ArgumentParser({
    add_help:        false,
    description:    'mavic_panos'
});

parser.add_argument(
    '--root',   // e.g. "E:/OfaPics/SerienHD"
    {
        required: false
    }
);

parser.add_argument(
    '--dir',   // Directory
    {
        required: false
    }
);

parser.add_argument(
    '-u',
    {
        help: 'Update.',
        action: 'store_true'
    }
);

var args = parser.parse_args();

var root_dir = 'E:/OfaPics/SerienHD';

if (args.root != undefined && args.root != '') {
    root_dir = args.root;

}

var sub_dir = '';

if (args.dir != undefined && args.dir != '') {
    sub_dir = '' + args.dir;
    
}

var update = false;

if (args.u != undefined && args.u != '') {
    update = args.u;
    
}

var db_connection = database.connect();

var file_no = 0;
var file_hds = [];

recursive(root_dir, function (err, files) {
    if (err) {
        console.log('pics_2_db | readDirectory(): ' + err.message);
        finish();
    }

    for (var i = 0; i < files.length; i++) {
        if (sub_dir != '' && files[i].replace(/\\/g, '/').includes(sub_dir)) {
            var file_hd = {
                pfad:       files[i].replace(/\\/g, '/'),
                nummer:     parseInt(path.basename(files[i].replace(/\\/g, '/').substr(root_dir.length)))
            };

            file_hds.push(file_hd);
        }
    }

    // console.log(file_hds);

    if (file_hds.length > 0) {
        checkAndWriteDb(0);
    } else {
        finish();
    }
});

function checkAndWriteDb(file_no)
{
    if (file_no >= file_hds.length) {
        finish();
        return;
    }

    var sql = 'SELECT b.id, b.nummer, b.hd_path FROM ofa_bild b WHERE b.nummer="' + file_hds[file_no].nummer + '"';
    
    if (update == false) {
        sql += ' AND (b.hd_path IS NULL OR b.hd_path=\"\");';
    }

    // console.log(sql);

    db_connection.query(sql, function (err, result) {
        if (err) {
            console.log(err.message);

            setTimeout(function() {
                connectDatabase();
                checkAndWriteDb(jahr, gruppe); 
            }, 60000);

            return;
        }

        // console.log(result);

        if (result == null || result.length == 0) {
            // console.log('Bild ' + file_hds[file_no].nummer + ' existiert nicht in Datenbank oder hat schon einen Pfad.');

            file_no++;

            setTimeout(function() {
                checkAndWriteDb(file_no); 
            }, 1);
        } else {
            var dimensions = sizeOf(file_hds[file_no].pfad);
            var hd_path = file_hds[file_no].pfad.substr(root_dir.length);
            var hd_path_old = result[0]['hd_path'];

            if (hd_path != hd_path_old) {
                var sql = 'UPDATE ofa_bild b SET b.hd_path="' + hd_path + '", b.hd_width="' + dimensions.width + '", b.hd_height="' + dimensions.height + '" WHERE b.id="' + result[0]['id'] + '";';
                // console.log(sql);

                db_connection.query(sql, function (err, result) {
                    if (err) {
                        console.log(err.message);
                        console.log(sql);
                        setTimeout(function() {
                            connectDatabase();
                            checkAndWriteDb(jahr, gruppe); 
                        }, 60000);

                        return;
                    }

                    console.log('UPDATED | ' + file_hds[file_no].nummer + ': alt=' + hd_path_old + ' / neu=' + hd_path + ' | ' + dimensions.width + '/' + dimensions.height);

                    file_no++;

                    setTimeout(function() {
                        checkAndWriteDb(file_no); 
                    }, 1);
                });
            } else {
                console.log('NOT UPDATED | ' + file_hds[file_no].nummer + ': alt=' + hd_path_old + ' / neu=' + hd_path + ' | ' + dimensions.width + '/' + dimensions.height);

                file_no++;

                setTimeout(function() {
                    checkAndWriteDb(file_no); 
                }, 1);
            }
        }
    });
}

function finish()
{
    console.log('finish(): DB connection closed');
    db_connection.destroy();
}
