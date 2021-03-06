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

session_start();

define('IN_DIARY', true);
define('ROOT_PATH', realpath('./') . '/');
define('INCLUDE_PATH', realpath(ROOT_PATH . '../include') . '/');

setlocale(LC_ALL, 'en_EN');
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

if(@$_GET['code'] == 404)
{
    show_error_page($dbconn, $diary_login, "Nie znaleziono zadanego pliku na serwerze " . $_SERVER['SERVER_NAME']);
}
else
{
    echo "NIEZNANY KOD";
}

?>
