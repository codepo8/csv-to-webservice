<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <title>CSV to web service - creating a searchable web interface automatically from CSV files with YQL</title>
  <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">
  <link rel="stylesheet" href="styles.css" type="text/css">
</head>
<body>
<div id="doc" class="yui-t7">
  <div id="hd" role="banner">
    <h1>CSV to web service</h1>
    <ul><li><a href="index.php">Index</a></li><li><a href="http://github.com/codepo8/csv-to-webservice">Download on GitHub</a></li></ul>
  </div>
  <div id="bd" role="main">
    <p>This is a demo page for the <code>csvtoservice.php</code> script that uses <a href="http://developer.yahoo.com/yql">YQL</a> to convert CSV resources to web interfaces.</p>

    <h2>What you can do with this</h2>
    <p>This script allows you to get any CSV file from the internet and turn it into a web interface that empowers users to search and filter the data.</p>
    <p>The following is an example, try it out. Simply hit the search button and see the results.</p>
    <div id="example">
    <h3>Olympic Winter Medals Albertville 1992</h3>
    <p>Simply enter your search criteria in the appropriate field and submit the form.</p>
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
    echo '<h4>Filters</h4>';
    echo $content['form'];

    // show the table
    if($content['table']){
      echo '<h4>Results</h4>';
      echo $content['table'];
    }

  }
?>
</div>
<h2>The code</h2>
<p>The above search form and table result is powered by a simple dataset: <a href="http://winterolympicsmedals.com/medals.csv">http://winterolympicsmedals.com/medals.csv</a>. All you need to convert it to what you see above is the following code in your PHP documents:</p>

<pre><code>&lt;?php 
  include(&#x27;csvtoservice.php&#x27;);
  $content = csvtoservice(
    &#x27;http://winterolympicsmedals.com/medals.csv&#x27;,
    array(
      &#x27;filter&#x27;=&gt; array(
        &#x27;eventgender&#x27;,
        &#x27;city&#x27;
      ),
      &#x27;rename&#x27;=&gt; array(
        &#x27;noc&#x27;=&gt;&#x27;country&#x27;
      ),
      &#x27;preset&#x27;=&gt; array(
        &#x27;year&#x27;=&gt; &#x27;1992&#x27;
      ),
      &#x27;prefill&#x27;=&gt; array(
        &#x27;discipline&#x27;=&gt; &#x27;Alpine Skiing&#x27;,
        &#x27;medal&#x27;=&gt; &#x27;Gold&#x27;
      ),
      &#x27;uppercase&#x27;=&gt;true
    )
  );
  if($content){

    if($content[&#x27;form&#x27;]){
      echo &#x27;&lt;h4&gt;Filters&lt;/h4&gt;&#x27;;
      echo $content[&#x27;form&#x27;];
    }

    if($content[&#x27;table&#x27;]){
      echo &#x27;&lt;h4&gt;Results&lt;/h4&gt;&#x27;;
      echo $content[&#x27;table&#x27;];
    }

  }
?&gt;</code></pre>

<p>This is already a complex example, the simplest way to show a form and a result table for any CSV file is the following:</p>

<pre><code>&lt;?php 
  include(&#x27;csvtoservice.php&#x27;);
  $content = csvtoservice(&#x27;http://winterolympicsmedals.com/medals.csv&#x27;);
  if($content){

    if($content[&#x27;form&#x27;]){
      echo &#x27;&lt;h4&gt;Filters&lt;/h4&gt;&#x27;;
      echo $content[&#x27;form&#x27;];
    }

    if($content[&#x27;table&#x27;]){
      echo &#x27;&lt;h4&gt;Results&lt;/h4&gt;&#x27;;
      echo $content[&#x27;table&#x27;];
    }

  }
?&gt;</code></pre>

<p>You can see this <a href="simplest-example.php">in action here</a> and it is part of the source code on GitHub.</p>

<h2>The parameters and options</h2>

<p>In essence, all you need to provide is a URL that points to a CSV file and the script does the rest. You assign a variable to the main function that will get the HTML as properties. For example:</p>

<pre><code>$myservice = csvtoservice('http://winterolympicsmedals.com/medals.csv');</code></pre>

<p>The returned properties will be:</p>

<ul>
  <li><code>$myservice['form']</code> - the HTML form with all possible fields contained in the dataset.</li>
  <li><code>$myservice['table']</code> - the data table of the information returned by the form submission - this will only show up once the form is submitted.</li>
  <li><code>$myservice['json']</code> - the data in raw JSON format (for debugging).</li>
  <li><code>$myservice['yql']</code> - the YQL statement (for debugging).</li>
</ul>

<p>You can see all of the information in the <a href="example-with-logging.php">example with logging</a>.</p>

<p>If you want to tweak the outcome of the form and the table and you want to change the data names or remove parts of it you can set an options array:</p>

<pre><code>$myservice = csvtoservice(
  'http://winterolympicsmedals.com/medals.csv',
  array(
    'filter' => array(<em>fieldnames</em>),
    'rename'=> array(
      '<em>field</em>'=>'<em>new name</em>',
      '<em>field2</em>'=>'<em>new name 2</em>'
    ),
    'preset'=> array(
      '<em>field</em>'=>'<em>preset value</em>',
      '<em>field2</em>'=>'<em>preset value 2</em>'
    ),
    'prefill'=> array(
      '<em>field</em>'=> '<em>value</em>',
      '<em>field2</em>'=> '<em>value 2</em>',
    ),
    'uppercase'=><em>Boolean</em> 
  )
);</code></pre>

<ul>
  <li><code>rename</code> allows you to rename fields. In the above example the country who won the medals was defined as <em>NOC</em>, which makes sense, but reads much better as <em>country</em>.</li>
  <li><code>filter</code> contains an array of fields to not show in the form or the table. This allows you to get rid of some parts of the data.</li>
  <li><code>preset</code> is an array of fields to preset with a hard value. These fields will be part of the query of the data but will not be added to the form or displayed. This allows you to pre-filter the data. In the above example this was the year of the games.</li>
  <li><code>prefill</code> is an array of fields to pre-fill the form with in case you want to give the end user a hint what they can search for.</li>
  <li><code>uppercase</code> is an boolean value if the script should uppercase the first letter of the field name or not ("City" instead of "city").</li>
</ul>
<p>That's it, really... Have fun!</p>
  </div>
<div id="ft" role="contentinfo"><p>Written by <a href="http://wait-till-i.com">Chris Heilmann</a>, powered by <a href="http://developer.yahoo.com/yql/">YQL</a> - demo csv by the <a href="http://www.guardian.co.uk/news/datablog">Guardian Data Blog</a>.</p></div>
</div>
</body>
</html>
