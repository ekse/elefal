<?php
/******************************************************************************
  String encoding-decoding functions
  Copyright (C) 2005  Sylvain Hallé
  
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
 * Encodes an arbitrary string into a MySQL compatible string
 * @param $s The string to encode
 * @return The encoded string
 */
function db_encode($s)
{
  $matches = array("\\", "'", "\"", "\0", "\b", "\n", "\r", "\t");
  $replacements = array("\\\\", "\\'", "\\\"", "\\0", "\\b", "\\n", "\\r",
    "\\t");
  $st = str_replace($matches, $replacements, $s);
  return $st;
}

/**
 * Decodes a MySQL string (at the moment does nothing particular)
 * @param $s The string to decode
 * @return The same string (for the moment)
 */
function db_decode($s)
{
  $matches = array("\\", "%", "'", "\"", "\0", "\b", "\n", "\r", "\t", "_");
  $replacements = array("\\\\", "\\%", "\\\"", "\\0", "\\b", "\\n", "\\r",
    "\\t", "\\_");
  $st = str_replace($replacements, $matches, $s);
  return $st;
}

/**
 * Encodes a French decimal number to English notation.  Note: this
 * function also removes common currency symbols such as "F" and "$".
 * @param $s The string to convert
 * @return The converted string
 */
function format_number($s)
{
  $out = $s;
  $out = str_replace(",", ".", $out);
  $out = str_replace("\$", "", $out);
  $out = str_replace("F", "", $out);
  $out = str_replace(" ", "", $out);
  return $out;
}

/**
 * Encodes a (local) filename so that links to local files work
 * @param $f String containing the filename
 * @return The URL-encoded filename
 */
function encode_filename($f)
{
  $out = $f;
  
  // Changes "\" to "/"
  $out = str_replace("\\", "/", $out);
  // Encodes string
  $out = urlencode($out);
  // Replaces slashes
  $out = str_replace("%2F", "/", $out);
  // Replaces colons
  $out = str_replace("%3A", ":", $out);
  // Replaces commas
  $out = str_replace("%2C", ",", $out);
  // Replaces spaces in "+" with "%20"
  $out = str_replace("+", "%20", $out);
  // Appends default directory
  $out = DEFAULT_DIR.$out;
  return $out;
}

/**
 * Renders a LaTeX/BibTeX string into a UTF text string.  For example, converts
 * "\'e" into "é".
 * @param $s The string to convert
 * @return The converted string
 */
