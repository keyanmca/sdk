<?php


//Start the string with the content path to the streaming asset (live stream name or VOD file name). The content path is the part of the URL that starts with the application name (excluding the '/' that precedes the application name) and continues through to the end of the stream name or file name. Be sure to exclude all HTTP request keywords after the stream name or file name (for example, /manifest.m3u8, /media.ts, /Manifest, /manifest.f4v, and so on). See the Examples at the end of this section to view sample applications of this rule.

//Append the '?' character to the path that you created in the previous step. This character separates the content path from the public SecureToken query parameters that follow.

//Append the public SecureToken query parameters, shared secret, and client IP address (if applicable) to the '?' character that you created in the previous step. These items MUST be in alphabetical order and separated by the '&' character.


$wowzaContentURL = 'http://192.168.9.20:1935/wakawaka/mp4:myStream_aac/playlist.m3u8';
$wowzaContentPath = 'wakawaka/mp4:myStream_aac';
$wowzaSecureToken = 'mySharedSecret';
$wowzaTokenPrefix = 'wowzatoken';
$wowzaCustomParameter = $wowzaTokenPrefix . "CustomParameter=myParameter";
$wowzaSecureTokenStartTime = $wowzaTokenPrefix  ."starttime=". time() ;
$wowzaSecureTokenEndTime = $wowzaTokenPrefix  ."endtime=". (time() + (7 * 24 * 60 * 60) );
$viewer_ip = '192.168.9.20';

$hashstr = $wowzaContentPath ."?". $viewer_ip ."&". $wowzaSecureToken ."&". $wowzaCustomParameter ."&". $wowzaSecureTokenEndTime ."&". $wowzaSecureTokenStartTime;
$hash = hash('sha256', $hashstr ,1);
$usableHash=strtr(base64_encode($hash), '+/', '-_');
$url = $wowzaContentURL ."?". $wowzaSecureTokenStartTime ."&". $wowzaSecureTokenEndTime ."&". $wowzaCustomParameter ."&".  $wowzaTokenPrefix ."hash=$usableHash";

echo "<pre>";
echo "\nwowzaContentURL = $wowzaContentURL";
echo "\nwowzaContentPath = $wowzaContentPath";
echo "\nwowzaSecureToken = $wowzaSecureToken";
echo "\nwowzaTokenPrefix = $wowzaTokenPrefix";
echo "\nwowzaCustomParameter = $wowzaCustomParameter";
echo "\nwowzaSecureTokenStartTime = $wowzaSecureTokenStartTime";
echo "\nwowzaSecureTokenEndTime = $wowzaSecureTokenEndTime";
echo "<hr>";
echo "\nHash string = $hashstr";
echo "\nHash value = $usableHash";
echo "\nURL = $url";
echo "</pre>"; 



?>

