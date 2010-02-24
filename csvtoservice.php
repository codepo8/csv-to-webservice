<?php error_reporting(0);?>
<?php
/*
  CSV to Webservice by Christian Heilmann
  Homepage: http://isithackday.com/csvtowebservice/index.php
  Copyright (c)2010 Christian Heilmann
  Code licensed under the BSD License:
  http://wait-till-i.com/license.txt
*/
function csvtoservice($url,$options){
  $csv = get($url);
  $lines = preg_split('/\r?\n/msi',$csv);
  $columns = split(',',
              strtoLower(
                preg_replace('/\s/','',$lines[0])
              )
            );
  $colstring = join(',',$columns);

  if($options['preset']){
    $pres = $options['preset'];
    foreach(array_keys($pres) as $p){
      $presetstring .= ' and '.$p.' like "%'.$pres[$p].'%"'; 
    }
    $columns = array_diff($columns,array_keys($pres));
  }

  if($options['filter']){
    $columns = array_diff($columns,$options['filter']);
  }

  if($options['prefill']){
    foreach(array_keys($options['prefill']) as $p){
      $_GET[$p] = $options['prefill'][$p];
    }
  }
  
  if($options['rename']){
    $renames = array_keys($options['rename']); 
    foreach($columns as $k=>$c){
      foreach($renames as $r){
        if(!in_array($c,$renames)){
          $displaycolumns[$k] = $c;
        } else {
          if($c == $r){
            $displaycolumns[$k] = $options['rename'][$r];
          }
        }
      }
    }
  } else {
    $displaycolumns = $columns;
  }

  foreach($columns as $c){
    filter_input(INPUT_GET, $c, FILTER_SANITIZE_SPECIAL_CHARS);    
    $fromget[$c] = $_GET[$c];
  }

  $current = preg_replace('/.*\/+/','',$_SERVER['PHP_SELF']);

  $csvform = '<form action="'.$current.'">';
  foreach($columns as $k=>$c){
    $csvform .= '<div><label for="'.$c.'">'.
                ($options['uppercase'] ? 
                  ucfirst($displaycolumns[$k]) :
                  $displaycolumns[$k]).
                '</label>'.
                '<input type="text" id="'.$c.'" name="'.$c.
                '" value="'.$fromget[$c].'"></div>';
  }
  $csvform .= '<div id="bar"><input type="submit" name="csvsend"'.
              ' value="search"></div>';
  $csvform .= '</form>';

  if(isset($_GET['csvsend'])){
    $yql = 'select * from csv where url="'.$url.'" '.
           'and columns="'.$colstring.'"';
    foreach($columns as $c){
      if(isset($_GET[$c]) && $_GET[$c]!=''){
        $yql .= ' and '.$c.' like "%'.$_GET[$c].'%"';
      }
    }
    $yql .= $presetstring;
    $yqlquery = '<div id="yql">'.$yql.'</div>';

    $yqlendpoint = 'http://query.yahooapis.com/v1/public/yql?format=json';
    $query = $yqlendpoint.'&q='.urlencode($yql);
    $data = get($query);
    $datadecoded = json_decode($data);

    $csvtable = '<table><thead><tr>';
    foreach($columns as $k=>$c){
      $csvtable .= '<th scope="col">'.
                    ($options['uppercase'] ? 
                      ucfirst($displaycolumns[$k]) :
                      $displaycolumns[$k]).
                    '</th>';
    }
    $csvtable .= '</tr></thead><tbody>';
    if($datadecoded->query->results->row){

    foreach ($datadecoded->query->results->row as $r){
      $csvtable .= '<tr>';
      foreach($columns as $c){
        $csvtable .= '<td>'.$r->$c.'</td>';
      }
      $csvtable .= '</tr>';
    }
  } else {
    $csvtable .=  '<tr><td class="error" colspan="'.sizeof($columns).
                  '">No results found. Bummer.</td></tr>';
  }
    $csvtable .=  '</tbody></table>';
  }
  return array(
                'table'=>$csvtable,
                'form'=>$csvform,
                'query'=>$yqlquery,
                'json'=>$data
              );
}

function get($url){
  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  $output = curl_exec($ch); 
  curl_close($ch);
  return $output;
}