<?php

/*
This file is part of Diarusie.

Diarusie is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
    
Diarusie is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Foobar; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if(!defined('IN_DIARY'))
{
    die('Hacking attempt ? :&gt;');
}

function guess_ip()
{
    // Anti IP-Spoofing
    if(strstr(@$_SERVER['HTTP_X_FORWARDED_FOR'], ','))
    {
        $ip_table = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['HTTP_X_FORWARDED_FOR'] = trim($ip_table[count($ip_table) - 1]);
    }

    if(@$_SERVER['HTTP_CLIENT_IP'])
    {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    else if(@$_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR'] != 'unknown')
    {
        // Check IP class
        if(preg_match("/^(10+\.[0-9]+\.[0-9]+\.[0-9]+)/", $_SERVER['HTTP_X_FORWARDED_FOR'])
	    || preg_match("/^(127+\.0+\.0+\.1+)/", $_SERVER['HTTP_X_FORWARDED_FOR'])
	    || preg_match("/^(172+\.16+\.[0-9]+\.[0-9]+)/", $_SERVER['HTTP_X_FORWARDED_FOR'])
	    || preg_match("/^(192+\.168+\.[0-9]+\.[0-9]+)/", $_SERVER['HTTP_X_FORWARDED_FOR'])
	    || preg_match("/^(224+\.[0-9]+\.[0-9]+\.[0-9]+)/", $_SERVER['HTTP_X_FORWARDED_FOR'])
	    || preg_match("/^(240+\.[0-9]+\.[0-9]+\.[0-9]+)/", $_SERVER['HTTP_X_FORWARDED_FOR']))
	{
	    return $_SERVER['REMOTE_ADDR'];
	}
	else
	{
	    return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
    }
    else
    {
	return $_SERVER['REMOTE_ADDR'];
    }
}

function encode_ip($ipv4)
{
    $ip_array = explode('.', $ipv4);

    return sprintf('%02x%02x%02x%02x', $ip_array[0], $ip_array[1], $ip_array[2], $ip_array[3]);
}

function decode_ip($encoded_ipv4)
{
    $hex_ip = explode('.', chunk_split($encoded_ipv4, 2, '.'));

    return hexdec($hex_ip[0]). '.' . hexdec($hex_ip[1]) . '.' . hexdec($hex_ip[2]) . '.' . hexdec($hex_ip[3]);
}

function connect_db()
{
    if($conn = @pg_connect("host=" . SQL_HOST . " port=" . SQL_PORT . " dbname=" . SQL_DATABASE . " user=" . SQL_USERNAME . " password=" . SQL_PASSWORD))
    {
	pg_set_client_encoding($conn, 'UNICODE');
	return $conn;
    }
    else
    {
	echo "Last Error: " . pg_last_error($conn);
        return false;
    }
}

function get_diary_login()
{
    return chop(substr($_SERVER['SERVER_NAME'], 0, strpos($_SERVER['SERVER_NAME'], '.' . DIARY_DOMAIN)));
}

function get_diary_uid()
{
    $sql = "SELECT uid FROM " . TABLE_USERS . " WHERE login='" . get_diary_login() . "'";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    return pg_fetch_object($result, NULL)->uid;
}

function assign_vars($target, $vars_array)
{
    while(list($key, $val) = each($vars_array))
    {
	$target = str_replace($key, $val, $target);
    }

    return $target;
}

?>
