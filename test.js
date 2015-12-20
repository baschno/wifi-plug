var util = require('util');
var http = require('http');
var net  = require('net');
var dgram  = require('dgram');


var plug = { name: "Plug1", address: "127.0.0.1", mac:"AC CF 23 38 AE F6" };
//var plug = { name: "Plug1", address: "192.168.178.27", mac:"AC CF 23 38 AE F6" };
var on  = '10 4C F7 5F 5A 28 A1 81 57 4A C1 B5 63 CD 51 A7 8D';
var off = '10 F7 B4 E7 4B 97 0D 96 F3 CA 2B B5 D3 CD 1C 19 D0';
var prefix = '01 40';

//var message = new Buffer("01 40".replace(' ',''), "hex");
//var tmp = prefix + ' ' + plug.mac + ' ' + on;
var tmp = "10 4C F7 5F 5A 28 A1 81 57 4A C1 B5 63 CD 51 A7 8D";
console.log(tmp);
var message = new Buffer(tmp.replace(' ',''), "hex");
var client = dgram.createSocket('udp4');
console.log(message);
client.send(message, 0, message.length, 8530, plug.address, function(err, bytes) {
    if (err) throw err;
    console.log('UDP message sent to ' + plug.address +':8530');
    client.close();
});
