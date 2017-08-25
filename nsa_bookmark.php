<?php
 /* nsa_bookmark.php
  * 2017 Creative Feed / Damian West
  * -
  * developed for NetworkSA as a public search function for the bookmark.central.sa.edu.au library system
  * -
  * Usage:
  * $NSA = new nsa_bookmark(<by_keyword>, <by_title>, <by_author>, <by_subject>, <by_series>, <search_parameters>);
  * ^^ Build a new bookmark search item with the details required
  * print_r($NSA->search());
  * ^^ Query the database and print out the search results
  */

class nsa_bookmark {
  // URL that bookmark lives
  private $URL = 'http://bookmark.central.sa.edu.au/bmcpac.exe';
  private $DATA;

  function __construct($FIELD, $SEARCHFOR) {
    // Build the data you need to submit - this will get pulled from the SUBMIT on the calling page
    $this->DATA = array(
      'dbname' => 'networksa',
      'pages' => 'networksa',
      'field' => $FIELD,
      'searchfor' => $SEARCHFOR,
      'printerfriendly' => 'yes'
    );
  }
  
  function search() {
    $HTTP_OPTIONS = array(
      'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($this->DATA)
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
          if ($cc == 1) {
            $ITEM['barcode'] = trim(trim($COLS->item($cc)->nodeValue), 'Â');
          } elseif ($cc == 2) {
            $ITEM['title'] = trim(trim($COLS->item($cc)->nodeValue), 'Â');
          } elseif ($cc == 3) {
            $ITEM['author'] = trim(trim($COLS->item($cc)->nodeValue), 'Â');
          } elseif ($cc == 4) {
            $ITEM['call_number'] = trim(trim($COLS->item($cc)->nodeValue), 'Â');
          } elseif ($cc == 5) {
            $ITEM['type'] = trim(trim($COLS->item($cc)->nodeValue), 'Â');
          } elseif ($cc == 6) {
            $ITEM['copies'] = trim(trim($COLS->item($cc)->nodeValue), 'Â');
          }

          //$item[$j][$i] = trim(trim($cols->item($i)->nodeValue), 'Â');
          //echo '<div style="color: white; background-color: black;">'.trim($cols->item($i)->nodeValue).'</div>';
        }

        if ($ITEM['barcode'] != 'BARCODE') {
          $ITEMS[$ITEM['barcode']] = $ITEM;
        }
      }
    }

    return $ITEMS;
  }
}

?>
