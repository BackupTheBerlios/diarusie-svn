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

//echo preg_match("/^(http:\/\/)[a-zA-Z-0-9-]diary.int.pl$/i", $_SERVER["HTTP_REFERER"]);

if(!empty($_POST) && !empty($_SERVER['HTTP_REFERER']) &&
    strpos($_SERVER['HTTP_REFERER'], "http://" . $_SERVER['SERVER_NAME']) === false)
{
        die("Get lost :&gt;");
}

session_start();

define('IN_DIARY', true);
define('ROOT_PATH', realpath('./') . '/');
define('INCLUDE_PATH', realpath(ROOT_PATH . '../include') . '/');

setlocale(LC_ALL, '');
bindtextdomain("diarusie", INCLUDE_PATH . 'locale/');
textdomain("diarusie");
bind_textdomain_codeset("diarusie", 'UTF-8');

//echo _("Welcome!");

@include_once(ROOT_PATH . 'extension.inc');
@include_once(INCLUDE_PATH . 'common.inc.' . PHP_EXT);

$dbconn = connect_db() or die("ups db!");

$diary_login = get_diary_login();

if(@!$_SESSION['ip'])
{
    $_SESSION['ip'] = guess_ip();
}

switch(@$_GET['page'])
{
    case 'archive':
	include(INCLUDE_PATH . 'archive.inc.' . PHP_EXT);
	if(@$_GET['nid'] >= 1)
	{
	    show_archive_page($dbconn, $diary_login, SHOW_NOTE);
	}
	else if(@$_GET['month'] >= 1 && @$_GET['month'] <= 12)
	{
	    show_archive_page($dbconn, $diary_login, SHOW_MONTH);
	}
	else
	{
	    show_error_page($dbconn, $diary_login, "turlaj dropsy petaku!");
	}
	break;
    case 'comments':
	include(INCLUDE_PATH . 'comments.inc.' . PHP_EXT);
	switch(@$_GET['action'])
	{
	    case 'add':
		show_comment_add_page($dbconn, $diary_login);
		break;
	    default:
		if(!empty($_POST))
		{
		    if(!$_POST['author'] || !$_POST['contents'])
		    {
			header("Location: http://" . $_SERVER['SERVER_NAME'] . "/" . PAGE_COMMENTS . "&nid=" . $_POST['nid'] . "&action=add");
		    }
		    else
		    {
			add_comment_contents($dbconn, get_diary_uid());
		    }
		}
		show_comments_page($dbconn, $diary_login);
	}
	break;
    case 'guestbook':
	include(INCLUDE_PATH . 'guestbook.inc.' . PHP_EXT);
	switch(@$_GET['action'])
	{
	    case 'add':
		show_guestbook_add_page($dbconn, $diary_login);
		break;
	    default:
		if(!empty($_POST))
		{
		    if(!$_POST['author'] || !$_POST['inscription'])
		    {
			header("Location: http://" . $_SERVER['SERVER_NAME'] . "/" . PAGE_GUESTBOOK . "&action=add");
		    }
		    else
		    {
			add_guestbook_inscription($dbconn, get_diary_uid());
		    }
		}
		show_guestbook_page($dbconn, $diary_login);
	}
	break;
    default:
	include(INCLUDE_PATH . 'index.inc.' . PHP_EXT);
	show_main_page($dbconn, $diary_login);
}

pg_close($dbconn);

?>
