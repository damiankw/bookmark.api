<?php
 /* nsa_detail.php
  * 2017 Creative Feed / Damian West
  * -
  * developed for NetworkSA as a public search function for the bookmark.central.sa.edu.au library system
  * -
  * Usage:
  * nsa_search.php?keyword=<chk_keyword>&title=<chk_title>&author=<chk_author>&subject=<chk_subject>&series=<chk_series>&searchfor=<search_parameters>
  * ^^ Queries the database and outputs a JSON return file
  */

// include the required files
require('nsa_bookmark.php');

// check to ensure the form has been submitted
if (!isset($_REQUEST['record'])) {
  die('ERROR You have not entered the required detail.');
}

$NSA = new nsa_bookmark();

echo json_encode($NSA->detail($_REQUEST['record']), JSON_UNESCAPED_UNICODE);
?>