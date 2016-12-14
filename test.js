var state = 1;
var mac = "ACCF2338AEF6";
var code = "C1117150";
var rfslave = "1C3966";

var on = false;

var start = "0140" + mac + "10";
var value = "";
if (on === true) {
    value = "60";
} else {
    value = "70";
}
var before_encode = "00ffff" + code + "08" + rfslave + value + "04040404";
console.log(start + before_encode);
