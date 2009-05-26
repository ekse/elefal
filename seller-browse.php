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

// Get ordering criterion if specified
switch ($_GET["sort"])
{
  case "firstname":
    $sortstring .= "ORDER BY firstName";
    break;
  case "lastName":
    $sortstring .= "ORDER BY lastName";
    break;
  case "key":
    $sortstring .= "ORDER BY sellerKey";
    break;
  default:
    $sortstring .= "ORDER BY lastName";
    break;
}

// Get display criterion if specified
$whereclause = "";

// Connects to the database
$db = db_connect();
if (is_null($db))
{
  die($lang["Error connecting to the database."].$lang["MySQL says:"]." ".db_error($db));
}

// Get all the books
$result = db_query($db, "SELECT * FROM ".$config["ddDBPrefix"]."sellers ".$whereclause.$sortstring.";");
if (is_null($result))
{
  header("Location: ".mergeGetUrlData($_GET, "error-no-seller.php"));
  exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
  <meta name="robots" content="noindex,nofollow"/>
  <link rel="stylesheet" type="text/css" href="styles/<?php echo $_GET["style"]; ?>/style.css" title="Current style"/>
  <link rel="shortcut icon" type="image/x-icon" href="styles/icone.ico"/>
  <!-- Alternate stylesheets -->
  <link rel="alternate stylesheet" type="text/css" href="styles/printer-friendly/style.css" title="Printer friendly"/>
  <!-- End of alternate stylesheets -->
  <title>
  <?php echo $lang["Browse sellers"]; ?> - <?php echo $config["appname"]; ?>
  </title>
</head>
<body>
<div id="underbody">

<div id="header">
  <div id="header-left"></div>
  <div id="header-right"></div>
</div>

<div id="main1">
<div id="main2">

  <!-- Empty left and right borders -->
  <div id="left"></div>
  <div id="right"></div>

  <!-- Main content -->
  <div id="middle">
    <div id="undercenter">
      <div class="column-in">
      <!-- Begin -->
      <h1><?php echo $lang["Browse sellers"]; ?></h1>
      
      <?php if (isset($_GET["showall"]) && $_GET["showall"] == 1) { ?>
      <p><a href="<?php echo mergeGetUrlData($_GET, "book-browse.php?showall="); ?>"><?php echo $lang["Show only books in stock"]; ?></a></p>
      <?php } else { ?>
      <p><a href="<?php echo mergeGetUrlData($_GET, "book-browse.php?showall=1"); ?>"><?php echo $lang["Show all books"]; ?></a></p>
      <?php } ?>
      
      <table id="bookbrowse">
      <tr class="bookbrowse-header">
        <td><a href="<?php echo mergeGetUrlData($_GET, "seller-browse.php?sort=key"); ?>"><?php echo $lang["Seller ID"]; ?></a></td>
        <td><a href="<?php echo mergeGetUrlData($_GET, "seller-browse.php?sort=lastname"); ?>"><?php echo $lang["Last name"]; ?></a>, <a href="<?php echo mergeGetUrlData($_GET, "seller-browse.php?sort=firstname"); ?>"><?php echo $lang["first name"]; ?></a></td>
      </tr>
<?php
$modulo = 0;
while ($answer = db_fetch_assoc_array($result))
{
  $modulo = ($modulo == 0) ? 1 : 0;
?>
      <tr class="bookbrowse-data-<?php echo $modulo; ?>">
        <td><?php echo db_decode($answer["sellerKey"]); ?></td>
        <td><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?key=".db_decode($answer["sellerKey"])."&sort="); ?>"><?php echo db_decode($answer["lastName"]); ?>, <?php echo db_decode($answer["firstName"]); ?></a></td>
      </tr>
<?php
}
?>
      </table>
      <!-- End -->
      </div>
    </div>
  </div>

  <!-- Navigation thread -->
  <div id="navigation">
    <h2 id="navigation-title"><?php echo $lang["NAV"]; ?></h2>
    <p id="navigation-links">
    <a id="link-bookadd" href="<?php echo mergeGetUrlData($_GET, "book-add.php"); ?>"><?php echo $lang["Add a book"]; ?></a>
    <a id="link-bookbrowse" href="<?php echo mergeGetUrlData($_GET, "book-browse.php"); ?>"><?php echo $lang["Browse books"]; ?></a>
    <a id="link-booksell" href="<?php echo mergeGetUrlData($_GET, "book-sell.php"); ?>"><?php echo $lang["Sell a book"]; ?></a>
    <a id="link-selleradd" href="<?php echo mergeGetUrlData($_GET, "seller-add.php"); ?>"><?php echo $lang["Add a seller"]; ?></a>
    <a id="link-sellerbrowse" href="<?php echo mergeGetUrlData($_GET, "seller-browse.php"); ?>"><?php echo $lang["Browse sellers"]; ?></a>
    <a id="link-dbclear" href="<?php echo mergeGetUrlData($_GET, "database-clear.php"); ?>"><?php echo $lang["Clear database"]; ?></a>
    <a id="link-logout" href="<?php echo mergeGetUrlData($_GET, "logout.php"); ?>"><?php echo $lang["Logout"]; ?></a>
    </p>
  </div>

</div>
</div>

<!-- Page footer -->
<div id="footer">
  <div id="footer-left"></div>
  <div id="footer-right"></div>
  <hr id="hr-footer"/>
  <div id="footer-text-left">
    <?php echo $lang["CREDITS"]; ?>
    <?php echo $lang["Last modified:"]; ?> 2007-08-22.
    <a href="<?php echo mergeGetUrlData($_GET, "?style=printer-friendly"); ?>"><?php echo $lang["Printer-friendly version"]; ?></a> <?php echo $lang["of this page"]; ?>.
    <a href="<?php echo (mergeGetUrlData($_GET, "?style=") != "") ? mergeGetUrlData($_GET, "?style=") : "?style="; ?>"><?php echo $lang["Default style version"]; ?></a> <?php echo $lang["of this page"]; ?>.
  </div>
  <div id="footer-text-right">
  <!-- Nothing here -->
  </div>
</div>

</div>
</body>
</html>
