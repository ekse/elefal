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

include_once("lang.inc.php");

// Connect to the database
$db = db_connect();
if (is_null($db))
{
  die($lang["Error connecting to the database."].$lang["MySQL says:"]." ".db_error($db));
}

// Checks if a seller is passed in parameter
if (!isset($_GET["key"]))
{
  header("Location: ".mergeGetUrlData($_GET, "error-no-seller.php"));
  exit();
}

// Gets seller info
$result = db_query($db, "SELECT * FROM ".$config["ddDBPrefix"]."sellers WHERE sellerKey = '".$_GET["key"]."' LIMIT 1;");

// If no result, warns
if (db_num_rows($result) < 1)
{
  header("Location: ".mergeGetUrlData($_GET, "error-no-seller.php"));
  exit();
}

// Gets infos for this seller
$answer = db_fetch_assoc_array($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include("stylesheet.inc.php");?>
<title>
  <?php echo $lang["Seller information"]; ?> - <?php echo $config["appname"]; ?>
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
      <h1><?php echo $lang["Seller information"]; ?></h1>
      <p>
      <?php echo $lang["Informations for this seller are the following."]; ?>
      </p>
      <p>
      <a href="<?php echo mergeGetUrlData($_GET, "seller-edit.php?key=".$_GET["key"]."&show="); ?>"><?php echo $lang["Edit seller information"]; ?></a>
      </p>
      
      <table class="noborder">
      <tr>
        <td class="rightalign"><?php echo $lang["First name:"]; ?></td><td><?php echo db_decode($answer["firstName"]); ?></td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["Last name:"]; ?></td><td><?php echo db_decode($answer["lastName"]); ?></td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["Seller ID:"]; ?></td><td><?php echo $_GET["key"]; ?></td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["E-mail:"]; ?></td><td><?php echo db_decode($answer["email"]); ?></td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["Phone:"]; ?></td><td><?php echo db_decode($answer["phone"]); ?></td>
      </tr>
      </table>
      
      <h2><?php echo $lang["Books related to this seller"]; ?></h2>
<?php
// Get ordering criterion if specified
$sortstring = "ORDER BY ";

$sort_by = isset($_GET["sort"]) ? $_GET["sort"] : "";

switch ($sort_by)
{
  case "author":
    $sortstring .= "author";
    break;
  case "title":
    $sortstring .= "title";
    break;
  case "price":
    $sortstring .= "price";
    break;
  case "sellerid":
    $sortstring .= "sellerKey";
    break;
  default:
    $sortstring .= "bookID";
    break;
}

// Get display criterion if specified
if (isset($_GET["show"]) && $_GET["show"] == "instock")
  $whereclause = "status = 'instock' ";
else
  $whereclause = "TRUE ";

// Get all the books
$result = db_query($db, "SELECT * FROM ".$config["ddDBPrefix"]."books WHERE sellerKey = '".db_encode($_GET["key"])."' AND ".$whereclause.$sortstring.";");
if (!is_null($result)) {
?>
      <?php if (isset($_GET["show"]) && $_GET["show"] == "instock") { ?>
      <p><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?show="); ?>"><?php echo $lang["Show all books"]; ?></a></p>
      <?php } else { ?>
      <p><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?show=instock"); ?>"><?php echo $lang["Show only books in stock"]; ?></a></p>
      <?php } ?>
      
      <form name="markbooks" method="post" action="<?php echo mergeGetUrlData($_GET, "seller-mark-action.php"); ?>">
      
      <table id="bookbrowse">
      <tr class="bookbrowse-header">
        <td><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?sort=bookid"); ?>"><?php echo $lang["Book ID"]; ?></a></td>
        <td><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?sort=title"); ?>"><?php echo $lang["Title"]; ?></a></td>
        <td><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?sort=author"); ?>"><?php echo $lang["Author(s)"]; ?></a></td>
        <td><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?sort=sellerid"); ?>"><?php echo $lang["Seller ID"]; ?></a></td>
        <td><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?sort=price"); ?>"><?php echo $lang["Price"]; ?></a></td>
        <td><?php echo $lang["Status"]; ?></td>
        <td></td>
      </tr>
<?php
$modulo = 0;
$total_price = 0;
while ($answer = db_fetch_assoc_array($result))
{
  $modulo = ($modulo == 0) ? 1 : 0;
  if ($answer["status"] == "sold" || $answer["status"] == "lost")
  {
    // Book is to be refunded to seller if sold or lost
    $total_price += $answer["price"];
  }
?>
      <tr class="bookbrowse-data-<?php echo $modulo; ?>">
        <td>
          <?php echo db_decode($answer["bookID"]); ?>
        </td>
        <td><a href="<?php echo mergeGetUrlData($_GET, "book-info.php?bookid=".db_decode($answer["bookID"])."&sort=&show=&key="); ?>"><?php echo db_decode($answer["title"]); ?></a></td>
        <td><?php echo db_decode($answer["author"]); ?></td>
        <td><?php echo db_decode($answer["sellerKey"]); ?></td>
        <td><?php echo db_decode($answer["price"]); ?>&nbsp;<?php echo $lang["CURRENCY"]; ?></td>
        <td>
        <?php
        switch ($answer["status"])
        {
          case "instock":
            echo "<span class=\"instock\">".$lang["In stock"]."</span>\n";
            break;
          case "expected":
            echo "<span class=\"expected\">".$lang["Expected"]."</span>\n";
            break;
          case "lost":
            echo "<span class=\"lost\">".$lang["Lost"]."</span>\n";
            break;
          case "returned":
            echo "<span class=\"returned\">".$lang["Returned"]."</span>\n";
            break;
          case "sold":
            echo "<span class=\"sold\">".$lang["Sold"]."</span>\n";
            break;
          case "notset":
            echo "<span class=\"notset\">".$lang["Undefined"]."</span>\n";
            break;
        }
        ?>
        </td>
        <td>
          <input type="checkbox" name="<?php echo $answer["bookID"]; ?>"/>
        </td>
      </tr>
<?php
} }
?>
      </table>
      <?php if (!isset($_GET["show"]) || $_GET["show"] != "instock") { ?>
      <p>
      <?php echo $lang["Total to be refunded to seller:"]; ?>
      <?php echo $total_price; ?>&nbsp;<?php echo $lang["CURRENCY"]; ?>
      </p>
      <?php } ?>
      
      <p class="noprint">
      <?php echo $lang["Press the button below to mark all checked books as returned to the seller."]; ?>
      </p>
      <input class="noprint" type="submit" value="<?php echo $lang["Submit"]; ?>"/> 
      </form>
      
      <h2 class="printonly"><?php echo $lang["Signatures"]; ?></h2>
      <p class="printonly">
      ________________________________<br/>
      <?php echo $lang["Seller"]; ?>
      </p>
      <p class="printonly">
      ________________________________<br/>
      <?php echo $lang["Responsible"]; ?>
      </p>
      <!-- End -->
      </div>
    </div>
  </div>

  <!-- Navigation thread -->
  <div id="navigation">
    <h2 id="navigation-title"><?php echo $lang["NAV"]; ?></h2>
    <p id="navigation-links">
    <a id="link-bookadd" href="<?php echo mergeGetUrlData($_GET, "book-add.php?&key=&show="); ?>"><?php echo $lang["Add a book"]; ?></a>
    <a id="link-bookbrowse" href="<?php echo mergeGetUrlData($_GET, "book-browse.php?&key=&show="); ?>"><?php echo $lang["Browse books"]; ?></a>
    <a id="link-booksell" href="<?php echo mergeGetUrlData($_GET, "book-sell.php?&key=&show="); ?>"><?php echo $lang["Sell a book"]; ?></a>
    <a id="link-selleradd" href="<?php echo mergeGetUrlData($_GET, "seller-add.php?&key=&show="); ?>"><?php echo $lang["Add a seller"]; ?></a>
    <a id="link-sellerbrowse" href="<?php echo mergeGetUrlData($_GET, "seller-browse.php?&key=&show="); ?>"><?php echo $lang["Browse sellers"]; ?></a>
    <a id="link-dbclear" href="<?php echo mergeGetUrlData($_GET, "database-clear.php"); ?>"><?php echo $lang["Clear database"]; ?></a>
    <a id="link-logout" href="<?php echo mergeGetUrlData($_GET, "logout.php?&key=&show="); ?>"><?php echo $lang["Logout"]; ?></a>
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
