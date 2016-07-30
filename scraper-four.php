<?php

// include the requests libary
  include('./Requests/library/Requests.php');
Requests::register_autoloader();

// target the desired website

//$root = 'http://lunarsettlement.org/';
//$url = 'http://lunarsettlementindex.org/display/LSI/Biological+Support';
$url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Environment';
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
//echo $global_result;
//echo  matchtitleinpageRDF($url,$source_html);
//echo strip_tags(matchauthor($source_html));
// match the authorship information
// [2] echo to pull into global turtle file
$global_result = $global_result.matchauthor($url,$root,$source_html);
//echo $global_result;
// echo matchauthor($url,$root,$source_html);

$splarray = array();
$tharray = array();

$local_table_result = '';
$local_table_result_s1 = '';
$local_table_result_s2 = '';

// return the roadblocks...
if(containstable(matchbody($source_html)) == 1) {
  $splarray = preg_split('/===break===/',parsetable(matchbody($source_html)));
  array_pop($splarray);

// Use this code to match the table row descriptions. For now, hardcoding for the Table for Roadblocks. 
/*
  foreach ($splarray as $key => $value) {
     $splarrayth = preg_split('/===break===/',captureth($splarray[$key])); 
     array_push($tharray,$splarrayth[0]);
  }
*/   
      
       foreach ($splarray as $key => $value) {

        // Find if the table row contains a description of the Roadblocks,
        // then capture and display its contents  
          if(preg_match('/Description/',$splarray[$key],$matches)) {

             $comment = matchdescription("{$splarray[$key]}");
             // [3] echo to pull into global turtle file
             $local_table_result_s1 = $local_table_result_s1.'<'.$url.'> rdfs:comment "'.$comment.'"'."\n";
           //  echo $local_table_result,"\n";
    //         echo $global_result.'is the global result'."\n";
//             echo '<'.$url.'> rdfs:comment "'.$comment.'"'."\n";
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
           //$global_result = $global_result.'<'.$url.'> '.$spltd[$key];
           $local_table_result_s2 = $local_table_result_s2.'<'.$url.'> '.$spltd[$key];
  //         echo '<'.$url.'> '.$spltd[$key];
        }
       $local_table_result = $local_table_result_s1.$local_table_result_s2;  
      
} else {
  // Alternative [3],[4] echo to pull into the turtle file
  //$global_result = $global_result.matchlist($root,matchbody($source_html));
  $local_table_result = $local_table_result.matchlist($root,matchbody($source_html));
//  echo matchlist($root,matchbody($source_html));

} 

//echo 'the local table result'.$local_table_result."\n";

$global_result = $global_result.$local_table_result;
echo 'the global result is'.$global_result;


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


//             $result = $result.$match['url'];

             $result = $result.'<'.$root.$match['url'].'> a lsi:RoadblockCategory .'."\n".
                       '<'.$root.$match['url'].'> dct:title '.'"'.$match['name'].'" .'."\n";


//           $result = $result."Match $key: <a href=\"{$match['url']}\">{$match['name']}</a>\n";
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
//      $result = '<'.$match['rdfcontent'].'>'.' dc:title "'.$match['pagetitle']."\" .";
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
//    $url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Environment';
//    $root = 'http://lunarsettlementindex.org';
    if(preg_match($srch, $argument, $match)) {
       $result = $match['author'];
       $wohtml = strip_tags($result); 
//       echo $wohtml; 
/// ......

//      echo $result;   
  
    preg_match($srchinsidefour,$result,$matchfive);

/* 
    if(preg_match($srchinsidefour,$result,$matchfive)) {
      //  echo 'prov:startedAtTime "'.$matchfive['modified'].'" .'."\n";
        print_r($matchfive);
      }
*/
      $resultmo = '';

        if(preg_match_all($datesrch,$matchfive['modified'],$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
           $resultmo = $resultmo."{$match['year']}-{$match['month']}-{$match['day']}";
       }
     } else {
       $resultmo = 'No joy!';
     }

//     echo $resultmo;

//     print_r($months);

      foreach($months as $key => $value) {
    if(preg_match('/'.preg_quote($key).'/',$result,$matches)) {
      $respect = preg_replace('/'.preg_quote($key).'/',$months[$key],$resultmo);
//      echo 'prov:startedAtTime '.'"'.$respect.'"^^xsd:dateTime .';
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

      
//        echo '<'.$url.'>'.' prov:wasAttributedTo <'.$root.preg_replace('/[ \n]*/','',$matchthree['webid'])."> . \n";
  //      echo '<'.$root.preg_replace('/[ \n]*/','',$matchthree['webid']).'> foaf:name "'.$matchthree['name'].'" .'."\n";
   //     echo '<'.$url.'>'.' prov:qualifiedAttribution ['."\n".'a prov:Attribution;'."\n".
     //        'prov:agent <'.$root.preg_replace('/[ \n]*/','',$matchthree['webid'])."> ;\n".'prov:hadRole lsi:author ] .'."\n";
//        echo 'match four is '.$matchthree['webid']."\n";
//        print_r($matchthree); 
      }

     if(preg_match($srchinsidethree,$result,$matchfour)) {
     
       $local_result = $local_result.'<'.$url.'>'.' prov:wasAttributedTo <'.$root.preg_replace('/[ \n]*/','',$matchfour['webide'])."> . \n".
       '<'.$root.preg_replace('/[ \n]*/','',$matchfour['webide']).'> foaf:name "'.$matchfour['named'].'" .'."\n".
       '<'.$url.'>'.' prov:qualifiedAttribution ['."\n".'a prov:Attribution;'."\n".
       'prov:agent <'.$root.preg_replace('/[ \n]*/','',$matchfour['webide'])."> ;\n".'prov:hadRole lsi:editor ] .'."\n"; 
     

     //   echo '<'.$url.'>'.' prov:wasAttributedTo <'.$root.preg_replace('/[ \n]*/','',$matchfour['webide'])."> . \n";
     //   echo '<'.$root.preg_replace('/[ \n]*/','',$matchfour['webide']).'> foaf:name "'.$matchfour['named'].'" .'."\n";
     //   echo '<'.$url.'>'.' prov:qualifiedAttribution ['."\n".'a prov:Attribution;'."\n".
     //        'prov:agent <'.$root.preg_replace('/[ \n]*/','',$matchfour['webide'])."> ;\n".'prov:hadRole lsi:editor ] .'."\n";
  //      echo 'match three is '.preg_replace('/ /','',$matchfour['webide']);
  //      print_r($matchfour);
      } 

      $local_result = $local_result.'<'.$url.'>'.' lsi:lastmodified '.'"'.$respect.'"^^xsd:dateTime . '."\n";

   //   echo '<'.$url.'>'.' lsi:lastmodified '.'"'.$respect.'"^^xsd:dateTime . '."\n";
   //  return $result;
    //  echo '====== local result======'.$local_result.'======local result======='; 
      return $local_result;
} 
