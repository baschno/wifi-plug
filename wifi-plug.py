#!/usr/bin/env python3

import json
import urllib.parse
import os
from pprint import pprint
import hashlib
import rijndael
import base64

KEY_SIZE = 16
BLOCK_SIZE = 32

accessKey = 'Q763W08JZ07V23FR99410B3PC945LT28'
username = urllib.parse.quote(os.environ.get('ICOMEN_USERNAME'))
password = hashlib.md5(os.environ.get('ICOMEN_PASSWD').encode('utf-8')).hexdigest()
print(username)
print(password)
URLPrefix = 'http://smart2connect.yunext.com'

with open('fhem/config.json') as data_file:
    data = json.load(data_file)

# pprint(data)
pprint(data['list'][1]['addressCode'])

def encrypt(key, plaintext):
    padded_key = key.ljust(KEY_SIZE, '\0')
    padded_text = plaintext + (BLOCK_SIZE - len(plaintext) % BLOCK_SIZE) * '\0'

    # could also be one of
    #if len(plaintext) % BLOCK_SIZE != 0:
    #    padded_text = plaintext.ljust((len(plaintext) / BLOCK_SIZE) + 1 * BLOCKSIZE), '\0')
    # -OR-
    #padded_text = plaintext.ljust((len(plaintext) + (BLOCK_SIZE - len(plaintext) % BLOCK_SIZE)), '\0')

    r = rijndael.rijndael(padded_key, BLOCK_SIZE)

    ciphertext = ''
    for start in range(0, len(padded_text), BLOCK_SIZE):
        ciphertext += r.encrypt(padded_text[start:start+BLOCK_SIZE])

    encoded = base64.b64encode(ciphertext)

    return encoded

# Ein S2
# [Tue Dec 13 23:25:03.735004 2016] [:error] [pid 27320] [client 127.0.0.1:44616] \x01@\xac\xcf#8\xae\xf6\x10\xd6(\xd6\xf7\xa7\x97*\x142\x01K\xc7\x94\xcc\xae\xfb, referer: http://localhost/web2.php
#
# Aus S2
# [Tue Dec 13 23:26:04.081929 2016] [:error] [pid 27129] [client 127.0.0.1:44632] \x01@\xac\xcf#8\xae\xf6\x10\xb8\x11\x06D\xc9\x18\xa6s\x10\x98\x93\xa4NP\xc14, referer: http://localhost/web2.php
#
# \x01@\xac\xcf#8\xae\xf6\x10\xd6(\xd6\xf7\xa7\x97*\x142\x01K\xc7\x94\xcc\xae\xfb
# \x01@\xac\xcf#8\xae\xf6\x10\xb8\x11\x06D\xc9\x18\xa6s\x10\x98\x93\xa4NP\xc14
#http://stackoverflow.com/questions/8217269/decrypting-strings-in-python-that-were-encrypted-with-mcrypt-rijndael-256-in-php

# function encodePacket($packet) {
#     $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
#     $key = '0123456789abcdef';
#     mcrypt_generic_init($td, $key, $key);
#     $result = mcrypt_generic($td, $packet);
#     mcrypt_generic_deinit($td);
#     mcrypt_module_close($td);
#     return $result;
# }
