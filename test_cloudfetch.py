import pytest
import json
import cloudfetch
import binascii

def test_hexlify():
    hexstr = "abcdef0123456"
    print(binascii.unhexlify(hexstr))

def test_item_content():
    test_dict = '{"deviceName": "Lampe", "macAddress": "AFFE", "orderNumber": 1, "authCode": "7150", "deviceType": "11", "addressCode": "1ADD",  "companyCode": "C1"}'

    test_data = json.loads(test_dict)

    assert "Lampe" == cloudfetch.get_device_name_from_item(test_data)
    assert "AFFE" == cloudfetch.get_mac_address_from_item(test_data)
    assert "C1117150" == cloudfetch.get_code_from_item(test_data)


def test_te():
    test_dict = '{"list":[{"deviceName": "Lampe", "macAddress": "AFFE", "orderNumber": 1, "authCode": "7150", "deviceType": "11", "companyCode": "C1"}]}'
    test_data = json.loads(test_dict)
    for item in test_data['list']:
        print(item)
        assert "Lampe" == cloudfetch.get_device_name_from_item(item)
        assert "AFFE" == cloudfetch.get_mac_address_from_item(item)


def test_buildUrl():
    test_url = cloudfetch.build_url("u@ser", "pwd", "/path")
    assert test_url == "http://smart2connect.yunext.com/path?accessKey=Q763W08JZ07V23FR99410B3PC945LT28&username=u%40ser&password=9003D1DF22EB4D3820015070385194C8"