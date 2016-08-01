<?php

// include the requests libary
  include('./Requests/library/Requests.php');
Requests::register_autoloader();

// target the desired website

//$root = 'http://lunarsettlement.org/';
//$url = 'http://lunarsettlementindex.org/display/LSI/Biological+Support';
//$url = 'http://lunarsettlementindex.org/display/LSI/X-Rays';
$url = 'http://lunarsettlementindex.org/display/LSI/Human+Health+Risk+of+Long-term+Low+Gravity';
// $url = 'http://lunarsettlementindex.org/display/LSI/Bone+Mass+Monitoring';
//$url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Environment';
//$url = 'http://lunarsettlementindex.org/display/LSI/Communications';
//$url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Settlement+Roadblocks';
//$url = 'http://lunarsettlementindex.org/display/LSI/Roadblock+Categories';

// set the request headers
$headers = array('Accept' => 'text/html');

// perform a http get with requests
$response = Requests::get($url,$headers);

// save the response to a variable
$source_html = $response->body;

$root = 'http://lunarsettlementindex.org';
// global storage string...
$global_result = '';

// Pull out the title from the RDF
// [1] echo to pull into global turtle file
$global_result = matchtitleinpageRDF($url,$source_html);

// match the authorship information
// [2] echo to pull into global turtle file
$global_result = $global_result.matchauthor($url,$root,$source_html);

$splarray = array();
$tharray = array();

$tabletype = array('List of Roadblocks' => array('Description','List of Roadblocks'),
               'Roadblock' => array('Description','Roadblock Type','Priority (1-5)'),
               'Solution Category' => array('Solution Description','Cost Drivers','Average Est Investment Cost','Average Est Time to Maturity','Commercial Status:','Related Industries/Fields','Preliminary Tech Required','Est Time to Maturity (in years)','Funding Opportunities'),
'Specific Solution' => array('Current Player(s)','Progress Status','Est Investment Cost',
'Est Time to Maturity','Component Systems'));


$local_table_result = '';
$local_table_result_s1 = '';
$local_table_result_s2 = '';
$local_table_result_s3 = '';

$temparray = array();
$tabletypekey = '';

// return the roadblocks...
if(containstable(matchbody($source_html)) == 1) {
  $splarray = preg_split('/===break===/',parsetable(matchbody($source_html)));
  array_pop($splarray);

// Use this code to match the table row descriptions. For now, hardcoding for the Table for Roadblocks. 

     foreach ($splarray as $key => $value) {
        $splarrayth = preg_split('/===break===/',captureth($splarray[$key])); 
        array_push($tharray,$splarrayth[0]);
      }

     foreach($tabletype as $key => $value) {
        foreach($value as $key2 => $valuetwo) {
            array_push($temparray,$value[$key2]);
      }
         $result = array_diff($temparray,$tharray);
       if($result == NULL) {
          $tabletypekey = $key;
        }
  $temparray = [];
}
  echo 'the table type is: '.$tabletypekey."\n";
  echo 'the th array is'."\n";
  print_r($tharray);

/// For the case where the table type is a List of Roadblocks:
if($tabletypekey == 'List of Roadblocks') {        
 foreach ($splarray as $key => $value) {

        // Find if the table row contains a description of the Roadblocks,
        // then capture and display its contents  
          if(preg_match('/Description/',$splarray[$key],$matches)) {

             $comment = matchdescription("{$splarray[$key]}");
             // [3] echo to pull into global turtle file
             $local_table_result_s1 = $local_table_result_s1.'<'.$url.'> rdfs:comment "'.$comment.'"'."\n";
          }
        
       // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/List of Roadblocks/',$splarray[$key],$matches)) {

             $splarraytd = preg_split('/===break===/',capturetd($splarray[$key]));
          }
          
      }
      
         array_pop($splarraytd);   
 
        foreach ($splarraytd as $key => $value) {
           $spltd = preg_split('/===break===/',scrapetd($root,$splarraytd[$key]));
         array_pop($spltd);
//         print_r($spltd);
        }
       
       // print out the results of the contents of the roadblocks
        foreach($spltd as $key => $value) {
          // [4] echo to pull into global turtle file
           $local_table_result_s2 = $local_table_result_s2.'<'.$url.'> '.$spltd[$key];
        }

       $local_table_result = $local_table_result_s1.$local_table_result_s2;  
  }

