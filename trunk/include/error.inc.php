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

function show_error_page($dbconn, $diary_login, $message)
{
    $sql = "SELECT u.uid, s.page_error FROM " . TABLE_SETTINGS . " AS s, " . TABLE_USERS . " AS u WHERE u.login='" . $diary_login . "' AND s.uid=u.uid LIMIT 1";
    $result = pg_query($sql) or die(pg_last_error($dbconn));
    $data = pg_fetch_object($result, NULL);
    pg_free_result($result);

    echo assign_vars($data->page_error, array(
		    '{login}' => $diary_login,
		    '{errormessage}' => $message
		    ));
}

?>
