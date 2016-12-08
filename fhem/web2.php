<?php
/* Web frontend for querying registered WiFi plugs and sending switching commands
 * (c) 2016 SebiM. Contact via forum on forum.fhem.de
 *
 * Testet on PHP 5.6, needs Bootstrap in the same directory (tested with 3.3.7)
 *
 * Version: 2016-12-03 15:21h

 */

$BroadcastIP = '255.255.255.255';

function getJSON($path) {
	global $_GET;
	$accessKey = 'Q763W08JZ07V23FR99410B3PC945LT28';
	$username = urlencode($_GET['username']);
	$password = strtoupper(md5($_GET['password']));
	$URLPrefix = 'http://smart2connect.yunext.com';

	$f = file_get_contents("$URLPrefix$path?accessKey=$accessKey&username=$username&password=$password");
//	$lpath = str_replace('/', '_', $path);
//	file_put_contents($lpath, $f);
//	$f = file_get_contents($lpath);

	return json_decode($f);
}

function cmpOrderNumber($a, $b) {
    return ($a->orderNumber < $b->orderNumber) ? -1 : 1;
}

function encodePacket($packet) {
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    $key = '0123456789abcdef';
    mcrypt_generic_init($td, $key, $key);
    $result = mcrypt_generic($td, $packet);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $result;
}


if (isset($_GET['action'])) {
	// AJAX request
	header('Content-Type: application/json');
	if ($_GET['action'] == 'getlist') {
		$plugs = [];
		$WiFiPlugCodesByMAC = [];

		$f = getJSON('/api/device/wifi/list');
		if (isset($f->success) && $f->success == 1) {
			uasort($f->list, 'cmpOrderNumber');
			foreach ($f->list as $plug) {
				$code = $plug->companyCode . $plug->deviceType . $plug->authCode;
				$plugs[$plug->deviceName] = ['mac' => $plug->macAddress, 'code' => $code];
				$WiFiPlugCodesByMAC[$plug->macAddress] = $code;
			}
		}

		$f = getJSON('/api/device/wifi/list');
		if (isset($f->success) && $f->success == 1) {
			uasort($f->list, 'cmpOrderNumber');
			foreach ($f->list as $plug) {
				$plugs[$plug->deviceName] = [
					'mac' => $plug->macAddress,
					'code' => $plug->companyCode . $plug->deviceType . $plug->authCode
				];
			}
		}

		$f = getJSON('/api/device/rf/list');
		if (isset($f->success) && $f->success == 1) {
			uasort($f->list, 'cmpOrderNumber');
			foreach ($f->list as $plug) {
				$plugs[$plug->deviceName] = [
					'mac' => $plug->macAddress,
					'code' => $WiFiPlugCodesByMAC[$plug->macAddress],
					'rfslave' => $plug->addressCode
				];
			}
		}

		$result = [];
		foreach ($plugs as $name => $plug) {
			$result[] = array_merge(['name' => $name], $plug);
		}
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		exit;
	} else if ($_GET['action'] == 'switch') {
		$state = $_GET['state'];
		$mac = $_GET['mac'];
		$code = $_GET['code'];
		if (isset($_GET['rfslave'])) {
			$rfslave = $_GET['rfslave'];
		}

		$msg = hex2bin("0140{$mac}10");

		if (isset($rfslave)) {
			$value = ($state == 1) ? '60' : '70';
			$msg .= encodePacket(hex2bin("00ffff{$code}08{$rfslave}{$value}04040404"));
		} else {
			$value = ($state == 1) ? 'ff' : '00';
			$msg .= encodePacket(hex2bin("00ffff{$code}010000{$value}ff04040404"));
		}

		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
		for ($i = 0; $i < 4; $i++) {
			socket_sendto($sock, $msg, strlen($msg), 0, $BroadcastIP, 8530);
			usleep(50 * 1000);  // 50 ms
		}
		socket_close($sock);

		echo json_encode(['success' => 1]);
		exit;
	}
	echo json_encode(['error' => 1]);
	exit;
}
?><!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>WiFi-Steckdosen</title>

		<!-- Bootstrap -->
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<script>
// Settings
var cloudPrefix = 'http://smart2connect.yunext.com/';
var accessKey = 'Q763W08JZ07V23FR99410B3PC945LT28';

