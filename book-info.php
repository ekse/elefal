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

// Set Language
include_once("lang.inc.php");

// Connects to the database
$db = db_connect();
if (is_null($db))
{
  die($lang["Error connecting to the database."].$lang["MySQL says:"]." ".db_error($db));
}

// Checks if a book is passed in parameter
if (!isset($_GET["bookid"]))
{
  header("Location: ".mergeGetUrlData($_GET, "error-no-book.php"));
  exit();
}

// Gets book ID
$result = db_query($db, "SELECT * FROM ".$config["ddDBPrefix"]."books WHERE bookID = ".$_GET["bookid"]." LIMIT 1;");
$answer = db_fetch_assoc_array($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include("stylesheet.inc.php"); ?>  
<title>
<?php echo $lang["Book information"]; ?> - <?php echo $config["appname"]; ?>
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
      <h1><?php echo $lang["Book information"]; ?></h1>
      <p>
      <?php echo $lang["Informations for this book are the following."]; ?>
      </p>
      
      <p>
      <a href="<?php echo mergeGetUrlData($_GET, "book-edit.php?bookid=".db_decode($answer["bookID"])); ?>"><?php echo $lang["Edit book information"]; ?></a>
      </p>
      
      <table class="noborder">
      <tr>
        <td class="rightalign"><?php echo $lang["Book ID:"]; ?></td><td><?php echo db_decode($answer["bookID"]); ?></td>
      </tr>
<?php
// Gets seller info
$result2 = db_query($db, "SELECT * FROM ".$config["ddDBPrefix"]."sellers WHERE sellerKey = '".$answer["sellerKey"]."' LIMIT 1;"); // Do not db_decode this
$answer2 = db_fetch_assoc_array($result2);
?>
      <tr>
        <td class="rightalign"><?php echo $lang["Seller ID:"]; ?></td><td><a href="<?php echo mergeGetUrlData($_GET, "seller-info.php?key=".db_decode($answer["sellerKey"])."&bookid="); ?>"><?php echo db_decode($answer["sellerKey"]); ?></a>
        (<?php
        if (db_num_rows($result2) == 0)
          echo $lang["Seller not in database"];
        else
          echo db_decode($answer2["firstName"])." ".db_decode($answer2["lastName"]);
        ?>)
        </td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["Book title:"]; ?></td><td><?php echo db_decode($answer["title"]); ?></td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["Author(s):"]; ?></td><td><?php echo db_decode($answer["author"]); ?></td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["Year:"]; ?></td><td><?php echo db_decode($answer["bookYear"]); ?></td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["Price:"]; ?></td><td><?php echo db_decode($answer["price"]); ?>&nbsp;<?php echo $lang["CURRENCY"]; ?></td>
      </tr>
      <tr>
        <td class="rightalign"><?php echo $lang["Status:"]; ?></td>
        <td>
          <?php
          switch ($answer["status"])
          {
            case "instock":
              echo $lang["In stock"];
              break;
            case "expected":
              echo $lang["Expected"];
              break;
            case "sold":
              echo $lang["Sold"];
              break;
            case "returned":
              echo $lang["Returned"];
              break;
            case "lost":
              echo $lang["Lost"];
              break;
            case "notset":
              echo $lang["Undefined"];
              break;
          }
          ?>
        </td>
      </tr>
      </table>
      <!-- End -->
      </div>
    </div>
  </div>

  <!-- Navigation thread -->
  <div id="navigation">
    <h2 id="navigation-title"><?php echo $lang["NAV"]; ?></h2>
    <p id="navigation-links">
    <a id="link-bookadd" href="<?php echo mergeGetUrlData($_GET, "book-add.php?bookid="); ?>"><?php echo $lang["Add a book"]; ?></a>
    <a id="link-bookbrowse" href="<?php echo mergeGetUrlData($_GET, "book-browse.php?bookid="); ?>"><?php echo $lang["Browse books"]; ?></a>
    <a id="link-booksell" href="<?php echo mergeGetUrlData($_GET, "book-sell.php?bookid="); ?>"><?php echo $lang["Sell a book"]; ?></a>
    <a id="link-selleradd" href="<?php echo mergeGetUrlData($_GET, "seller-add.php?bookid="); ?>"><?php echo $lang["Add a seller"]; ?></a>
    <a id="link-sellerbrowse" href="<?php echo mergeGetUrlData($_GET, "seller-browse.php?bookid="); ?>"><?php echo $lang["Browse sellers"]; ?></a>
    <a id="link-dbclear" href="<?php echo mergeGetUrlData($_GET, "database-clear.php"); ?>"><?php echo $lang["Clear database"]; ?></a>
    <a id="link-logout" href="<?php echo mergeGetUrlData($_GET, "logout.php?bookid="); ?>"><?php echo $lang["Logout"]; ?></a>
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
