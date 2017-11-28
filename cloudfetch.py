import json
import urllib2
import hashlib


accessKey = "Q763W08JZ07V23FR99410B3PC945LT28"
username = urllib2.quote("icomen@schnorbus.net")
pwd = hashlib.md5("5Tfg4s").hexdigest().upper()
urlpref = "http://smart2connect.yunext.com"
wifi_path = "/api/device/wifi/list"
rf_path = "/api/device/rf/list"


def getConfigJson(path):
    url = '{}{}?accessKey={}&username={}&password={}'.format(urlpref, path, accessKey, username, pwd)

    print("url: " + url)
    response = urllib2.urlopen(url)
    data = json.load(response)
    return data


def get_device_name_from_item(item):
    return item.deviceName


def get_mac_address_from_item(item):
    return item['macAddress']


data = getConfigJson(wifi_path)
print json.dumps(data)
data = getConfigJson(rf_path)
print data


for item in data['list']:
    print item
    print get_device_name_from_item(item)
    print get_mac_address_from_item(item)