// $grabberarray = array();

  if($tabletypekey == 'Roadblock') {
    foreach ($splarray as $key => $value) {
    //   echo $splarray[$key]."\n";

     // Find if the table row contains a description of the Roadblock,
        // then capture and display its contents  
          if(preg_match('/Description/',$splarray[$key],$matches)) {

             $comment = matchdescription("{$splarray[$key]}");
             // [3] echo to pull into global turtle file
             $local_table_result_s1 = $local_table_result_s1.'<'.$url.'> rdfs:comment "'.$comment.'"'."\n";
          }
     //  echo 'the roadblock description is'.$local_table_result_s1;
   
        // Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Roadblock Type/',$splarray[$key],$matches)) {
           //  echo 'Hello Priority'."\n";
            $splarraytd = preg_split('/===break===/',capturetd($splarray[$key]));
            array_pop($splarraytd);
           // echo 'spl array td';
          //  print_r($splarraytd);

            foreach ($splarraytd as $key => $value) {
             
              $spltd = preg_split('/===break===/',scrapetd($root,$splarraytd[$key]));
            //  echo 'spltd'."\n";
              array_pop($spltd);
            //  print_r($spltd);
              if($spltd !== NULL) {
                  
                  foreach($spltd as $key => $value) {
                 // [4] echo to pull into global turtle file
                 $local_table_result_s2 = $local_table_result_s2.
                                       '<'.$url.'> '.$spltd[$key];
               } 
                
             } 
              if($spltd == NULL) {
              //  echo $splarraytd[$key].'is a test';
                $local_table_result_s2 = strip_tags($splarraytd[$key]);
              }
              
              } 
             //  $anotherresult = matchtdtable($root,$splarray[$key]);  
//              echo 'this is another result '.matchtdtable($root,$splarray[$key]);
//              echo 'tis is the end of the result'."\n";
           }
             //  echo $local_table_result_s2.'is awesome:'."\n";
           //}

              //  $anotherresult = matchtdtable($root,$splarray[$key]);  
//             echo 'this is another result '.matchtdtable($root,$splarray[$key]);
//              echo $splarray[$key].'....'."\n";
//              echo 'tis is the end of the result'."\n";


 //  echo '<'.$url.'> '.'lsi:roadblockType '.'"'.$local_table_result_s2.'" .'."\n";
//  echo 'this is another result '.$anotherresult."\n";

   // Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Priority \(1\-5\)/',$splarray[$key],$matches)) {
           //  echo 'Hello Priority'."\n";
            $splarraytd = preg_split('/===break===/',capturetd($splarray[$key]));
            array_pop($splarraytd);
          //  print_r($splarraytd);

          
            foreach ($splarraytd as $key => $value) {

              $spltd = preg_split('/===break===/',scrapetd($root,$splarraytd[$key]));
            //  echo 'spltd'."\n";
              array_pop($spltd);
           //   print_r($spltd);
              if($spltd !== NULL) {

                  foreach($spltd as $key => $value) {
                 // [4] echo to pull into global turtle file
                 $local_table_result_s3 = $local_table_result_s2.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
             //   echo $splarraytd[$key].'is a test';
                $local_table_result_s3 = strip_tags($splarraytd[$key]);
              }

              } 




          }

// comment this out temporarily
  //   echo '<'.$url.'>'.'lsi:priority "'.$local_table_result_s3.'"^^xsd:integer .'."\n";

     $local_table_result = $local_table_result_s1.
          '<'.$url.'> '.'lsi:roadblockType '.'"'.$local_table_result_s2.'" .'."\n".
          '<'.$url.'>'.'lsi:priority "'.$local_table_result_s3.'"^^xsd:integer .'."\n";
    }

   //   array_pop($splarraytd);

  //  echo 'the roadblock description is'.$local_table_result_s1."\n";
  //  echo 'the roadblock type is'.$local_table_result_s2."\n";
  //  echo 'the roadblock priority is'.$local_table_result_s3."\n";
  //  echo 'break'."\n";
  //  echo $local_table_result;

  }    

  if($tabletypekey == 'Solution Category') {
     echo '==========This is a Solution=========='."\n";
  } 

  if($tabletypekey == 'Specific Solution') {
     echo 'This is the Specific Solution'."\n";
  }


} else {
  // Alternative [3],[4] echo to pull into the turtle file
  $local_table_result = $local_table_result.matchlist($root,matchbody($source_html));

} 

//echo  'the local table result'.$local_table_result."\n";

$global_result = $global_result.$local_table_result;
echo $global_result;

