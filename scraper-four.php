<?php
  include('./Requests/library/Requests.php');
Requests::register_autoloader();

//$root = 'http://lunarsettlement.org/';
//$url = 'http://lunarsettlementindex.org/display/LSI/Biological+Support';
$url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Environment';
//$url = 'http://lunarsettlementindex.org/display/LSI/Communications';
//$url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Settlement+Roadblocks';
//$url = 'http://lunarsettlementindex.org/display/LSI/Roadblock+Categories';
$headers = array('Accept' => 'text/html');
$response = Requests::get($url,$headers);

$source_html = $response->body;

echo matchRDF($source_html);
echo strip_tags(matchauthor($source_html));
//echo 'the matchbody is'.matchbody($source_html);

/*
echo 'the match list is'.matchlist(matchbody($source_html));
*/

//echo 'do I contain a table'.containstable(matchbody($source_html));

$splarray = array();
$tharray = array();

if(containstable(matchbody($source_html)) == 1) {
//  echo 'yes=======================================';
//  echo 'psrse s'.parsetable(matchbody($source_html));
  $splarray = preg_split('/===break===/',parsetable(matchbody($source_html)));
  array_pop($splarray);
//  print_r($splarray);
  foreach ($splarray as $key => $value) {
     $splarrayth = preg_split('/===break===/',captureth($splarray[$key])); 
  //   echo 'this is the th';
  //   print_r($splarrayth);
     array_push($tharray,$splarrayth[0]);
  }

/*
   foreach ($splarrayth as $key => $value) {
     if($splarrayth[$key] == "Description") {
        echo "It is a Description";
/*       echo "It is a description";
       $desc = $splarrayth[$key];
       echo strip_tags($desc);
       echo "It is a description".$desc; */
//     }

  /*   if($splarrayth[$key] == "List of Roadblocks") {
       echo "It is a roadblock";
  */
          // split between the description and the list of roadblocks...
       foreach ($splarray as $key => $value) {
          /*foreach($tharray as $keytwo => $valuetwo) {

           


           if(preg_match('/'.preg_quote("{$tharray[$keytwo]}").'/',$splarray[$key],$matches)) {   
               echo $matches[0].' is in "'.$splarray[$key].'" .'."\n";
           }
          } */
          
          if(preg_match('/Description/',$splarray[$key],$matches)) {

//             echo 'The piggy Description is in "'.$splarray[$key].'" .'."\n";
           //  $comment = strip_tags("{$splarray[$key]}");
             $comment = matchdescription("{$splarray[$key]}");
             echo 'rdfs:comment "'.$comment.'"'."\n";
//             echo "\n".'end of the strip';
          }
        

          if(preg_match('/List of Roadblocks/',$splarray[$key],$matches)) {

//             echo 'The elephant Description is in "'.$splarray[$key].'" .'."\n";
             $splarraytd = preg_split('/===break===/',capturetd($splarray[$key]));
//             $spltd = preg_split('/===break===/',"{$splarraytd}");
          }
 
            // print_r($splarraytd);
         /*  if(preg_match('/Description/', $string, $match) {
           //  echo 'I am a match';
          } */        
        //  echo 'The'.$key.'crazy match is'.$splarray[$key];
        //  $splarraytd = preg_split('/===break===/',capturetd($splarray[$key]));
//        echo 'this is the descrition';

     //  print_r($splarraytd);
          
      }
      
         array_pop($splarraytd);   
//        echo 'the first td is'.$splarray[0];
 
        foreach ($splarraytd as $key => $value) {
//         echo 'beginning of spltd'."\n";
//           $spltd = preg_split('/===break===/',scrapetd($splarray[$key]));
           $spltd = preg_split('/===break===/',scrapetd($splarraytd[$key]));
         array_pop($spltd);
         print_r($spltd);
        }
    
   //  }

 //  }

/*
// split between the description and the list of roadblocks...
  foreach ($splarray as $key => $value) {
     $splarraytd = preg_split('/===break===/',capturetd($splarray[$key]));
     echo 'this is the descrition';
     print_r($splarraytd);
  }

  foreach ($splarraytd as $key => $value) {
      $spltd = preg_split('/===break===/',scrapetd($splarray[$key]));
       print_r($spltd);

  }
*/
}




