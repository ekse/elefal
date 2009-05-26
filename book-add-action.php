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

/**
 * Last modified: 2007-08-22 SH
 */

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

// Checks if seller exists in database (it must)
$query_string = "SELECT * FROM ".$config["ddDBPrefix"]."sellers WHERE sellerKey = '".strtoupper($_POST["sellerid"])."' LIMIT 1;";
$result= db_query($db, $query_string);
if (is_null($result) || db_num_rows($result) < 1)
{
  header("Location: ".mergeGetUrlData($_GET, "error-no-seller.php"));
  exit();
}

// Adds book into database
$timestamp = time();
$query_string = "INSERT INTO ".$config["ddDBPrefix"]."books SET "
  ."sellerKey = '".strtoupper(db_encode($_POST["sellerid"]))."', "
  ."title = '".db_encode($_POST["title"])."', "
  ."author = '".db_encode($_POST["author"])."', "
  ."bookYear = ".($_POST["year"] == "" ? "NULL" : db_encode($_POST["year"])).", "
  ."price = ".db_encode(format_number($_POST["price"])).", "
  ."status = '".db_encode($_POST["status"])."', "
  ."lastUpdate = '".date("Y-m-d H:i:s", $timestamp)."';";
$result = db_query($db, $query_string);

// Gets book ID
$query_string = "SELECT * FROM ".$config["ddDBPrefix"]."books WHERE "
  ."sellerKey = '".strtoupper(db_encode($_POST["sellerid"]))."' AND "
  ."title = '".db_encode($_POST["title"])."' AND "
  ."author = '".db_encode($_POST["author"])."' AND "
  ."bookYear ".($_POST["year"] == "" ? " IS NULL" : " = ".db_encode($_POST["year"]))." AND "
  ."ABS(price - ".db_encode(format_number($_POST["price"])).") < 0.01 AND "
  ."status = '".db_encode($_POST["status"])."' AND "
  ."lastUpdate = '".date("Y-m-d H:i:s", $timestamp)."' LIMIT 1;";
$result = db_query($db, $query_string);
//echo $query_string;
$answer = db_fetch_assoc_array($result);
$bookid = $answer["bookID"];

// Sleeps for 1/2 second so that MySQL can add the record
usleep(500000);

// Redirects to book information page
header ("Location: ".mergeGetUrlData($_GET, "book-info.php?bookid=".db_decode($answer["bookID"])));
exit();
?>
