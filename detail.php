<?php
 /* detail.php
  * 2017 Creative Feed / Damian West
  * -
  * developed for NetworkSA as a public search function for the bookmark.central.sa.edu.au library system
  * to convert their system into a usable, searchable JSON API style function
  *
  * Usage: detail.php?record=<record_id>
  * Returns: 
  * - title: Item title
  * - author: Item author
  * - subjects: Item subjects (animals, foods, etc) [not working yet]
  * - call_number: Item call number
  * - publishing: Item publishing company
  * - item_type: Item type (book, puzzle, etc)
  * - notes: Any additional notes
  * - barcode: Record ID
  * - location: Physical location
  * - status: Availability status
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