function matchtdtable($root,$arrayelement) {
 $local_result = '';  
 if(preg_match('/Roadblock Type/',$arrayelement,$matches)) {
           //  echo 'Hello Priority'."\n";
            $splarraytd = preg_split('/===break===/',capturetd($arrayelement));
            array_pop($splarraytd);
            print_r($splarraytd);


            foreach ($splarraytd as $key => $value) {

              $spltd = preg_split('/===break===/',scrapetd($root,$splarraytd[$key]));
            //  echo 'spltd'."\n";
              array_pop($spltd);
           //   print_r($spltd);
              if($spltd !== NULL) {

                  foreach($spltd as $key => $value) {
                 // [4] echo to pull into global turtle file
                 $local_result = $local_table_result_s2.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
             //   echo $splarraytd[$key].'is a test';
                $local_result = strip_tags($splarraytd[$key]);
              }

              } 

   }

     return $local_result;
}


function matchbody($string) {
    $srch = "#"     // start pattern
    . "<div id=\"main-content\" class=\"wiki-content\">"    // find the div
    . "(?<argument>.*)"        // get the string we want as 'argument'
    . "</rdf:RDF>"  // end div and spaces
    . "#siU";   // end string and end pattern
  if(preg_match($srch, $string, $match)) {
   $result = $match[1];
  } else {
   $result = 'No joy!';   
  }
   return $result;
 }

function matchlist($root,$argument) {
   $result = '';
   $searchlist = "#"
              ."<li><a href=\"(?<url>.*)\">(?<name>.*)</a></li>"
              ."#siU";
   if(preg_match_all($searchlist,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {


             $result = $result.'<'.$root.$match['url'].'> a lsi:RoadblockCategory .'."\n".
                       '<'.$root.$match['url'].'> dct:title '.'"'.$match['name'].'" .'."\n";

     }
   } else {
     $result = 'No joy!';
   }
   return $result;
}

function containstable($argument) {
  $result = '';
  if(preg_match('/<table class="confluenceTable">/', $argument, $matches)) {
    $result = true;
  } else {
    $result = false;
 }
  return $result;
}

// Now write a function about how to match the things you want in a table..
function parsetable($argument) {
  // $result = array();
   $result = '';
   $srch = "#"     // start pattern
    . "<tr"    // find the div
    . "(?<argument>.*)"        // get the string we want as 'argument'
    . "</tr>"  // end div and spaces
    . "#siU";   // end string and end pattern

if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
         $result = $result."{$match['argument']}===break===";
      }
   } else {
     $result = 'No joy!';
   }
   return $result;



}

function captureth($argument) {
   $result = '';
      $srch = "#"     // start pattern
    . "<th.*class=\".*\">"    // find the div
    . "(?<argument>.*)"        // get the string we want as 'argument'
    . "</th>"  // end div and spaces
    . "#siU";   // end string and end pattern


if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
         $result = $result."{$match['argument']}===break===";
         $result = strip_tags($result);
       }
   } else {
     $result = 'No joy!';
   }
   return $result;

}

function capturetd($argument) {  
   $result = '';
   $srch = "#"     // start pattern
    . "<td.*class=\".*\">"    // find the div
    . "(?<argument>.*)"        // get the string we want as 'argument'
    . "</td>"  // end div and spaces
    . "#siU";   // end string and end pattern


if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
       $result = $result."{$match['argument']}===break===";
       }
   } else {
     $result = 'No joy!';
   }
   return $result;


}

function scrapetd($root,$argument) {
//  $root = 'http://lunarsettlement.org';
  $result = '';
  $srch = "#"
          . "<div class=\"details\">.*"
          . "<a href=\"(?<url>.*)\">(?<name>.*)</a>.*"
          . "<div class=\"label-details\">(?<contents>.*)</div>.*</div>"
          . "#siU";
  
  if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
         $striphtmlfromtag = scrapetags($root,"{$match['url']}","{$match['contents']}");
       $result = $result."lsi:roadblock <".$root."{$match['url']}> .\n"."<".$root."{$match['url']}> dct:title \"{$match['name']}\" .\n{$striphtmlfromtag}===break===";

       }
   } else {
     $result = 'No joy!';
   }
   return $result;
}

function scrapetags($root,$url,$body) {
//   $root = 'http://lunarsettlement.org';
   $result = '';
   $srch = "#"
           ."href=\"(?<matchingurl>.*)\" "
           . "rel=\"tag\">"
           ."(?<matching>.*)"
           ."</a>"
           ."#siU";
   if(preg_match_all($srch,$body,$matches, PREG_SET_ORDER)) {
        foreach ($matches as $key => $match) {
            $result = $result.
"<".$root."{$match['matchingurl']}> dct:title "."\"{$match['matching']}\" .\n".
"<".$root.$url."> schema:isRelatedTo <".$root."{$match['matchingurl']}> .\n";
        }
   } else {
     $result = 'No joy!';
 }
  return $result;
}

function matchdescription($argument) {
   $result = '';
   $srch = "#"
           ."data-macro-name=\"text-data\">"
           ."(?<description>.*)"
           ."</p>"
           ."#siU";
    if(preg_match($srch, $argument, $match)) {
      $result = $match['description'];
    } else {
      $result = 'No joy!';
    }
   return $result;
}

