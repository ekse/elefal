<?php
/******************************************************************************
  Merge GET data
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
 * Creates a string of GET data by reading the PHP $_GET[] array and overriding
 * parameters defined in a second local array.  To avoid overriding a parameter
 * in the GET array, set it to null in the local array; to unset a parameter of
 * the GET array, set it to the empty string in the local array.  NOTE:  the
 * ampersand used to separate fields is ESCAPED to its HTML entity.  If you want
 * it unescaped, change the value of the $ampersand variable to just the
 * ampersand.
 * @param $getarray The PHP array containing the parameters
 * @param $localarray The array containing the local parameters to override
 * @return A URL-formatted string of all GET parameters
 */
function mergeGetData($getarray, $localarray)
{
  $out = "";
  $ampersand = "&amp;";
  foreach($getarray as $key => $value)
  {
    if (!is_null($localarray[$key]))
    {
      if ($localarray[$key] != "")
        $out .= $key."=".$localarray[$key].$ampersand;
    }
    else
    {
      if ($value != "")
        $out .= $key."=".$value.$ampersand;
    }
  }
  foreach($localarray as $key => $value)
  {
    if (is_null($getarray[$key]))
    {
      if ($value != "")
        $out .= $key."=".$value.$ampersand;
    }
  }
  // Removes trailing ampersand
  return substr($out, 0, strlen($out) - strlen($ampersand));
}

/**
 * Works like mergeGetData, except that the GET data to override the PHP $_GET
 * array is already formatted into an URL.
 * @param $getarray The PHP array containing the parameters
 * @param $url The URL containing the local parameters to override
 * @return A URL-formatted string of all GET parameters
 */
function mergeGetUrlData($getarray, $url)
{
  $page_sep = "?";  // Separator between page and GET values
  $get_sep = "&";   // Separator between GET values
  $equal_sep = "="; // Separator between parameter and value
  
  // Parses URL
  $urlparts = explode($page_sep, $url);
  $urlpage = $urlparts[0];
  if (!isset($urlparts[1])) {
    return $urlpage;
  }
  // Separates each part into parameter-value tuples
  $localarray = array();
  foreach($urlparams as $tuple)
  {
    $exploded = explode($equal_sep, $tuple);
    if ($exploded[0] != "")
      $localarray[$exploded[0]] = $exploded[1];
  }
  
  // Creates the final URL
  $mgd = mergeGetData($getarray, $localarray);
  return $urlpage.(($mgd != "") ? $page_sep.$mgd : "");
}
?>
