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

function get_last_notes($dbconn, $diary_login, $format)
{
    $notes = '';
    $tmp = $format;

    $sql = "SELECT u.uid, n.* FROM " . TABLE_USERS . " AS u, " . TABLE_NOTES . " AS n WHERE n.uid=u.uid AND u.login='" . $diary_login . "' ORDER BY timestamp DESC LIMIT 5";
    $result = pg_query($sql) or die(pg_last_error($dbconn));

    if(pg_num_rows($result) == 0)
    {
	return "Still empty here";
//	show_error_page($dbconn, $diary_login, "Still empty here !");
//	return ;
    }

    while($data = pg_fetch_object($result, NULL))
    {
	$sql2 = "SELECT u.uid, COUNT(c.*) AS count FROM " . TABLE_USERS . " AS u, " . TABLE_COMMENTS . " AS c WHERE u.login='" . $diary_login . "' AND c.uid=u.uid AND c.nid='" . $data->nid . "' GROUP BY u.uid";
	$result2 = pg_query($sql2) or die(pg_last_error($dbconn));
	$data2 = pg_fetch_object($result2, NULL);

	$notes .= assign_vars($format, array(
			    '{subject}' => '<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_ARCHIVE . '&nid=' . $data->nid . '">' . $data->subject . '</a>',
			    '{contents}' => $data->contents,
			    '{date}' => date("d-m-Y", $data->timestamp),
			    '{time}' => date("H:i:s", $data->timestamp),
			    '{hrefcomment}' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_COMMENTS . '&nid=' . $data->nid,
			    '{commentscount}' => @(int)$data2->count
			    ));
	pg_free_result($result2);
    }
    pg_free_result($result);

    return $notes;
}

function get_archive($dbconn, $diary_login)
{
    $year = '';
    $archive = '';

    $sql = "SELECT DISTINCT n.year, n.month FROM " . TABLE_USERS. " AS u, " . TABLE_NOTES . " AS n WHERE n.uid=u.uid AND u.login='" . $diary_login . "' ORDER BY  n.year DESC, n.month DESC";
    $result = pg_query($sql) or die(pg_last_error($dbconn));

    while($data = pg_fetch_object($result, NULL))
    {
	if($data->year != $year)
	{
	    $year = $data->year;
	    $archive .= $year . '<br>';
	}
	$archive .= '<a href="http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_ARCHIVE .	'&year=' . $data->year . '&month=' . $data->month . '">' . $data->month . '</a><br>';
    }

    return $archive;
}

function get_links($dbconn, $diary_login)
{
    $category = '';
    $links = '';

    $sql = "SELECT u.uid, l.name, l.description, l.address, lc.name AS lcname FROM " . TABLE_USERS . " AS u, " . TABLE_LINKS . " AS l, " . TABLE_LINKS_CATEGORIES . " AS lc WHERE lc.uid=u.uid AND l.uid=u.uid AND u.login='" . $diary_login . "' AND l.lcid IN (SELECT DISTINCT lcid FROM " . TABLE_LINKS_CATEGORIES . " ORDER BY lcid DESC) AND l.lcid = lc.lcid ORDER BY l.lcid DESC";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    while($data = pg_fetch_object($result, NULL))
    {
	if($data->lcname != $category)
	{
	    $category = $data->lcname;
	    $links .= $category . '<br>';
	}
	$links .= '<a href="' . $data->address . '">' . $data->name . '</a>' . $data->description . '<br>';
    }

    return $links;
}

function show_main_page($dbconn, $diary_login)
{
    $sql = "SELECT u.uid, s.page_main, s.format_note FROM " . TABLE_SETTINGS . " AS s, " . TABLE_USERS . " AS u WHERE u.login='" . $diary_login . "' AND s.uid=u.uid LIMIT 1";
    $result = pg_query($dbconn, $sql) or die(pg_last_error($dbconn));
    $data = pg_fetch_object($result, NULL);

    echo assign_vars($data->page_main, array(
		    '{login}' => $diary_login,
		    '{diary}' => get_last_notes($dbconn, $diary_login, $data->format_note),
		    '{hrefguestbook}' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAGE_GUESTBOOK,
		    '{archive}' => get_archive($dbconn, $diary_login),
		    '{links}' => get_links($dbconn, $diary_login)
		    ));
}

?>