// Helpers
function getJSON(url, onSuccess, onError) {
	var xhr = typeof XMLHttpRequest != 'undefined' ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.open('get', url, true);
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) { // Done
			if (xhr.status == 200) { // OK
				if (typeof onSuccess !== 'undefined') {
					onSuccess(JSON.parse(xhr.responseText));
				}
			} else if (typeof onError !== 'undefined') {
				onError(xhr.status);
			}
		}
	};
	xhr.send();
};

var plugs;

function doSwitch(index, state) {
	var plug = plugs[index];
	var url = '?action=switch&state=' + state + '&mac=' + plug.mac + '&code=' + plug.code;
	if (typeof plug.rfslave != 'undefined') {
		url = url + '&rfslave=' + plug.rfslave;
	}
	getJSON(url);
}

function showPlugsTable() {
	document.getElementById('LoginForm').style.display = 'none';
	var table = document.getElementById('PlugsTable');
	for (var i = 0; i < plugs.length; i++) {
		var tr = document.createElement('tr');
		var td = document.createElement('td');
		td.textContent = plugs[i].name;
		tr.appendChild(td);
		td = document.createElement('td');
		var button = document.createElement('a');
		button.setAttribute('type', 'button');
		button.setAttribute('class', 'btn btn-sm btn-success');
		button.setAttribute('onClick', 'doSwitch(' + i + ', 1)');
		button.textContent = 'Einschalten';
		td.appendChild(button);
		td.appendChild(document.createTextNode(' '));
		button = document.createElement('a');
		button.setAttribute('type', 'button');
		button.setAttribute('class', 'btn btn-sm btn-danger');
		button.setAttribute('onClick', 'doSwitch(' + i + ', 0)');
		button.textContent = 'Ausschalten';
		td.appendChild(button);
		tr.appendChild(td);
		table.appendChild(tr);
	}
	document.getElementById('PlugsBlock').style.display = 'block';
}

function doLogin() {
	var username = document.getElementById('username').value;
	var password = document.getElementById('password').value;

	getJSON('?action=getlist&username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password),
		function (data) {
			localStorage.setItem('plugs', JSON.stringify(data));
			plugs = data;
			showPlugsTable();
		},
		function (status) {
			var error = document.getElementById('ErrorBlock');
			error.style.display = 'block';
			error.textContent = 'Es ist ein Fehler aufgetreten, Status-Code ' . status;
		}
	);
};

function reset() {
	localStorage.removeItem('plugs');
	location.reload();
}

window.onload = function () {
	var data = localStorage.getItem('plugs');
	if (data) {
		data = JSON.parse(data);
		if (data.length > 0) {
			plugs = data;
			showPlugsTable();
		}
	}
}
		</script>
	</head>
	<body>
		<div class="container">
			<h1>WiFi-Steckdosen Aldi/Lidl/Medion etc.</h1>
			<form id="LoginForm" class="form-inline">
				<div class="alert alert-danger" role="alert">Es sind noch keine Geräte-Informationen vorhanden. (Hinweis: Diese werden nach einmaligem Abrufen im Browser gespeichert.)</div>
				<p>Bitte die Anmeldedaten entsprechend der App eintragen.</p>
				<div class="form-group">
					<label for="username">Benutzername (E-Mail):</label>
					<input id="username" type="text" class="form-control">
				</div>
				<div class="form-group">
					<label for="password">Kennwort:</label>
					<input id="password" type="password" class="form-control">
				</div>
				<a class="btn btn-default btn-primary" onclick="doLogin()">Gerätedaten abrufen</a>
			</form>
			<div id="ErrorBlock" style="display: none" class="alert alert-danger" role="alert">Fehler</div>
			<div id="PlugsBlock" style="display: none">
				<p><a class="btn btn-default btn-warning" onClick="reset()">Liste neu laden (zurücksetzen)</a></p>
				<table class="table table-condensed table-hover">
					<tbody id="PlugsTable"></tbody>
				</table>
			</div>
		</div>
	</body>
</html>
