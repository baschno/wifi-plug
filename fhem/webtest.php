<?php

$BroadcastIP = '255.255.255.255';


function encodePacket($packet) {
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    $key = '0123456789abcdef';
    mcrypt_generic_init($td, $key, $key);
    $result = mcrypt_generic($td, $packet);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $result;
}


	//<!-- http://localhost/web2.php?action=switch&state=1&mac=ACCF2338AEF6&code=C1117150&rfslave=1C3966 -->
$state = 1;
$mac = "ACCF2338AEF6";
$code = "C1117150";
$rfslave = "1C3966";

$start = "0140{$mac}10";
echo $start;
$msg = hex2bin($start);

if (isset($rfslave)) {
	$value = ($state == 1) ? '60' : '70';
  $before_encode = "00ffff".$code."08".$rfslave.$value."04040404";
	echo $before_encode;
	$msg .= encodePacket(hex2bin($before_encode));
	// $msg .= encodePacket(hex2bin("00ffff{$code}08{$rfslave}{$value}04040404"));
} else {
	$value = ($state == 1) ? 'ff' : '00';
	$before_encode= "00ffff".$code."010000".$value."ff04040404";
	$msg .= encodePacket(hex2bin($before_encode));
}

$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
for ($i = 0; $i < 4; $i++) {
	socket_sendto($sock, $msg, strlen($msg), 0, $BroadcastIP, 8530);
	usleep(50 * 1000);  // 50 ms
}
socket_close($sock);

echo $msg;
exit;
?>
