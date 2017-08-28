<?php
 /* bookmark.php
  * 2017 Creative Feed / Damian West
  * -
  * developed for NetworkSA as a public search function for the bookmark.central.sa.edu.au library system
  * to convert their system into a usable, searchable JSON API style function
  */

class nsa_bookmark {
  // URL that bookmark lives, as well as databage/page detail
  private $URL = 'http://bookmark.central.sa.edu.au/bmcpac.exe';
  private $DB = 'networksa';
  private $PAGE = 'networksa';

  function search($FIELD, $SEARCHFOR) {
    // Build the data you need to submit - this will get pulled from the SUBMIT on the calling page
    $DATA = array(
      'dbname' => $this->DB,
      'pages' => $this->PAGE,
      'field' => $FIELD,
      'searchfor' => $SEARCHFOR,
      'printerfriendly' => 'yes'
    );
    
    $HTTP_OPTIONS = array(
      'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($DATA)
      )
    );
    
    $HTTP_CONTEXT = stream_context_create($HTTP_OPTIONS);
    
    // get data from bookmark
    $HTML = file_get_contents($this->URL, false, $HTTP_CONTEXT);

    // make sure the data is OK
    if ($HTML === FALSE) {
      return 'ERROR An unexpected error occurred, data could not be pulled from bookmark.';
    }

    // load up the HTML into a DOMDocument
    $DOM01 = new DOMDocument();
    @$DOM01->loadHTML($HTML);

    // Check if there is actually anything in the HTML
    if ($DOM01->getElementsByTagName('table')->length == 0) {
      return 'ERROR An unexpected error occurred, data could not be pulled from bookmark.';
    }

    // Loop through all of the tables
    foreach ($DOM01->getElementsByTagName('table') as $TABLE) {
      //echo '<div style="background-color: #'. random_color() .';">'. $dom->saveHTML($table) .'</div>';

      $DOM02 = new DOMDocument();
      @$DOM02->loadHTML($DOM01->saveHTML($TABLE));

      $ROWS = $DOM02->getElementsByTagName('tr');

      $ITEMS = array();
      for ($cr = 0; $cr < $ROWS->length; $cr++) {
        //echo '<div style="background-color: #'. random_color() .';">'. $rows->item($j)->nodeValue .'</div>';

        $COLS = $ROWS->item($cr)->getElementsByTagName('td');

        $ITEM = array();

        for ($cc = 0; $cc < $COLS->length; $cc++) {
          $TEXT = str_replace('&nbsp;', '', (trim(trim($COLS->item($cc)->nodeValue), 'Â')));
          if ($TEXT == 'Â ') {
            $TEXT = '';
          }

          if ($cc == 1) {
            $ITEM['barcode'] = $TEXT;
          } elseif ($cc == 2) {
            $ITEM['title'] = $TEXT;
          } elseif ($cc == 3) {
            $ITEM['author'] = $TEXT;
          } elseif ($cc == 4) {
            $ITEM['call_number'] = $TEXT;
          } elseif ($cc == 5) {
            $ITEM['type'] = $TEXT;
          } elseif ($cc == 6) {
            $ITEM['copies'] = $TEXT;
          }
        }

        if ($ITEM['barcode'] != 'BARCODE') {
          $ITEMS[$ITEM['barcode']] = $ITEM;
        }
      }
    }

    return $ITEMS;
  }
  
  function detail($RECORD) {
    // Build the data you need to submit - this will get pulled from the SUBMIT on the calling page
    $DATA = array(
      'dbname' => $this->DB,
      'pages' => $this->PAGE,
      'record' => $RECORD,
      'send' => 'details',
    );
    
    $HTTP_OPTIONS = array(
      'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($DATA)
      )
    );
    
    $HTTP_CONTEXT = stream_context_create($HTTP_OPTIONS);
    
    // get data from bookmark
    $HTML = file_get_contents($this->URL, false, $HTTP_CONTEXT);

    // make sure the data is OK
    if ($HTML === FALSE) {
      return 'ERROR An unexpected error occurred, data could not be pulled from bookmark.';
    }

    // load up the HTML into a DOMDocument
    $DOM01 = new DOMDocument();
    @$DOM01->loadHTML($HTML);

    // Check if there is actually anything in the HTML
    if ($DOM01->getElementsByTagName('tr')->length == 0) {
      return 'ERROR An unexpected error occurred, data could not be pulled from bookmark.';
    }

    $ROWS = $DOM01->getElementsByTagName('tr');

    // Loop through all of the tables
      $ITEM = array();
    for ($cr = 0; $cr < $ROWS->length; $cr++) {
      //echo '<div>'. trim($ROWS->item($cr)->nodeValue) .'</div>';
      $TEXT = trim($ROWS->item($cr)->nodeValue);
      
      if (substr($TEXT, 0, 6) == 'Title:') {
        $ITEM['title'] = substr($TEXT, 6);
      } elseif (substr($TEXT, 0, 7) == 'Author:') {
        $ITEM['author'] = substr($TEXT, 7);
      } elseif (substr($TEXT, 0, 9) == 'Subjects:') {
        $ITEM['subjects'] = substr($TEXT, 9);
      } elseif (substr($TEXT, 0, 12) == 'Call number:') {
        $ITEM['call_number'] = substr($TEXT, 12);
      } elseif (substr($TEXT, 0, 11) == 'Publishing:') {
        $ITEM['publishing'] = substr($TEXT, 11);
      } elseif (substr($TEXT, 0, 10) == 'Item type:') {
        $ITEM['item_type'] = substr($TEXT, 10);
      } elseif (substr($TEXT, 0, 6) == 'Notes:') {
        $ITEM['notes'] = substr($TEXT, 6);
      } elseif (substr($TEXT, 0, 10) == 'Copy info:') {
        $COLS = $ROWS->item($cr)->getElementsByTagName('tr')->item(1)->getElementsByTagName('td');
        for ($cc = 0; $cc < $COLS->length; $cc++) {
          if ($cc == 0) {
            $ITEM['barcode'] = trim($COLS->item($cc)->nodeValue);
          } elseif ($cc == 1) {
            $ITEM['location'] = trim($COLS->item($cc)->nodeValue);
          } elseif ($cc == 2) {
            $ITEM['status'] = trim($COLS->item($cc)->nodeValue);
          } elseif ($cc == 3) {
            $ITEM['call_number'] = trim($COLS->item($cc)->nodeValue);
          }
        }
      }
    }

    return $ITEM;
    
  }
}

?>