function matchbody($string) {
    $srch = "#"     // start pattern
    // . "^.*"   // start string and leading text
    . "<div id=\"main-content\" class=\"wiki-content\">"    // find the div
    . "(?<argument>.*)"        // get the string we want as 'argument'
    . "</rdf:RDF>"  // end div and spaces
    // . ".*"    // whatever is left
    . "#siU";   // end string and end pattern
  if(preg_match($srch, $string, $match)) {
   $result = $match[1];
  } else {
   $result = 'No joy!';   
  }
   return $result;
 }

function matchlist($argument) {
   $result = '';
   $searchlist = "#"
              ."<li><a href=\"(?<url>.*)\">(?<name>.*)</a></li>"
              ."#siU";
   if(preg_match_all($searchlist,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
           $result = $result."Match $key: <a href=\"{$match['url']}\">{$match['name']}</a>\n";
       }
   } else {
     $result = 'No joy!';
   }
   return $result;
}

function containstable($argument) {
  $result = '';
  if(preg_match('/<table class="confluenceTable">/', $argument, $matches)) {
//    $result = $matches[0]; 
    $result = true;
  } else {
    $result = false;
  //  $result = 'No joy!';   
 }
  return $result;
}

// Now write a function about how to match the things you want in a table..
function parsetable($argument) {
  // $result = array();
   $result = '';
   $srch = "#"     // start pattern
    // . "^.*"   // start string and leading text
    . "<tr"    // find the div
    . "(?<argument>.*)"        // get the string we want as 'argument'
    . "</tr>"  // end div and spaces
    // . ".*"    // whatever is left
    . "#siU";   // end string and end pattern

if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
      //    captureth($match['argument']);  
      //    array_push($result,{$match['argument']}); 
    //   $result = $result."Match $key: {$match['argument']}===break===";
         $result = $result."{$match['argument']}===break===";
//         $rev = "{$match['argument']}";
//        preg_match('/<th.*<\/th>/',$rev,$matches);
  //       echo 'revto is'.$matches[0];
  //       echo 'rev is'.$rev;
  //       echo 'revto_is'.captureth($rev).'=====the th======';
//         echo 'revtd_is'.capturetd($rev).'hello';
         
      //     $result = $result."Match $key: captureth({$match['argument']})\n"; 
      }
   } else {
    // $result = NULL;
     $result = 'No joy!';
   }
//   $result = strip_tags($result);
   return $result;



}

function captureth($argument) {
   $result = '';
      $srch = "#"     // start pattern
    // . "^.*"   // start string and leading text
    . "<th.*class=\".*\">"    // find the div
    . "(?<argument>.*)"        // get the string we want as 'argument'
    . "</th>"  // end div and spaces
    // . ".*"    // whatever is left
    . "#siU";   // end string and end pattern

/*
if(preg_match($srch,$argument,$matches)) {
     $result = $matches['argument']."   ";
   } else {
      $result = 'No goy!';
   }
   return $result;
*/

if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
      //     $result = $result."Match $key: {$match['argument']}\n";
         $result = $result."{$match['argument']}===break===";
       }
   } else {
     $result = 'No joy!';
   }
   return $result;

}

function capturetd($argument) {  
   $result = '';
   $srch = "#"     // start pattern
    // . "^.*"   // start string and leading text
    . "<td.*class=\".*\">"    // find the div
    . "(?<argument>.*)"        // get the string we want as 'argument'
    . "</td>"  // end div and spaces
    // . ".*"    // whatever is left
    . "#siU";   // end string and end pattern

/*
if(preg_match($srch,$argument,$matches)) {
     $result = $matches['argument']."   ";
   } else {
      $result = 'No goy!';
   }
   return $result;
*/

if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
     //      $result = $result."Match $key: {$match['argument']}\n";
       $result = $result."{$match['argument']}===break===";
       }
   } else {
     $result = 'No joy!';
   }
   return $result;


}

function scrapetd($argument) {
  $root = 'http://lunarsettlement.org';
  $result = '';
  $srch = "#"
          . "<div class=\"details\">.*"
          . "<a href=\"(?<url>.*)\">(?<name>.*)</a>.*"
          . "<div class=\"label-details\">(?<contents>.*)</div>.*</div>"
//          . "<div class =\"label-details\">(?<contents>.*)</div></div>"
          . "#siU";
  
  if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
     //      $result = $result."Match $key: {$match['argument']}\n";
    //   $striphtmlfromtag = strip_tags("{$match['contents']}");
         $striphtmlfromtag = scrapetags("{$match['url']}","{$match['contents']}");
       $result = $result."lsi:roadblock <".$root."{$match['url']}> .\n"."<".$root."{$match['url']}> dct:title \"{$match['name']}\" .\n{$striphtmlfromtag}===break===";

//    $result = $result."{$match['url']}..{$match['name']}..{$match['contents']}===break===";
       }
   } else {
     $result = 'No joy!';
   }
   return $result;
}

