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

// Set language
include_once("lang.inc.php");

// Connects to the database
$db = db_connect();
if (is_null($db))
{
  die($lang["Error connecting to the database."].$lang["MySQL says:"]." ".db_error($db));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include("stylesheet.inc.php"); ?>
<title>
<?php echo $lang["Sell books (checklist)"]; ?> - <?php echo $config["appname"]; ?>
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
      <h1><?php echo $lang["Sell books (checklist)"]; ?></h1>
      <p>
      <?php echo $lang["Before proceeding to the transaction, please check that the books listed below are the right ones."]; ?>
      <?php echo $lang["You can change the list by getting back to the previous page."]; ?>
      </p>
      
      <h2><?php echo $lang["Book list"]; ?></h2>
      <form name="book-list-form" method="post" action="<?php echo mergeGetUrlData($_GET, "book-sell-action.php"); ?>">
<?php
for ($i = 1; $i <= 10; $i++)
{ ?>
      <input type="hidden" name="bookid-<?php echo $i; ?>" value="<?php echo $_POST["bookid-".$i]; ?>"/>
<?php } ?>
      <table id="book-checklist">
<?php
$total_price = 0;
for ($i = 1; $i <= 10; $i++)
{
  if ($_POST["bookid-".$i] != "")
  {
  $result = db_query($db, "SELECT * FROM ".$config["ddDBPrefix"]."books WHERE bookID = ".db_encode($_POST["bookid-".$i])." LIMIT 1;");
  $answer = db_fetch_assoc_array($result);
  $total_price += $answer["price"];
?>
      <tr>
        <td>
        <span class="bookid"><?php echo db_decode($answer["bookID"]); ?></span>
        </td>
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
        <span class="bookauthor"><?php echo db_decode($answer["author"]); ?></span>.
        (<span class="bookyear"><?php echo db_decode($answer["bookYear"]); ?></span>).
        <span class="booktitle"><?php echo db_decode($answer["title"]); ?></span>.
        </td>
        <td>
        <span class="bookprice"><?php echo db_decode($answer["price"]); ?>&nbsp;<?php echo $lang["CURRENCY"]; ?></span>
        </td>
      </tr>
<?php } } ?>
      </table>
      
      <p>
      <?php echo $lang["Total price"]; ?>:
      <?php echo $total_price; ?>&nbsp;<?php echo $lang["CURRENCY"]; ?>
      </p>
      
      <input type="submit" value="<?php echo $lang["Proceed"]; ?>"/>
      </form>
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
