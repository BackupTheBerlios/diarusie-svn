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

function get_guestbook_inscriptions($dbconn, $diary_login, $format)
{
    $inscriptions = '';

    $sql = "SELECT u.uid, g.* FROM " . TABLE_USERS . " AS u, " . TABLE_GUESTBOOK . " AS g WHERE g.uid=u.uid AND u.login='" . $diary_login . "'";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    while($data = pg_fetch_object($result, NULL))
    {
	$inscriptions .= assign_vars($format, array(
				    '{author}' => $data->author,
				    '{email}' => $data->email,
				    '{webpage}' => $data->webpage,
				    '{inscription}' => $data->inscription,
				    '{date}' => date("d-m-Y", $data->timestamp),
				    '{time}' => date("H:i:s", $data->timestamp),
//				    '{ipaddress}' => $data->ipaddress
				    ));
    }

    return $inscriptions;
}

function show_guestbook_page($dbconn, $diary_login)
{
    $sql = "SELECT u.uid, COUNT(*) AS count FROM " . TABLE_USERS . " AS u, " . TABLE_GUESTBOOK . " AS g WHERE g.uid=u.uid AND u.login='" . $diary_login . "' GROUP BY u.uid";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $data = pg_fetch_object($result, NULL);
    pg_free_result($result);
    $count = $data->count;
    $data = '';
    $sql = "SELECT u.uid, s.page_guestbook, s.format_guestbook_inscription FROM " . TABLE_USERS . " AS u, " . TABLE_SETTINGS . " AS s WHERE s.uid=u.uid AND u.login='" . $diary_login . "' LIMIT 1";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $data = pg_fetch_object($result, NULL);
    echo assign_vars($data->page_guestbook, array(
		    '{login}' => $diary_login,
		    '{inscriptions}' => get_guestbook_inscriptions($dbconn, $diary_login, $data->format_guestbook_inscription),
		    '{guestscount}' => (int)$count,
		    '{hrefguestbookadd}' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_GUESTBOOK . '&action=add'
		    ));
}

function show_guestbook_add_page($dbconn, $diary_login)
{
    $sql = "SELECT u.uid, s.page_guestbook_add FROM " . TABLE_USERS . " AS u, " . TABLE_SETTINGS . " AS s WHERE s.uid=u.uid AND u.login='" . $diary_login . "' LIMIT 1";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $data = pg_fetch_object($result, NULL);
    echo assign_vars($data->page_guestbook_add, array(
		    '{login}' => $diary_login,
		    '{hrefguestbook}' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_GUESTBOOK
		    ));
}

function add_guestbook_inscription($dbconn, $diary_uid)
{
    echo $sql = "INSERT INTO guestbook (uid, author, email, webpage, ipaddress, inscription, timestamp)
	    VALUES ('" . $diary_uid . "', '" . addslashes($_POST['author']) . "', '" . $_POST['email']
	    . "', '" . $_POST['webpage'] . "', '" . $_SESSION['ip'] . "', '" . addslashes($_POST['inscription'])
	    . "', '" . time() . "')";
    pg_query($sql) or die(pg_last_error($dbconn));
}

?>