function scrapetags($url,$body) {
   $root = 'http://lunarsettlement.org';
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
//        print_r($match);
      $result = $match['description'];
//      echo 'the result for the gglue'.$result;
    } else {
      $result = 'No joy!';
    }
   return $result;
}

function matchRDF($argument) {
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
 
/*
   $srch = "#"
           ."<!--.*<rdf:RDF"
           ."(?<rdfcontent>.*)"
           ."</rdf:RDF>"
           ."#siU";
*/
// match rdf:about and dc:title
        if(preg_match($srch, $argument, $match)) {
//        print_r($match);
      $result = '<'.$match['rdfcontent'].'>'.' dc:title "'.$match['pagetitle']."\" .";
//      echo 'the result for the gglue'.$result;
    } else {
      $result = 'No joy!';
    }
   return $result;       
}

// <li class="page-metadata-modification-info">
// </li>

function matchauthor($argument) {
     $months = array('January' => '01',
                 'February' => '02',
                 'March' => '03',
                 'April' => '04',
                 'May' => '05',
                 'June' => '06',
                 'July' => '07',
                 'August' => '08',
                 'September' => '09',
                 'October' => '10',
                 'November' => '11',
                 'December' => '12');

     $datesrch = "#"
         ."(?<month>.*)"
         ." "
         ."(?<day>.*)"
         .", "
         ."(?<year>.*)"
         ."$"
         ."#siU";
/*
     if(preg_match_all($datesrch,$matchfive['modified'],$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
           $result = $result."{$match['year']}-{$match['month']}-{$match['day']}";
       }
     } else {
       $result = 'No joy!';
     }
*/

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

    $url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Environment';
    $root = 'http://lunarsettlementindex.org';
    if(preg_match($srch, $argument, $match)) {
       $result = $match['author'];
       $wohtml = strip_tags($result); 
       echo $wohtml; 
    if(preg_match($srchinside,$wohtml,$matchtwo)) {
        echo 'match two s '.$matchtwo['creator'].' modification is '.$matchtwo['mod'];
        print_r($matchtwo); 
      }
    if(preg_match($srchinsidetwo,$result,$matchthree)) {
        echo '<'.$url.'>'.' prov:wasAttributedTo <'.$root.preg_replace('/[ \n]*/','',$matchthree['webid'])."> . \n";
        echo  '<'.$root.preg_replace('/[ \n]*/','',$matchthree['webid']).'> foaf:name "'.$matchthree['name'].'" .'."\n";
        echo 'prov:qualifiedAttribution ['."\n".'a prov:Attribution;'."\n".
             'prov:agent <'.$root.preg_replace('/[ \n]*/','',$matchthree['webid'])."> ;\n".'prov:hadRole :author'."\n ];";
        echo 'match four is '.$matchthree['webid']."\n";
        print_r($matchthree); 
      }

     if(preg_match($srchinsidethree,$result,$matchfour)) {
        echo '<'.$url.'>'.' prov:wasAttributedTo <'.$root.preg_replace('/[ \n]*/','',$matchfour['webide'])."> . \n";
        echo '<'.$root.preg_replace('/[ \n]*/','',$matchfour['webide']).'> foaf:name "'.$matchfour['named'].'" .'."\n";
        echo 'match three is '.preg_replace('/ /','',$matchfour['webide']);
        print_r($matchfour);
      } 
    
       if(preg_match($srchinsidefour,$result,$matchfive)) {
        echo 'prov:startedAtTime "'.$matchfive['modified'].'" .'."\n";
        print_r($matchfive);
      }

      $resultmo = '';      

        if(preg_match_all($datesrch,$matchfive['modified'],$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
           $resultmo = $resultmo."{$match['year']}-{$match['month']}-{$match['day']}";
       }
     } else {
       $resultmo = 'No joy!';
     }

     echo $resultmo;


      foreach($months as $key => $value) {
    if(preg_match('/'.preg_quote($key).'/',$result,$matches)) {
      $respect = preg_replace('/'.preg_quote($key).'/',$months[$key],$resultmo);
      echo '"'.$respect.'"^^xsd:dateTime';
    }
  }


    } else {
       $result = 'No joy!';
    }
     return $result;

} 
