import json
import urllib2
import hashlib
import sys


accessKey = "Q763W08JZ07V23FR99410B3PC945LT28"

wifi_path = "/api/device/wifi/list"
rf_path = "/api/device/rf/list"
result = {}


def buildUrl(username, password, path):
    urlpref = "http://smart2connect.yunext.com"
    usr = urllib2.quote(username)
    pwd = hashlib.md5(password).hexdigest().upper()
    url = '{}{}?accessKey={}&username={}&password={}'.format(urlpref, path, accessKey, usr, pwd)

    return url


def getConfigJson(username, password, path):
    url = buildUrl(username, password, path)

    print("url: " + url)
    response = urllib2.urlopen(url)
    data = json.load(response)
    return data


def get_device_name_from_item(item):
    return item['deviceName']


def get_mac_address_from_item(item):
    return item['macAddress']


def get_code_from_item(item):
    code = "{}{}{}".format(item['companyCode'], item['deviceType'], item['authCode'])
    return code


if __name__ == "__main__":

    if len(sys.argv)<2:
        print("Usage: {} <login> <password>".format(sys.argv[0]))
        sys.exit()

    usr = sys.argv[1]
    pwd = sys.argv[2]

    data = getConfigJson(usr, pwd, wifi_path)
    for item in data['list']:
        print item
        result[get_device_name_from_item(item)] = "hhooo"

    print result
    data = getConfigJson(usr, pwd, rf_path)

    for item in data['list']:
        print item

