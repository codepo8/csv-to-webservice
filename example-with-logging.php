<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <title>Logging and properties example of csvtoservice.php</title>
  <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">
  <link rel="stylesheet" href="styles.css" type="text/css">
</head>
<body>
<div id="doc" class="yui-t7">
  <div id="hd" role="banner">
    <h1>Logging and properties example of csvtoservice.php</h1>
    <ul><li><a href="index.php">Index</a></li><li><a href="http://github.com/codepo8/csv-to-webservice">Download on GitHub</a></li></ul>
  </div>
  <div id="bd" role="main">
<?php 
  include('csvtoservice.php');
  $content = csvtoservice(
    'http://winterolympicsmedals.com/medals.csv',
    array(
      'filter'=> array('eventgender','city'),
      'rename'=> array(
        'noc'=>'country'
      ),
      'preset'=> array(
        'year'=> '1992'
      ),
      'prefill'=> array(
        'discipline'=> 'Alpine Skiing',
        'medal'=> 'Gold'
      ),
      'uppercase'=>true
    )
 );

  // if it could be loaded and parsed...
  if($content){

    // show the form
    if($content['form']){
      echo '<h2>Filters</h2>';
      echo $content['form'];
    }
    // show the query (debugging, really)
    if($content['query']){
      echo '<h2>YQL request</h2>';
      echo $content['query'];
    }

    // show the table
    if($content['table']){
      echo '<h2>Results</h2>';
      echo $content['table'];
    }

    // display the JSON (debugging, really) 
    if($content['json']){
      echo '<h2>Results (json)</h2>';
      echo $content['json'];
    }
  }
?>
?>
  </div>
<div id="ft" role="contentinfo"><p>Written by <a href="http://wait-till-i.com">Chris Heilmann</a>, powered by <a href="http://developer.yahoo.com/yql/">YQL</a> - demo csv by the <a href="http://www.guardian.co.uk/news/datablog">Guardian Data Blog</a>.</p></div>
</div>
</body>
</html>