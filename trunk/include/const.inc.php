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

// User levels
define('GUEST', -1);
define('OWNER', 0);
define('ADMIN', 1);
define('USER', 2);

// Tables names
define('TABLE_COMMENTS', 'comments');
define('TABLE_GUESTBOOK', 'guestbook');
define('TABLE_LINKS', 'links');
define('TABLE_LINKS_CATEGORIES', 'links_categories');
define('TABLE_NOTES', 'notes');
define('TABLE_SETTINGS', 'settings');
define('TABLE_USERS', 'users');

// Pages
define('PAGE_ARCHIVE', 'index.php?page=archive');
define('PAGE_COMMENTS', 'index.php?page=comments');
define('PAGE_GUESTBOOK', 'index.php?page=guestbook');

// Error codes
define('INFORMATION', 1);
define('GENERAL_ERROR', 2);
define('CRITICAL_ERROR', 3);

// Archive
define('SHOW_NOTE', 1);
define('SHOW_MONTH', 2);

?>
