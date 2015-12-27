var dgram  = require('dgram');

var prefix = '01 40';
var on  = '10 4C F7 5F 5A 28 A1 81 57 4A C1 B5 63 CD 51 A7 8D';
var off = '10 F7 B4 E7 4B 97 0D 96 F3 CA 2B B5 D3 CD 1C 19 D0';

function cmdString(plug, stateOn) {
  var parts = [];
  parts.push(prefix);
  parts.push(plug.mac);
  parts.push(stateOn ? on : off);
  return parts.join(' ').replace(/ /g,'');
}

var exports = module.exports = {};

exports.sendPlugCmd=function (plug, stateOn) {
    var message = new Buffer(cmdString(plug, stateOn), "hex");
    var client = dgram.createSocket('udp4');
    console.log("Sending: " + stateOn + " to " + plug.name);
    client.send(message, 0, message.length, 8530, plug.address, function(err, bytes) {
      if (err)
        throw err;
      console.log('UDP message ('+message+') sent to ' + plug.address +':8530');
      client.close();
    });
};

exports.sendPlugRawCmd=function (ip, rawContent) {
    var message = new Buffer(rawContent, "hex");
    var client = dgram.createSocket('udp4');
    console.log("Sednging: "+rawContent);
    client.send(message, 0, message.length, 8530, ip, function(err, bytes) {
      if (err)
        throw err;
      console.log('UDP message ('+message+') sent to ' + ip +':8530');
      client.close();
    });
};
//{
//  sendPlugCmd(testplug, true);
//  sendPlugCmd(testplug, false);
//}
