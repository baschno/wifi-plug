var plugutil = require('./plugutil');
var query = require('cli-interact').getYesNo;

var arr = [
"0140accf2338aef610727152e1d8a0e8d8fc2c44450b213015",
"0140accf2338aef610f2864d9b65b01872b304f3e5e10dce21",
"0140accf2338aef610409e99ab07845114d9b0a2da64724108",
"0140accf2338aef61086028bd36f190ae0b535d19775f292d1",
"0140accf2338aef610ac549c772e6327cff189064462444f5e",
"0140accf2338aef610157a56f69f49dae72c55ebaee6dbd2a5",
"0140accf2338aef61056082a03419842be9f2c81404d032491",
"0140accf2338aef610ad5ebe14233ff9d3748613ab6e082a07",
"0140accf2338aef610a567a153f83c3aec6a189f8003ddee75",
"0140accf2338aef610a9c48f1fe984edc7a4e4d0b1ffe725eb",
"0140accf2338aef6101c383bd053fe75b47e25d560e4a16cc8"
];

var i = 1;
arr.forEach(function(item){
  console.log(i + ": " +item);
  plugutil.sendPlugRawCmd('192.168.178.27', item)
  var answer = query('Is it true');
  i++;
});