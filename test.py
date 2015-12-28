#!/usr/bin/env python3

import socket
import time

mac = 'AC CF 23 38 AE F6'
#mac = 'AC CF 23 34 0E 3C'
on  = '10 4C F7 5F 5A 28 A1 81 57 4A C1 B5 63 CD 51 A7 8D'
off = '10 F7 B4 E7 4B 97 0D 96 F3 CA 2B B5 D3 CD 1C 19 D0'
ip = '127.0.0.1'
# // ip = '192.168.178.27'

with open('udpdata.txt') as f:
    lines = f.readlines()

for line in lines:
    print(line)
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM, 0)
    s.connect((ip, 8530)) # (IP, Port) is a single variable passed to connect function
    bytes = bytearray.fromhex(line)
    s.send(bytes) # to switch 'on'
    s.send(bytes) # to switch 'on'
    time.sleep(5)
