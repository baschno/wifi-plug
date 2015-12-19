var util = require('util');
var http = require('http');


var express     = require('express');
var bodyparser = require('body-parser');
var app = express();

var plug = { name: "Plug1", address: "192.168.178.27", mac:"AC CF 23 38 AE F6" };

app.use(bodyparser.urlencoded({extended:true}));
app.use(bodyparser.json());

var port = process.env.port || 8888;

// ROUTES FOR OUR API
// =============================================================================
var router = express.Router();              // get an instance of the express Router

// middleware to use for all requests
router.use(function(req, res, next) {
    // do logging
    console.log('Something is happening.');
    next(); // make sure we go to the next routes and don't stop here
});

// test route to make sure everything is working (accessed at GET http://localhost:8080/api)
router.get('/', function(req, res) {
    res.json({ message: 'hooray! welcome to our api!' });
});



// more routes for our API will happen here
router.route('/plug')
  .get(function(req, res) {
    res.json({message: plug}
  );
});


router.route('/plug/:action')
  .get(function(req, res){
    switch (req.params.action) {
      case 'on':
        res.send("on her!")
        break;
      case 'off':
        res.send("off her!")
        break;
      default:

    }
    res.send(req.params.action);
  });


// REGISTER OUR ROUTES -------------------------------
// all of our routes will be prefixed with /api
app.use('/api', router);

// START THE SERVER
// =============================================================================
app.listen(port);
console.log('Magic happens on port ' + port);