function matchtitleinpageRDF($url,$argument) {
   $result = '';

   $srch = "#"
           ."<!--.*<rdf:RDF"
           .".*rdf:about=\""
           ."(?<rdfcontent>.*)"
           ."\".*"
           ."dc:title=\""
           ."(?<pagetitle>.*)"
           ."\".*" 
           ."</rdf:RDF>"
           ."#siU";
// match rdf:about and dc:title
        if(preg_match($srch, $argument, $match)) {
        $result = '<'.$url.'>'.' dc:title "'.$match['pagetitle']."\" ."."\n";
    } else {
      $result = 'No joy!';
    }
   return $result;       
}


function matchauthor($url,$root,$argument) {
     $months = array('Jan' => '01',
                 'Feb' => '02',
                 'Mar' => '03',
                 'Apr' => '04',
                 'May' => '05',
                 'Jun' => '06',
                 'Jul' => '07',
                 'Aug' => '08',
                 'Sep' => '09',
                 'Oct' => '10',
                 'Nov' => '11',
                 'Dec' => '12');

     $datesrch = "#"
         ."(?<month>.*)"
         ." "
         ."(?<day>.*)"
         .", "
         ."(?<year>.*)"
         ."$"
         ."#siU";

   $result = '';
   $srch = "#"
           ."<li class=\"page-metadata-modification-info\">"
           ."(?<author>.*)"
           ."</li>"
           ."#siU";
   $srchinside = '#'
                 .'Created by '
                 .'(?<creator>.*)'
                 .',.*last modified on '
                 .'(?<mod>.*)$'
                 .'#siU';
   $srchinsidetwo = "#"
                  ."<span class='author'>.*"
                  ."<a href=\""
                  ."(?<webid>.*)"
                  ."\".*>"
                  ."(?<name>.*)"
                  ."</a>"
                  ."#siU";
   
   $srchinsidethree = "#"
                  ."<span class='editor'>.*"
                  ."<a href=\""
                  ."(?<webide>.*)"
                  ."\".*>"
                  ."(?<named>.*)"
                  ."</a>"
                  ."#siU";

   $srchinsidefour = "#"
                  ."<a class='last-modified'.*>"
                  ."(?<modified>.*)"
                  ."</a>"
                  ."#siU";

      $local_result = '';
    if(preg_match($srch, $argument, $match)) {
       $result = $match['author'];
       $wohtml = strip_tags($result); 
  
    preg_match($srchinsidefour,$result,$matchfive);

      $resultmo = '';

        if(preg_match_all($datesrch,$matchfive['modified'],$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
           $resultmo = $resultmo."{$match['year']}-{$match['month']}-{$match['day']}";
       }
     } else {
       $resultmo = 'No joy!';
     }

      foreach($months as $key => $value) {
    if(preg_match('/'.preg_quote($key).'/',$result,$matches)) {
      $respect = preg_replace('/'.preg_quote($key).'/',$months[$key],$resultmo);
    }
  }


    } else {
       $result = 'No joy!';
    }


//echo 'this was the result'.$result."\n";

// .........
    if(preg_match($srchinside,$wohtml,$matchtwo)) {
    //    echo 'match two s '.$matchtwo['creator'].' modification is '.$matchtwo['mod'];
  ///      print_r($matchtwo); 
      }
    if(preg_match($srchinsidetwo,$result,$matchthree)) {
       
      $local_result = $local_result.'<'.$url.'>'.' prov:wasAttributedTo <'.$root.preg_replace('/[ \n]*/','',$matchthree['webid'])."> . \n".
      '<'.$root.preg_replace('/[ \n]*/','',$matchthree['webid']).'> foaf:name "'.$matchthree['name'].'" .'."\n".
      '<'.$url.'>'.' prov:qualifiedAttribution ['."\n".'a prov:Attribution;'."\n".
      'prov:agent <'.$root.preg_replace('/[ \n]*/','',$matchthree['webid'])."> ;\n".'prov:hadRole lsi:author ] .'."\n";

      }

     if(preg_match($srchinsidethree,$result,$matchfour)) {
     
       $local_result = $local_result.'<'.$url.'>'.' prov:wasAttributedTo <'.$root.preg_replace('/[ \n]*/','',$matchfour['webide'])."> . \n".
       '<'.$root.preg_replace('/[ \n]*/','',$matchfour['webide']).'> foaf:name "'.$matchfour['named'].'" .'."\n".
       '<'.$url.'>'.' prov:qualifiedAttribution ['."\n".'a prov:Attribution;'."\n".
       'prov:agent <'.$root.preg_replace('/[ \n]*/','',$matchfour['webide'])."> ;\n".'prov:hadRole lsi:editor ] .'."\n"; 
      } 

      $local_result = $local_result.'<'.$url.'>'.' lsi:lastmodified '.'"'.$respect.'"^^xsd:dateTime . '."\n";

      return $local_result;
} 
