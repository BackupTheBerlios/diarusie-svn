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

function get_note($dbconn, $diary_login, $format, $nid)
{
    $sql = "SELECT u.uid, n.* FROM " . TABLE_USERS . " AS u, " . TABLE_NOTES . " AS n WHERE u.login='" . $diary_login . "' AND n.uid=u.uid AND n.nid='" . $nid . "' LIMIT 1";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $data = pg_fetch_object($result, NULL);

    if(pg_num_rows($result) == 0)
    {
	show_error_page($dbconn, $diary_login, "no such note !!!");
	return;
    }

    pg_free_result($result);

    $sql = "SELECT COUNT(*) AS commentscount FROM " . TABLE_USERS . " AS u, " . TABLE_COMMENTS . " AS c WHERE u.login='" . $diary_login . "' AND c.uid=u.uid AND c.nid='" . $nid . "'";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $count = pg_fetch_object($result, NULL);
    pg_free_result($result);
    $commentscount = (int)$count->commentscount;

    return assign_vars($format, array(
			    '{subject}' => $data->subject,
			    '{contents}' => $data->contents,
			    '{date}' => date("d-m-Y", $data->timestamp),
			    '{time}' => date("H:i:s", $data->timestamp),
			    '{hrefcomment}' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_COMMENTS . '&nid=' . $data->nid,
			    '{commentscount}' => $commentscount
			    ));
}

function get_archive_notes($dbconn, $diary_login, $format, $month, $year)
{
    $notes = '';
    $tmp = $format;

    $sql = "SELECT u.uid, n.* FROM " . TABLE_USERS . " AS u, " . TABLE_NOTES . " AS n WHERE u.login='" . $diary_login . "' AND n.uid=u.uid AND n.year='" . $year . "' AND n.month='" . $month . "'";
    $result = pg_query($sql) or die(pg_last_error($dbconn));

    if(pg_num_rows($result) == 0)
    {
	show_error_page($dbconn, $diary_login, "brak notek z tego okresu!");
	return;
    }

//    pg_free_result($result);

    while($data = pg_fetch_object($result, NULL))
    {
	$sql2 = "SELECT COUNT(*) AS commentscount FROM " . TABLE_USERS . " AS u, " . TABLE_COMMENTS . " AS c WHERE u.login='" . $diary_login . "' AND c.uid=u.uid AND c.nid='" . $data->nid . "'";
	$result2 = pg_query($sql2) or die(pg_last_error($dbconn));
	$data2 = pg_fetch_object($result2, NULL);

	$notes .= assign_vars($format, array(
			    '{subject}' => '<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_ARCHIVE . '&nid=' . $data->nid . '">' . $data->subject . '</a>',
			    '{contents}' => $data->contents,
			    '{date}' => date("d-m-Y", $data->timestamp),
			    '{time}' => date("H:i:s", $data->timestamp),
			    '{hrefcomment}' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_COMMENTS . '&nid=' . $data->nid,
			    '{commentscount}' => (int)$data2->commentscount
			    ));
	pg_free_result($result2);
    }

    return $notes;
}

function show_archive_page($dbconn, $diary_login, $mode)
{
    $nid = (empty($_GET['nid']) ? NULL : (int)$_GET['nid']);
    $month = (empty($_GET['month']) ? NULL : (int)$_GET['month']);
    $year = (empty($_GET['year']) ? NULL : (int)$_GET['year']);

    $sql = "SELECT u.uid, s.page_archive, s.format_note FROM " . TABLE_SETTINGS . " AS s, " . TABLE_USERS . " AS u WHERE u.login='" . $diary_login . "' AND s.uid=u.uid LIMIT 1";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $settings = pg_fetch_object($result, NULL);

    switch($mode)
    {
	case SHOW_NOTE:
	    $sql2 = "SELECT COUNT(*) AS commentscount FROM " . TABLE_COMMENTS . " AS c WHERE c.uid='1' AND c.nid='" . $nid . "'";
	    $result2 = pg_query($sql2) or die(pg_last_error($dbconn));
	    $data2 = pg_fetch_object($result2, NULL);

	    echo assign_vars($settings->page_archive, array(
			    '{login}' => $diary_login,
			    '{archive}' => get_note($dbconn, $diary_login, $settings->format_note, $nid),
			    '{commentscount}' => $data2->commentscount
			    ));
	    break;
	case SHOW_MONTH:
	    $sql = "SELECT * FROM notes";
	    $result = pg_query($sql) or die(pg_last_error($dbconn));
	    $data = pg_fetch_object($result, NULL);

	    echo assign_vars($settings->page_archive, array(
			    '{login}' => $diary_login,
			    '{archive}' => get_archive_notes($dbconn, $diary_login, $settings->format_note, $month, $year),
			    ));
	    break;
    }
}

?>
