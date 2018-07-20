<?php
/* Web frontend for querying registered WiFi plugs and sending switching commands
 * (c) 2016 SebiM. Contact via forum on forum.fhem.de
 *
 * Testet on PHP 5.6, needs Bootstrap in the same directory (tested with 3.3.7)
 *
 * Version: 2016-12-03 15:21h

 */
if ($argc <=1) {
	echo "Usage: $argv[0] <state> <rfslave>\n";
	exit(1);
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

$BroadcastIP = '255.255.255.255';
$mac = $argv[1];
$code =$argv[2];
$state = $argv[3];
if ($argc == 5)
	$rfslave = $argv[4];

echo("Mac: " . $mac . "\n");
$msg = hex2bin("0140{$mac}10");
echo("hex2bin: " . $msg . "\n");
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
exit;

?>
