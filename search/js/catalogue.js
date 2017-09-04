function clear_items() {
  // clear the search history
  document.getElementById('books').innerHTML = '';
}

function get_items() {
  // get the json from the server
  document.getElementById('books').innerHTML = '.. loading.';
  var html = new XMLHttpRequest();
  html.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
              document.getElementById('books').innerHTML = '';

      var items = JSON.parse(this.responseText);
      for (var i = 0; i < items.length; i++) {
        //document.getElementById('books').innerHTML += json[i].title + '<br />';

        var text = '<div class="book-div">';
          text += '  <div class="float-left"><strong>' + items[i].title + '</strong><br>';
          text += '    <u><a href="">' + items[i].author + '</a></u><br>';
          text += items[i].type
          text += '  </div>';
          text += '  <div class="float-right text-center">' + items[i].call_number + '<br>';
          text += '    <a href="https://networksa.worldsecuresystems.com/aboriginal-resource-centre_test#" class="btn-small">ADD TO TROLEY</a>';
          text += '  </div>';
          text += '</div>';

        document.getElementById('books').innerHTML += text;
      }
    }
  }

  try {
    html.open("GET", 'http://local.damian.id.au/networksa.org.au/search.php?field=keyword&searchfor=' + document.getElementById('searchfor').value, true);
  } catch(err) {
    alert(err.message);
  }
  html.send();
  
  return false;
}

function search_author() {
  // search for an author.... manually.
}

function get_item(record) {
  var html = new XMLHttpRequest();
  html.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var json = JSON.parse(this.responseText);
      document.getElementById('json').innerHTML = json.title;
    }
  }

  html.open("GET", 'detail.php?record=3458', true);

  html.send();
}
// get the detail from the form
//document.getElementById('form').innerHTML = document.getElementById('searchfor').value;

//document.getElementById('status').innerHTML = 'Loaded.';
