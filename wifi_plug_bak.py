# import json
# import sys
# from subprocess import call
from flask import Flask
app = Flask(__name__)

#
#
#
# def switch(switch_key, state):
#     if switch_key not in json_data.keys():
#         print "Key not found in config json"
#     else:
#         switch_cfg = json_data[switch_key]
#         mac = switch_cfg['mac']
#         code = switch_cfg['code']
#         rfslave = None
#         if 'rfslave' in switch_cfg.keys():
#             rfslave = switch_cfg['rfslave']
#
#         call_array = []
#         call_array.append("php")
#         call_array.append("wifi_cmd.php")
#         call_array.append(mac)
#         call_array.append(code)
#         call_array.append(state)
#
#         print "Mac  " + mac
#         print "Code " + code
#         if rfslave is not None:
#             print "Rfslave " + rfslave
#             call_array.append(rfslave)
#
#         print call_array
#         # call(call_array)

@app.route('/')
def index():
    return "Medion/Lidl Wifi Switch Control"

# @app.route('/switch_activate/<int:switch_name>', methods=['GET'])
# def activate_switch(switch_name):
#     # switch(switch_name, 1)
#     print "Activate " + switch_name
#
# @app.route('/switch_deactivate/<string:switch_name>', methods=['GET'])
# def deactivate_switch(switch_name):
#     # switch(switch_name, 0)
#     print "De-Activate " + switch_name
#
#####
if __name__ == "__main__":
    # with open('config.json') as jsonfile:
    #     json_data = json.load(jsonfile)
    #
    # if not json_data:
    #     sys.exit()


    app.run(debug=true)
