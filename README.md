# wifi-plug
python script and node-js rest api server for accessing a WIFI plug provided by Hi-flying electronics technology Co.

Sold in germany by Lidl, Medion, ...

## Installation

### Fetch Plug configuration

Use `fhem/wifiplug-cloudfetch.php` script which will connect to the cloud service and read the configuration.
Put it into a `config.json` file.

### Virtualenv for Python/flask:
````
sudo apt install virtualenv
python3 -m venv flaskenv
source ./flaskenv/bin/activate
pip install flask jsonify

````




# Resources
- http://forum.fhem.de/index.php/topic,38112.75.html
- https://scotch.io/tutorials/build-a-restful-api-using-node-and-express-4
- http://stackoverflow.com/questions/18880301/node-js-convert-hexadecimal-number-to-bytearray
- http://stackoverflow.com/questions/11498508/socket-emit-vs-socket-send
- http://www.w3schools.com/js/js_objects.asp
