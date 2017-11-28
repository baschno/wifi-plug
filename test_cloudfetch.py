import pytest
import json
import cloudfetch


def test_te():
    test_data = json.loads('{"list": [{"deviceName": "Bodenlampe Flur", "macAddress": "ACCF2338AEF6", "orderNumber": 1, "authCode": "7150", "imageName": "1.png", "deviceType": "11", "companyCode": "C1", "lastOperation": 1475085114427}], "success": true}')
    for item in test_data:
        assert "Bodenlampe Flur" == cloudfetch.get_device_name_from_item(item)
        assert "ACCF2338AEF6" == get_mac_address_from_item(item)
