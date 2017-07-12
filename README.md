# wifi-plug
python script and node-js rest api server for accessing a WIFI plug provided by Hi-flying electronics technology Co.

Sold in germany by Lidl, Medion, ...

## Installation

### Prerequisite
Install php (until php part is not rewritten to python)
```
sudo apt-get install php5
```
### Fetch Plug configuration

Use `fhem/wifiplug-cloudfetch.php` script which will connect to the cloud service and read the configuration.
Put it into a `config.json` file.

### Virtualenv for Python/flask:
````
sudo apt install virtualenv
virtualenv run
source ./run/bin/activate
pip install -r requirements.txt

````




# Resources
- http://forum.fhem.de/index.php/topic,38112.75.html
- https://scotch.io/tutorials/build-a-restful-api-using-node-and-express-4
- http://stackoverflow.com/questions/18880301/node-js-convert-hexadecimal-number-to-bytearray
- http://stackoverflow.com/questions/11498508/socket-emit-vs-socket-send
- http://www.w3schools.com/js/js_objects.asp
