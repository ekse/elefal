<?php
if (isset($_SESSION["lang"]))
  include_once("lang/".$_SESSION["lang"].".php");

// Override default language settings by URL settings
if (isset($_GET["lang"]))
  include_once("lang/".$_GET["lang"].".php");
?>

