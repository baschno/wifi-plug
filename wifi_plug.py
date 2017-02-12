import json
import sys
from subprocess import call

if len(sys.argv) < 3:
    print "Missing parameter switch name and/or name"
    sys.exit()

switch_key = sys.argv[1]
state = sys.argv[2]

with open('config.json') as jsonfile:
    json_data = json.load(jsonfile)

if not json_data:
    sys.exit()

if switch_key not in json_data.keys():
    print "Key not found in config json"
    sys.exit()

switch_cfg = json_data[switch_key]
mac = switch_cfg['mac']
code = switch_cfg['code']
rfslave = None
if 'rfslave' in switch_cfg.keys():
    rfslave = switch_cfg['rfslave']

call_array = []
call_array.append("php")
call_array.append("wifi_cmd.php")
call_array.append(mac)
call_array.append(code)
call_array.append(state)

print "Mac  " + mac
print "Code " + code
if rfslave is not None:
    print "Rfslave " + rfslave
    call_array.append(rfslave)

print call_array
call(call_array)
