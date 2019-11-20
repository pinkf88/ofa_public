// npm install emailjs

var email   = require('emailjs');
var config  = require('../configs/ofa_config.json');


var server 	= email.server.connect(config.mailserver);

module.exports = {
    send: function(msg_txt, msg_subject, address_from=address_to=config.other.address_from, address_to=config.other.address_to) {
        server.send({
            text: msg_txt,
            from: address_from,
            to: address_to,
            cc: "",
            subject: '[PI] ' + msg_subject
        }, function(err, message) {
            console.log(err);
        });
    }
}
