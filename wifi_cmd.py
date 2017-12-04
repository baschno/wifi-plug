import binascii
import string
import rijndael
import base64

KEY_SIZE = 16
BLOCK_SIZE = 32

def bintohex(data):
    return binascii.hexlify(data)

def encode_packet(plaintext):
    key = '0123456789abcdef'

    padded_key = key.ljust(KEY_SIZE, '\0')
    padded_text = plaintext + (BLOCK_SIZE - len(plaintext) % BLOCK_SIZE) * '\0'


    r = rijndael.rijndael(padded_key, BLOCK_SIZE)

    ciphertext = ''
    for start in range(0, len(padded_text), BLOCK_SIZE):
        ciphertext += r.encrypt(padded_text[start:start+BLOCK_SIZE])

    encoded = base64.b64encode(ciphertext)

    return encoded

"""
function encodePacket($packet) {
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    $key = '0123456789abcdef';
    mcrypt_generic_init($td, $key, $key);
    $result = mcrypt_generic($td, $packet);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $result;
}
"""