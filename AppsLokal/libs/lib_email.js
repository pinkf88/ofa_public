// npm install emailjs

var { SMTPClient }  = require('emailjs');
var consts          = require('../../configs/ofa_consts.json');


module.exports = {
    send: function(msg_subject, msg_txt, address_from=consts.MAILSERVER.ADDRESS_FROM, address_to=consts.MAILSERVER.ADDRESS_TO) {
        send(msg_subject, msg_txt, address_from, address_to);
    }
}


var error_counter = 0;

function send(msg_subject, msg_txt, address_from, address_to)
{
    const mail_client = new SMTPClient({
        user:       consts.MAILSERVER.USER,
        password:   consts.MAILSERVER.PASSWORD,
        host:       consts.MAILSERVER.HOST,
        ssl:        true,
    });
    
    
    console.log('Message sending: ' + msg_subject);

    mail_client.send({
            text:       msg_txt,
            from:       address_from,
            to:         address_to,
            cc:         '',
            subject:    '[PI] ' + msg_subject
        },
        (err, message) => {
            if (err) {
                console.log(err);

                if (error_counter > 3) {
                    error_counter = 0;
                } else {
                    setTimeout( function() {
                        error_counter++;
                        send(msg_subject, msg_txt, address_from, address_to);
                    }, 300000);
                }
            } else {
                error_counter = 0;
            }
        }
    );
}
