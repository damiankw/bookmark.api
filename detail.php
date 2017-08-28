<?php
 /* detail.php
  * 2017 Creative Feed / Damian West
  * -
  * developed for NetworkSA as a public search function for the bookmark.central.sa.edu.au library system
  * to convert their system into a usable, searchable JSON API style function
  *
  * Usage: detail.php?record=<record_id>
  * Returns the following fields in JSON, some may not exist (depending on type):
  * 
  */

// include the required files
require('bookmark.php');

// check to ensure the form has been submitted
if (!isset($_REQUEST['record'])) {
  die('ERROR You have not entered the required detail.');
}

$NSA = new nsa_bookmark();

echo json_encode($NSA->detail($_REQUEST['record']), JSON_UNESCAPED_UNICODE);

?>