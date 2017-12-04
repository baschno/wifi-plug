import pytest
import wifi_cmd


def test_bin2hex():
    assert "6162636431323334" == wifi_cmd.bintohex('abcd1234')

def test_encode_packet():
    assert "4d7c6f9d30cc82ca09fc38c2d10bf052" == wifi_cmd.encode_packet("abcd1234")



