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

/*
function get_note($dbconn, $diary_login, $format, $nid)
{
    $sql = "SELECT u.uid, n.* FROM " . TABLE_USERS . " AS u, " . TABLE_NOTES . " AS n WHERE u.login='" . $diary_login . "' AND n.uid=u.uid AND n.nid='" . $nid . "' LIMIT 1";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $data = pg_fetch_object($result, NULL);
    pg_free_result($result);

    return assign_vars($format, array(
			    '{subject}' => $data->subject,
			    '{contents}' => $data->contents,
			    '{date}' => date("d-m-Y", $data->timestamp),
			    '{time}' => date("H:i:s", $data->timestamp),
			    ));
}
*/

function get_comments($dbconn, $diary_login, $format, $nid)
{
    $comments = '';

    $sql = "SELECT u.uid, c.* FROM " . TABLE_USERS . " AS u, " . TABLE_COMMENTS . " AS c WHERE u.login='" . $diary_login . "' AND c.uid=u.uid AND nid='" . $nid . "' ORDER BY c.timestamp DESC";
    $result = pg_query($sql) or die(pg_last_error($dbconn));

    if(pg_num_rows($result) == 0)
    {
	return '';
    }

    while($data = pg_fetch_object($result, NULL))
    {
	$comments .= assign_vars($format, array(
				    '{author}' => $data->author,
				    '{email}' => $data->email,
				    '{date}' => date("d-m-Y", $data->timestamp),
				    '{time}' => date("H:i:s", $data->timestamp),
//				    '{ipaddress}' => $data->ipaddress,
				    '{contents}' => $data->contents,
				    ));
    }

    pg_free_result($result);

    return $comments;
}

function show_comments_page($dbconn, $diary_login)
{
    $nid = (int)$_GET['nid'];

    // Get Note
    $sql = "SELECT u.uid, n.* FROM " . TABLE_USERS . " AS u, " . TABLE_NOTES . " AS n WHERE u.login='" . $diary_login . "' AND n.uid=u.uid AND n.nid='" . $nid . "' LIMIT 1";
    $result = pg_query($sql) or die(pg_last_error($dbconn));

    if(pg_num_rows($result) == 0)
    {
	show_error_page($dbconn, $diary_login, "no such note!");
	return;
    }
    else
    {
	$datanote = pg_fetch_object($result, NULL);
	pg_free_result($result);

	$sql = "SELECT u.uid, s.format_note, s.page_comments, s.format_comment FROM " . TABLE_SETTINGS . " AS s, " . TABLE_USERS . " AS u WHERE u.login='" . $diary_login . "' AND s.uid=u.uid LIMIT 1";
	$result = pg_query($sql) or die(pg_last_error($dbconn));
	$dataformat = pg_fetch_object($result, NULL);

        // Strip link to add comment and number of comments
	$dataformat->format_note = preg_replace('/(<a.* href="{hrefcomment}")(.+)(.*>)/', '', $dataformat->format_note);
	$dataformat->format_note = preg_replace('/(.?{commentscount}.?)/', '', $dataformat->format_note);

	$note = assign_vars($dataformat->format_note, array(
			    '{subject}' => $datanote->subject,
			    '{contents}' => $datanote->contents,
			    '{date}' => date("d-m-Y", $datanote->timestamp),
			    '{time}' => date("H:i:s", $datanote->timestamp),
			    ));

	$comments = get_comments($dbconn, $diary_login, $dataformat->format_comment, $nid);

	$sql = "SELECT COUNT(c.*) AS commentscount FROM " . TABLE_USERS . " AS u, " . TABLE_COMMENTS . " AS c WHERE c.nid='" . $nid . "' AND c.uid=u.uid AND u.login='" . $diary_login . "'";
	$result = pg_query($sql) or die(pg_last_error($dbconn));
	$datacount = pg_fetch_object($result, NULL);
	pg_free_result($result);
	$commentscount = (int)$datacount->commentscount;

	echo assign_vars($dataformat->page_comments, array(
			'{login}' => $diary_login,
			'{note}' => $note,
			'{comments}' => $comments,
			'{commentscount}' => $commentscount,
			'{hrefcommentadd}' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_COMMENTS . '&nid=' . $nid . '&action=add'
			));

    }
}

function show_comment_add_page($dbconn, $diary_login)
{
    $nid = (int)$_GET['nid'];

    $sql = "SELECT u.uid, s.page_comment_add FROM " . TABLE_USERS . " AS u, " . TABLE_SETTINGS . " AS s WHERE s.uid=u.uid AND u.login='" . $diary_login . "' LIMIT 1";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $data = pg_fetch_object($result, NULL);
    echo assign_vars($data->page_comment_add, array(
		    '{login}' => $diary_login,
		    '{hrefcomment}' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_COMMENTS . '&nid=' . $nid,
		    '{nid}' => $nid
		    ));
}

function add_comment_contents($dbconn, $diary_uid)
{
    echo $sql = "INSERT INTO comments (uid, nid, author, email, webpage, ipaddress, contents, timestamp)
		VALUES ('" . $diary_uid . "', '" . (int)$_POST['nid'] . "', '" . addslashes($_POST['author'])
		 . "', '" . $_POST['email'] . "', '" . $_POST['webpage'] . "', '" . $_SESSION['ip']
		 . "', '" . addslashes($_POST['contents']) . "', '" . time() . "')";
    pg_query($sql) or die(pg_last_error($dbconn));
}

?>
