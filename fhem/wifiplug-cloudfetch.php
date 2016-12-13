#!/usr/bin/php
<?php
// Request device data for Aldi & Lidl WiFi & RF plugs from cloud
// 2016 SebiM, can be considered as public domain

if ($argc != 3) {
	echo "Usage: $argv[0] <Username> <Password>\n";
	exit(1);
}

function getJSON($path) {
	global $argv;
	$accessKey = 'Q763W08JZ07V23FR99410B3PC945LT28';
	$username = urlencode($argv[1]);
	$password = strtoupper(md5($argv[2]));
	$URLPrefix = 'http://smart2connect.yunext.com';

	$f = file_get_contents("$URLPrefix$path?accessKey=$accessKey&username=$username&password=$password");
	/* Testing stuff...
	$lpath = str_replace('/', '_', $path);
	file_put_contents($lpath, $f);
	$f = file_get_contents($lpath);
	 */
	return json_decode($f);
}

function cmpOrderNumber($a, $b) {
    return ($a->orderNumber < $b->orderNumber) ? -1 : 1;
}

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

echo json_encode($plugs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
