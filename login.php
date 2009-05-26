<?php
/******************************************************************************
  eleFAL, a used-book selling management tool
  Copyright (C) 2006  Sylvain Hallé
  
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

// Inclusion of configuration files
require_once("config.inc.php");

// Inclusion of libraries
require_once("merge-get.lib.php");

// Override default language settings by session settings
if ($_SESSION["lang"])
  include_once("lang/".$_SESSION["lang"].".php");

// Override default language settings by URL settings
if ($_GET["lang"])
  include_once("lang/".$_GET["lang"].".php");

// Checks username and password
if (isset($_POST["username"]) && isset($_POST["password"]))
{
  // Basic check; to be replaced later
  if ($_POST["username"] == $config["adminlogin"] && $_POST["password"] == $config["adminpassword"])
  {
    // Login OK, starts session and puts session id
    session_start();
    $_SESSION["sessionid"] = "123"; // Bogus data
    $_SESSION["lastclick"] = time();
    // Redirects to main page
    header("Location: ".mergeGetUrlData($_GET, "main.php"));
    exit();
  }
}
// Wrong identification
die("Bad login");
?>
