var dateformat  = require('dateformat');


module.exports = {
    log: function(s)
    {
        console.log(dateformat(new Date(), 'dd.mm.yyyy HH:MM:ss  ') + s);
    },

    get_subpfad: function(nummer, jahr = 0)
    {
        if (nummer > 10000000) {
            // Ticket
            return 'tickets/' + jahr + '/';
        }
    
        var s1 = "" + (Math.floor(nummer / 1000) * 1000);
        var s2 = "" + (Math.floor(nummer / 100) * 100);
        
        return s1.padStart(6, '0') + '/' + s2.padStart(6, '0') + '/';
    },
    
    get_subpfad1000: function(nummer, jahr = 0)
    {
        if (nummer > 10000000) {
            // Ticket
            return 'tickets/' + jahr + '/';
        }
    
        var s1 = "" + (Math.floor(nummer / 1000) * 1000);
        
        return s1.padStart(6, '0') + '/';
    },

    get_subpfad_web: function(nummer)
    {
        var s = '' + (Math.floor(nummer / 1000) * 1000);

        return s.padStart(6, '0') + '/';
    }
};
