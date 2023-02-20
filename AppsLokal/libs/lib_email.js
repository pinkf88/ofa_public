// npm install emailjs

var { SMTPClient }  = require('emailjs');
var config          = require('../configs/ofa_config.json');


module.exports = {
    send: function(msg_subject, msg_txt, address_from=config.mailserver.address_from, address_to=config.mailserver.address_to) {
        send(msg_subject, msg_txt, address_from, address_to);
    }
}


var error_counter = 0;

async function send(msg_subject, msg_txt, address_from, address_to, attachment = [])
{
    const mail_client = new SMTPClient({
        user:       config.mailserver.user,
        password:   config.mailserver.password,
        host:       config.mailserver.host,
        ssl:        true,
        timeout:    60000
    });

    console.log('Message sending: ' + msg_subject);

    var message = {
        text:       msg_txt,
        from:       address_from,
        to:         address_to,
        cc:         '',
        subject:    '[PI] ' + msg_subject,
        attachment: attachment
    };

    var error_counter = 0;

    do {
        try {
            var ret = await mail_client.sendAsync(message);
            // tools.log('A');
            // console.log('sendAsync ret=', ret);
            error_counter = 0;
        } catch (err) {
            // tools.log('B');
            // console.log('sendAsync err=', err);
            error_counter++;

            if (error_counter > 3) {
                error_counter = 0;
            } else {
                await tools.wait(65000);
            }
        }
    } while (error_counter > 0);

    console.log('Message sent: ' + msg_subject);
}