function latex2utf($s)
{
  $out = $s;
  $patterns_braces = array(
    "{\\'a}", "{\\`a}", "{\\^a}", "{\\\"a}", "{\\~a}", "{\\aa}", "{\\ae}",
    "{\\'A}", "{\\`A}", "{\\^A}", "{\\\"A}", "{\\~A}", "{\\AA}", "{\\AE}",
    "{\\c{c}}",
    "{\\c{C}}",
    "{\\'e}", "{\\`e}", "{\\^e}", "{\\\"e}",
    "{\\'E}", "{\\`E}", "{\\^E}", "{\\\"E}",
    "{\\'i}", "{\\`i}", "{\\^i}", "{\\\"i}",
    "{\\'I}", "{\\`I}", "{\\^I}", "{\\\"I}",
    "{\\~n}",
    "{\\~N}",
    "{\\'o}", "{\\`o}", "{\\^o}", "{\\\"o}", "{\\~o}", "{\\oe}", "{\\o}",
    "{\\'O}", "{\\`O}", "{\\^O}", "{\\\"O}", "{\\~O}", "{\\OE}", "{\\O}",
    "{\\'u}", "{\\`u}", "{\\^u}", "{\\\"u}",
    "{\\'U}", "{\\`U}", "{\\^U}", "{\\\"U}",
    "{\\\"s}", "{!`}", "{?`}", "{\\&}"
    );
  // Same thing without enclosing braces
  $patterns_nobraces = array(
    "\\'a", "\\`a", "\\^a", "\\\"a", "\\~a", "\\aa", "\\ae",
    "\\'A", "\\`A", "\\^A", "\\\"A", "\\~A", "\\AA", "\\AE",
    "\\c{c}",
    "\\c{C}",
    "\\'e", "\\`e", "\\^e", "\\\"e",
    "\\'E", "\\`E", "\\^E", "\\\"E",
    "\\'i", "\\`i", "\\^i", "\\\"i",
    "\\'I", "\\`I", "\\^I", "\\\"I",
    "\\~n",
    "\\~N",
    "\\'o", "\\`o", "\\^o", "\\\"o", "\\~o", "\\oe", "\\o",
    "\\'O", "\\`O", "\\^O", "\\\"O", "\\~O", "\\OE", "\\O",
    "\\'u", "\\`u", "\\^u", "\\\"u",
    "\\'U", "\\`U", "\\^U", "\\\"U",
    "\\\"s", "!`", "?`", "\\&"
    );
  $replacements = array(
    "á", "à", "â", "ä", "ã", "å", "æ",
    "Á", "À", "Â", "Ä", "Ã", "Å", "Æ",
    "ç",
    "Ç",
    "é", "è", "ê", "ë",
    "É", "È", "Ê", "Ë",
    "í", "ì", "î", "ï",
    "Í", "Ì", "Î", "Ï",
    "ñ",
    "Ñ",
    "ó", "ò", "ô", "ö", "õ", "œ", "ø",
    "Ó", "Ò", "Ô", "Ö", "Õ", "Œ", "Ø",
    "ú", "ù", "û", "ü",
    "Ú", "Ù", "Û", "Ü",
    "ß", "¡", "¿", "&");
  $out = str_replace($patterns_braces, $replacements, $out);
  $out = str_replace($patterns_nobraces, $replacements, $out);
  return $out;
}

/**
 * Opposite of latex2utf.  Note that all LaTeX characters have enclosing braces
 * to make them compatible with BibTeX.
 * @param $s The string to encode
 * @return The LaTeX encoded string
 */
function utf2latex($s)
{
  $out = $s;
  $patterns = array(
    "{\\'a}", "{\\`a}", "{\\^a}", "{\\\"a}", "{\\~a}", "{\\aa}", "{\\ae}",
    "{\\'A}", "{\\`A}", "{\\^A}", "{\\\"A}", "{\\~A}", "{\\AA}", "{\\AE}",
    "{\\c{c}}",
    "{\\c{C}}",
    "{\\'e}", "{\\`e}", "{\\^e}", "{\\\"e}",
    "{\\'E}", "{\\`E}", "{\\^E}", "{\\\"E}",
    "{\\'i}", "{\\`i}", "{\\^i}", "{\\\"i}",
    "{\\'I}", "{\\`I}", "{\\^I}", "{\\\"I}",
    "{\\~n}",
    "{\\~N}",
    "{\\'o}", "{\\`o}", "{\\^o}", "{\\\"o}", "{\\~o}", "{\\oe}", "{\\o}",
    "{\\'O}", "{\\`O}", "{\\^O}", "{\\\"O}", "{\\~O}", "{\\OE}", "{\\O}",
    "{\\'u}", "{\\`u}", "{\\^u}", "{\\\"u}",
    "{\\'U}", "{\\`U}", "{\\^U}", "{\\\"U}",
    "{\\\"s}", "{!`}", "{?`}");
  $replacements = array(
    "á", "à", "â", "ä", "ã", "å", "æ",
    "Á", "À", "Â", "Ä", "Ã", "Å", "Æ",
    "ç",
    "Ç",
    "é", "è", "ê", "ë",
    "É", "È", "Ê", "Ë",
    "í", "ì", "î", "ï",
    "Í", "Ì", "Î", "Ï",
    "ñ",
    "Ñ",
    "ó", "ò", "ô", "ö", "õ", "œ", "ø",
    "Ó", "Ò", "Ô", "Ö", "Õ", "Œ", "Ø",
    "ú", "ù", "û", "ü",
    "Ú", "Ù", "Û", "Ü",
    "ß", "¡", "¿");
  $out = str_replace($replacements, $patterns, $out);
  return $out;
}
?>
