"""
Util for the Medion/Lidl wifi/rf-plug system.
Connects to the Cloud Service and returns the configuration as Json file.
"""

import json
import urllib2
import hashlib
import argparse

accessKey = "Q763W08JZ07V23FR99410B3PC945LT28"

wifi_path = "/api/device/wifi/list"
rf_path = "/api/device/rf/list"
result = {}


def build_url(username, password, path):
    urlpref = "http://smart2connect.yunext.com"
    usr = urllib2.quote(username)
    pwd = hashlib.md5(password).hexdigest().upper()
    url = '{}{}?accessKey={}&username={}&password={}'.format(urlpref, path, accessKey, usr, pwd)

    return url


def getConfigJson(username, password, path):
    url = build_url(username, password, path)
    response = urllib2.urlopen(url)
    return json.load(response)


def get_device_name_from_item(item):
    return item['deviceName']


def get_mac_address_from_item(item):
    return item['macAddress']


def get_code_from_item(item):
    """
    Builds the 'code' fragment out of different dict values.
    :param item: a dict containing the needed values.
    :return: Compiled code as string
    """
    code = "{}{}{}".format(item['companyCode'], item['deviceType'], item['authCode'])
    return code


def create_plug_cfg(item, companyCode=None):
    """
    Creates a plug configuration from the dictionary item.
    Only the wifi plug will return the company code
    :param item: dictionary containing the needed values.
    :param companyCode: Optional, not returned for the rf-plugs, only for the wifi-plug.
                        It needs to be provided from outside for the rf-plug-configs
    :return: JSON/dictionary object containing the configuration
    """
    plug_cfg = {}
    plug_cfg['mac'] = get_mac_address_from_item(item)

    if 'companyCode' in item:
            plug_cfg['code'] = get_code_from_item(item)

    if not companyCode is None:
        plug_cfg['code'] = companyCode

    if 'addressCode' in item:
            plug_cfg['rfslave'] = item['addressCode']

    return plug_cfg


if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--username", type=str, help="Username of Cloud Service Login", required=True)
    parser.add_argument("--password", type=str, help="Password of Cloud Service Login", required=True)
    parser.add_argument("--filename", type=str, help="Write the configuration to a file")

    args = parser.parse_args()
    # Start with the wifi-plug path
    data = getConfigJson(args.username, args.password, wifi_path)
    for item in data['list']:

        companyCode = get_code_from_item(item)

        plug_cfg = create_plug_cfg(item)
        result[get_device_name_from_item(item)] = plug_cfg

    # go on with the RF-plug configuration
    data = getConfigJson(args.username, args.password, rf_path)
    for item in data['list']:
        plug_cfg = create_plug_cfg(item, companyCode)
        result[get_device_name_from_item(item)] = plug_cfg

    print json.dumps(result)

    if args.filename is None:
        print("No file output")
    else:
        print("Output to file {}".format(args.filename))
        with open(args.filename, "w") as outfile:
            json.dump(result, outfile, indent=4)
