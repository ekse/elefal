<?php
/******************************************************************************
  Session management library
  Copyright (C) 2004,2006  Sylvain Hallé
  
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, 
  MA  02110-1301, USA.
******************************************************************************/

/**
 * This file is intended as a header to include in other PHP pages.  It must be
 * placed (included) BEFORE any HTML data has been served to the client (i.e.
 * before the DOCTYPE and HTML tags), otherwise HTTP headers can no longer be
 * modified and it does not work.
 *
 * NOTE:  This library uses two fields in the PHP $_SESSION[] array:  sessionid
 * which can be filled with any value (the lib only checks whether the parameter
 * is defined), and lastclick which is the last time a page with the session
 * header was invoked (to compute session timeout).  Using these field names in
 * your app will of course screw everything up.
 */

// Start (or continuation) of the PHP session
session_start();

// Hack required by IE6 to avoid problems
header("Cache-control: private");

// Session timeout length (in seconds)
$timeout = 600;

// URL to redirect if no session or session is timed out.  NOTE: URL must be
// absolute, and not relative!
$redirect = "http://localhost/eleFAL/index.php";

// Checks if a session exists
if (!isset($_SESSION["sessionid"]) || (isset($_SESSION["lastclick"]) && time() - $_SESSION["lastclick"] > $timeout))
{
  // Error: no session
  $_SESSION = array();
  session_destroy();
  header("Location: ".$redirect);
}
else
{
  // Saves time of last click
  $_SESSION["lastclick"] = time();
}
?>
