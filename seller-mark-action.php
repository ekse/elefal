<?
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
require_once("db-connection.lib.php");
require_once("encode-decode.lib.php");
require_once("merge-get.lib.php");
require_once("session.lib.php");

// Override default language settings by session settings
if ($_SESSION["lang"])
  include_once("lang/".$_SESSION["lang"].".php");

// Override default language settings by URL settings
if ($_GET["lang"])
  include_once("lang/".$_GET["lang"].".php");

// Connects to the database
$db = db_connect();
if (is_null($db))
{
  die($lang["Error connecting to the database."].$lang["MySQL says:"]." ".db_error($db));
}

$flag = 0;
$whereclause = "";
foreach($_POST as $key => $value)
{
  $flag++;
  if ($value == "on")
    $whereclause .= "bookID = ".$key." OR ";
}
$whereclause .= "1>2";

// Adds book into database
$timestamp = time();
$query_string = "UPDATE ".$config["ddDBPrefix"]."books SET "
  ."status = 'returned', "
  ."lastUpdate = '".date("Y-m-d H:i:s", $timestamp)."' "
  ."WHERE ".$whereclause." LIMIT ".$flag.";";
$result = db_query($db, $query_string);

// Redirects to seller information page
header ("Location: ".mergeGetUrlData($_GET, "seller-info.php?key=".$_GET["key"]));
exit();
?>
