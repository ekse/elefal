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

// Checks for valid data
if (!isset($_POST["sellerid"]) || trim($_POST["sellerid"]) == "")
{
  header("Location: ".mergeGetUrlData($_GET, "error-empty-seller.php"));
  exit();
}

// Checks if seller ID changes
if (trim($_POST["originalid"]) != trim($_POST["sellerid"]))
{
  // Checks if target seller ID exists in database
  $query_string = "SELECT * FROM ".$config["ddDBPrefix"]."sellers WHERE sellerKey = '".strtoupper(db_encode($_POST["sellerid"]))."' LIMIT 1;";
  $result = db_query($db, $query_string);
  if (!is_null($result) && db_num_rows($result) > 0)
  {
    // If yes, warns and redirects to this seller info page
    header("Location: ".mergeGetUrlData($_GET, "error-duplicate-seller.php?key=".$_POST["sellerid"]));
    exit();
  }
  // Otherwise, delete previous table entry
  $query_string = "DELETE FROM ".$config["ddDBPrefix"]."sellers WHERE sellerKey = '".strtoupper(db_encode($_POST["originalid"]))."' LIMIT 1;";
  $result = db_query($db, $query_string);
  if (is_null($result))
  {
    die($lang["Error connecting to the database."].$lang["MySQL says:"]." ".db_error($db));
  }
  // And inserts new
  $query_string = "INSERT INTO ".$config["ddDBPrefix"]."sellers SET "
    ."sellerKey = '".strtoupper(db_encode($_POST["sellerid"]))."', "
    ."firstName = '".db_encode($_POST["firstname"])."', "
    ."lastName = '".db_encode($_POST["lastname"])."', "
    ."email = '".db_encode($_POST["email"])."', "
    ."phone = '".db_encode($_POST["phone"])."';";
  $result = db_query($db, $query_string);
  if (is_null($db))
  {
    die($lang["Error connecting to the database."].$lang["MySQL says:"]." ".db_error($db));
  }
}
else  // Otherwise, ID does not change; REPLACE syntax is enough
{
  // Replaces seller into database
  $query_string = "REPLACE INTO ".$config["ddDBPrefix"]."sellers SET "
    ."sellerKey = '".strtoupper(db_encode($_POST["sellerid"]))."', "
    ."firstName = '".db_encode($_POST["firstname"])."', "
    ."lastName = '".db_encode($_POST["lastname"])."', "
    ."email = '".db_encode($_POST["email"])."', "
    ."phone = '".db_encode($_POST["phone"])."';";
  $result = db_query($db, $query_string);
  if (is_null($db))
  {
    die($lang["Error connecting to the database."].$lang["MySQL says:"]." ".db_error($db));
  }
}

// Redirects to seller information page
header ("Location: ".mergeGetUrlData($_GET, "seller-info.php?key=".strtoupper($_POST["sellerid"])));
exit();
?>
