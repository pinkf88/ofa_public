var mysql = require('mysql');
var config = require('../configs/ofa_config.json');

var db_connection = null;

module.exports = {
    connect: function()
    {
        if (db_connection != null) {
            db_connection.destroy();
        }

        db_connection = mysql.createConnection(config.database);

        return db_connection;
    },

    disconnect: function (con = db_connection)
    {
        con.destroy();
        // tools.log('finish(): DB connection closed');
    }
}
