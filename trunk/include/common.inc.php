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

//
// Enforce PHP enviroment :-)
//
ini_set('arg_separator.input', '&');
ini_set('arg_separator.input', '&amp;');
ini_set('display_errors', 'on');
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

//
// Some variables declaration
//
$diary_config = array();
$userdata = array();
$lang = array();

// Is this good idea to place this here ?
@include_once(INCLUDE_PATH . '.config.' . PHP_EXT);
@include_once(INCLUDE_PATH . 'const.inc.' . PHP_EXT);
/*
include_once(ROOT_PATH . 'include/template.inc.' . PHP_EXT);
include_once(ROOT_PATH . 'include/sessions.inc.' . PHP_EXT);
*/
@include_once(INCLUDE_PATH . 'functions.inc.' . PHP_EXT);
@include_once(INCLUDE_PATH . 'error.inc.' . PHP_EXT);

// Guess and encode users IP
/*
$user_ip = encode_ip(guess_ip());

$sql = "SELECT * FROM " . CONFIG_TABLE;
if(!($result = $db->query($sql)))
{
    message_die(CRITICAL_ERROR, "Could not query config information", "", __LINE__, __FILE__, $sql);
}

$rows = $db->fetch_rows($result);
while(@list(, $row) = @each($rows))
{
    $diary_config[$row->param] = $row->value;
}

if(!(@file_exists(ROOT_PATH . 'languages/lang_' . $diary_config['language'] . PHP_EXT)))
{
    $diary_config['language'] = 'english';
}

require_once(ROOT_PATH . 'languages/lang_' . $diary_config['language'] . '.' . PHP_EXT);

$db->free_result($result);
unset($rows);
*/
?>
