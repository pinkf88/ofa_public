let dateFormat = require('dateformat');


module.exports = {
    log: function(s)
    {
        console.log(dateFormat(new Date(), 'dd.mm.yyyy HH:MM:ss  ') + s);
    },
};
