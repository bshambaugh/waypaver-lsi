<?php

// include the requests libary
  include('./Requests/library/Requests.php');
Requests::register_autoloader();

// target the desired website

$root = 'http://lunarsettlement.org/';
//$url = 'http://lunarsettlementindex.org/display/LSI/Biological+Support';
//$url = 'http://52.201.49.119/display/LSI/Communications';
//$url = 'http://52.201.49.119/display/LSI/Habitation+and+Infrastructure';
//$url = 'http://52.201.49.119/display/LSI/Lunar+Environment';
//$url = 'http://52.201.49.119/display/LSI/Resource%2C+Acquisition%2C+Processing%2C+and+Storage';
$url = 'http://52.201.49.119/display/LSI/Transportation%2C+Mobility%2C+and+Navigation';
// $url = 'http://lunarsettlementindex.org/display/LSI/X-Rays';
//$url = 'http://lunarsettlementindex.org/display/LSI/Bone+Mass+Monitoring';
//$url = 'http://lunarsettlementindex.org/display/LSI/Human+Health+Risk+of+Long-term+Low+Gravity';
// $url = 'http://lunarsettlementindex.org/display/LSI/Bone+Mass+Monitoring';
//$url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Environment';
//$url = 'http://lunarsettlementindex.org/display/LSI/Communications';
//$url = 'http://lunarsettlementindex.org/display/LSI/Human+Health+Risk+of+Long-term+Low+Gravity';
//$url = 'http://52.201.49.119/display/LSI/Diet+Optimization';
//$url = 'http://52.201.49.119/display/LSI/Radiation+Shielding+Materials';
//$url = 'http://52.201.49.119/display/LSI/Indirect+Extraction';
//$url = 'http://52.201.49.119/display/LSI/Chemical+Extraction';
//$url = 'http://lunarsettlementindex.org/display/LSI/Diet+Optimization';
//$url = 'http://lunarsettlementindex.org/display/LSI/Carbothermal+Regolith+Reduction';
//$url = 'http://lunarsettlementindex.org/pages/viewpage.action?pageId=1212824';
//$url = 'http://lunarsettlementindex.org/display/LSI/Diet+Optimization';
//$url = 'http://lunarsettlementindex.org/display/LSI/Diet+Psychology';
//$url = 'http://lunarsettlementindex.org/display/LSI/Hydrogen+Reduction';
//$url = 'http://lunarsettlementindex.org/display/LSI/Microwaving+Water+from+Lunar+Regolith';
//$url = 'http://52.201.49.119/display/LSI/Transportation%2C+Mobility%2C+and+Navigation';
//$url = 'http://52.201.49.119/display/LSI/Resource%2C+Acquisition%2C+Processing%2C+and+Storage';
//$url = 'http://52.201.49.119/display/LSI/Lunar+Environment';
//$url = 'http://52.201.49.119/display/LSI/Communications';
//$url = 'http://52.201.49.119/display/LSI/Biological+Support';
//$url = 'http://lunarsettlementindex.org/pages/viewpage.action?pageId=1212824';
//$url = 'http://lunarsettlementindex.org/display/LSI/RESOLVE';
//$url = 'http://lunarsettlementindex.org/display/LSI/Carbothermal+Regolith+Reduction';
//$url = 'http://lunarsettlementindex.org/display/LSI/Chemical+Extraction';
//$url = 'http://lunarsettlementindex.org/display/LSI/Radiation+Shielding+Materials';
//$url = 'http://lunarsettlementindex.org/display/LSI/Indirect+Extraction';
//$url = 'http://lunarsettlementindex.org/display/LSI/RESOLVE';
//$url = 'http://lunarsettlementindex.org/display/LSI/Microwaving+Water+from+Lunar+Regolith';
//$url = 'http://lunarsettlementindex.org/display/LSI/Hydrogen+Reduction';
//$url = 'http://lunarsettlementindex.org/display/LSI/Diet+Psychology';
//$url = 'http://lunarsettlementindex.org/display/LSI/Chemical+Extraction';
//$url = 'http://lunarsettlementindex.org/display/LSI/Lunar+Settlement+Roadblocks';
//$url = 'http://lunarsettlementindex.org/display/LSI/Roadblock+Categories';
//$url = 'http://52.201.49.119/display/LSI/No+Mature+Entry%2C+Descent%2C+and+Landing+Capability';
//$url = 'http://52.201.49.119/display/LSI/Unknown+Ideal+Habitat+Structure';
echo scrapepage($url,$root);

function scrapepage($url,$root) {

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
$local_table_result_s4 = '';
$local_table_result_s5 = '';
$local_table_result_s6 = '';
$local_table_result_s7 = '';
$local_table_result_s8 = '';
$local_table_result_s9 = '';


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

// comment this out when not testing
/*
  echo 'the table type is: '.$tabletypekey."\n";
  echo 'the th array is'."\n";
  print_r($tharray);
*/

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
           }


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
                 $local_table_result_s3 = $local_table_result_s3.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s3 = strip_tags($splarraytd[$key]);
              }

              } 




          }

     $local_table_result = $local_table_result_s1.
          '<'.$url.'> '.'lsi:roadblockType '.'"'.$local_table_result_s2.'" .'."\n".
          '<'.$url.'>'.'lsi:priority "'.$local_table_result_s3.'"^^xsd:integer .'."\n";
    }


  }    


 if($tabletypekey == 'Solution Category') {
      foreach ($splarray as $key => $value) {
    //   echo $splarray[$key]."\n";

     // Find if the table row contains a description of the Roadblock,
        // then capture and display its contents  
          if(preg_match('/Solution Description/',$splarray[$key],$matches)) {


  //           echo $splarray[$key]."\n";
  //           echo '==================soln description==================='."\n";
             $comment = matchsolutiondescription("{$splarray[$key]}");
  //           echo $comment;            

             // [3] echo to pull into global turtle file
           if($comment !== NULL) {
             $local_table_result_s1 = $local_table_result_s1.'<'.$url.'> rdfs:comment "'.$comment.'"'."\n";
           }
          }
    //   echo 'the roadblock description is'.$local_table_result_s1;

        // Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Cost Drivers/',$splarray[$key],$matches)) {
         //    echo 'Hello Priority you have cost drivers'."\n";
            $splarraytd = preg_split('/===break===/',capturetd($splarray[$key]));
            array_pop($splarraytd);
           // echo 'spl array td';
       /*
            echo 'hello current player';
            print_r($splarraytd);
            echo 'goodbye current player';      
       */
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
           }

// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Average Est Investment Cost/',$splarray[$key],$matches)) {
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
                 $local_table_result_s3 = $local_table_result_s3.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s3 = strip_tags($splarraytd[$key]);
              }

              }




          }


// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Average Est Time to Maturity/',$splarray[$key],$matches)) {
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
                 $local_table_result_s4 = $local_table_result_s4.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s4 = strip_tags($splarraytd[$key]);
              }

              }




          }


// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Commercial Status:/',$splarray[$key],$matches)) {
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
                 $local_table_result_s5 = $local_table_result_s5.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s5 = strip_tags($splarraytd[$key]);
              }

              }




          }


// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Related Industries\/Fields/',$splarray[$key],$matches)) {
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
                 $local_table_result_s6 = $local_table_result_s6.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s6 = strip_tags($splarraytd[$key]);
              }

              }




          }



// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Preliminary Tech Required/',$splarray[$key],$matches)) {
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
                 $local_table_result_s7 = $local_table_result_s7.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s7 = strip_tags($splarraytd[$key]);
              }

              }




          }


// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Est Time to Maturity \(in years\)/',$splarray[$key],$matches)) {
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
                 $local_table_result_s8 = $local_table_result_s8.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s8 = strip_tags($splarraytd[$key]);
                // add the array
              }

              }




          }


// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Funding Opportunities/',$splarray[$key],$matches)) {
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
                 $local_table_result_s9 = $local_table_result_s9.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s9 = strip_tags($splarraytd[$key]);
                // add the array
              }

              }




          }


     $local_table_result = $local_table_result_s1.
        //  '<'.$url.'> '.'lsi:costDrivers '.'"'.$local_table_result_s2.'" .'."\n".
          '<'.$url.'>'.'lsi:averageEstInvestmentCost "'.$local_table_result_s3.'" .'."\n".
          '<'.$url.'>'.' lsi:averageEstTimetoMaturity "'.$local_table_result_s4.'" .'."\n".
          '<'.$url.'>'.' lsi:commercialStatus "'.$local_table_result_s5.'" .'."\n".
          '<'.$url.'>'.' lsi:relatedIndustriesFields 
"'.$local_table_result_s6.'" .'."\n";
       //   '<'.$url.'>'.' lsi:preliminaryTechRequired '.$local_table_result_s7."\n".
       //   '<'.$url.'>'.' lsi:estTimetoMaturity '.$local_table_result_s8."\n";
       //   '<'.$url.'>'.' lsi:fundingOpportunities '.$local_table_result_s9."\n";
    }


  }


  if($tabletypekey == 'Specific Solution') {
    foreach ($splarray as $key => $value) {
       echo $splarray[$key]."\n";

     // Find if the table row contains a description of the Roadblock,
        // then capture and display its contents  
          if(preg_match('/Description/',$splarray[$key],$matches)) {

             $comment = matchdescription("{$splarray[$key]}");
             $comment = strip_tags($comment);
     //        echo 'th comment are'.$comment."\n";
     //        echo 'end of the comment'."\n";
             // [3] echo to pull into global turtle file
//            echo ' comment is '.$comment."\n";
//            echo ' ============================ '."\n";
if($comment !== '') {
            // echo '==============I am not null========'."\n";

        //    echo ' comment is '.$comment."\n";
        //    echo ' ============================ '."\n";


          $local_table_result_s1 = $local_table_result_s1.'<'.$url.'> rdfs:comment "'.$comment.'" .'."\n";
       //    echo '==============I am not null========'.$local_table_result_s1."\n";
}       
   }
     //  echo 'the roadblock description is'.$local_table_result_s1."\n";
     //  echo 'end of the roadblock description'."\n";

 // Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Current Player\(s\)/',$splarray[$key],$matches)) {
             echo '==============================='."\n";
             echo 'Current Players are the awesome'."\n";
             echo '==============================='."\n";
             $splarraytd = preg_split('/===break===/',capturetd($splarray[$key]));
              array_pop($splarraytd);
           //  print_r($splarraytd);
              foreach ($splarraytd as $key => $value) {
               $local_table_result_s2 = strip_tags($splarraytd[$key]);
              }
               echo $local_table_result_s2."\n";  
           } 


// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Progress Status/',$splarray[$key],$matches)) {
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
                 $local_table_result_s3 = $local_table_result_s3.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s3 = strip_tags($splarraytd[$key]);
              }

              }




          }



 // Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Est Investment Cost/',$splarray[$key],$matches)) {
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
                 $local_table_result_s4 = $local_table_result_s4.
                                       '<'.$url.'> '.$spltd[$key];
               }

             }
              if($spltd == NULL) {
                $local_table_result_s4 = '"'.preg_replace('/&nbsp;/','',strip_tags($splarraytd[$key])).'"^^xsd:integer .';
              }

              }




          }


// Priority (1-5)
   // Find if the table row contains a List of Roadblocks, then capture the contents
          if(preg_match('/Est Time to Maturity/',$splarray[$key],$matches)) {
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
                 $local_table_result_s5 = $local_table_result_s5.
                                       '<'.$url.'> "'.$spltd[$key].'"^^xsd:integer';
               }

             }
              if($spltd == NULL) {
                $local_table_result_s5 = '"'.strip_tags($splarraytd[$key]).'"^^xsd:integer .';
              }

              }




          }



     if(preg_match('/Component Systems/',$splarray[$key],$matches)) {
           $local_splarrayth = array();
           $local_tharray = array();
          //   echo 'Hello Priority'.$matches[0]."\n";
            
            $srch = "#"     // start pattern
         . "<th class=\"confluenceTh\">"    // find the div
         .$matches[0]        // get the string we want as 'argument'
         . "</th>"  // end div and spaces
         ."<td class=\"confluenceTd\">"
         .".*<table class=\"confluenceTable"
         .".*</table>"
         . "#siU";   // end string and end pattern

         $arraytmp = array();
         $arraytmp2 = array();
         $temp_local_splarrayth = array();

         if(preg_match($srch, $source_html, $matched)) {
          echo 'this match is'."\n"; 
          print_r($matched);
         } 
 
          $local_splarray = preg_split('/===break===/',parsetable($matched[0]));
          print_r($local_splarray);

          foreach ($local_splarray as $key => $value) {
             $local_splarrayth = preg_split('/===break===/',captureth($local_splarray[0]));
      }
          print_r($local_splarrayth);

          foreach($local_splarrayth as $key => $value) {
           if($local_splarrayth[$key] !== '') {
             echo $local_splarrayth[$key]."\n";
             array_push($temp_local_splarrayth,$local_splarrayth[$key]);
           }
          }

          print_r($temp_local_splarrayth);
          echo 'end of the print result'."\n";


          //holder array
          $holder = array();
          $localres1 = '';
          $localres2 = '';
       
    
        foreach($local_splarray as $key => $value) {
          $spltd = preg_split('/===break===/',capturetd($local_splarray[$key]));
     //     print_r($spltd);
          foreach($spltd as $keytwo => $valuetwo) {
             if($spltd[$keytwo] !== '') {
               array_push($arraytmp,strip_tags($spltd[$keytwo]));
             }
         // write some code here...   
          }
          foreach($arraytmp as $keyin => $value) {
              if($keyin == 0) {
                 $localres1 = '<'.$url.'>'.' lsi:componentSystem <'.$url.'/'.preg_replace('/ /
','+',$arraytmp[$keyin]).'> .'."\n";
                 echo $localres1."\n";
              }
             
              if($keyin == 1) {
                 $localres2 = '<'.$url.'/'.preg_replace('/ /','+',$arraytmp[0]).'>'.' rdfs:comment "'.$arraytmp[$keyin].'" .';
                 echo $localres2."\n";
              }
          //  echo $arraytmp[$key].' for '.$temp_local_splarrayth[$key].' for '.$key."\n";
          
 
        //    echo 'local res one is'.$localres1;
        //    echo 'loca res two is'.$localres2;
}          
           if($key <= 1) {
              echo 'part one'."\n";
              $local_table_result_s6 = $localres1.$localres2;
              echo $local_table_result_s6;
           }
          
           if($key > 1 and $key <= 3) {
             echo 'part two'."\n";
         //     echo $localres1."\n";
              $local_table_result_s7 = $localres1.$localres2;
              echo $local_table_result_s7;
           }
          
        //   echo 'local res one is'.$key.' value  '.$localres1."\n";
        //   echo 'loca res two is'.$key.' value '.$localres2."\n";
        //   echo 'temp array'."\n";
        //  echo $local_splarray[$key]."\n";
      //    print_r($arraytmp);
          $arraytmp = [];
        }

       //   print_r($arraytmp);
         //   echo 'local res one is'.$localres1;
         //   echo 'loca res two is'.$localres2;           
         //   $local_table_result_s6 = $localres1;
         //   $local_table_result_s7 = $localres2;
            echo 'here is a break here'."\n";     
 
          foreach($arraytmp as $keythree => $valuethree) {
              echo strip_tags($arraytmp[$keythree])."\n";
           }

         
      //    echo 'end of the td matching'."\n";

     }
         
           $local_table_result = $local_table_result_s1.
          '<'.$url.'> '.' lsi:currentPlayer '.'"'.$local_table_result_s2.'" .'."\n".
          '<'.$url.'>'.' lsi:progressStatus "'.$local_table_result_s3.'" .'."\n".
          '<'.$url.'>'.' lsi:estInvestmentCost '.$local_table_result_s4."\n".
          '<'.$url.'>'.' lsi:estTimeToMaturity '.$local_table_result_s5."\n".
           $local_table_result_s6."\n".
           $local_table_result_s7;
          
          
             
     }


  }





/*

  if($tabletypekey == 'Specific Solution') {
     echo 'This is the Specific Solution'."\n";
  }
*/

} else {
  // Alternative [3],[4] echo to pull into the turtle file
  $local_table_result = $local_table_result.matchlist($root,matchbody($source_html));

} 

//echo  'the local table result'.$local_table_result."\n";

$global_result = $global_result.$local_table_result;
//echo $global_result;

//}

//echo 'break here to new stuff'."\n";

// echo matchsolutioncategories($source_html);

$ltresult_s1 = NULL;
$ltresult_s2 = NULL;
$ltresult_s3 = NULL;

if(matchListSolnCat($url,$root,matchsolutioncategories($source_html)) !== NULL) {
//echo matchListSolnCat($url,$root,matchsolutioncategories($source_html));

$ltresult_s1 = matchListSolnCat($url,$root,matchsolutioncategories($source_html));
} else {
  $ltresult_s1 = '';
}

if(matchtags($url,$root,$source_html) !== NULL) {
//echo matchtags($url,$root,$source_html);

$ltresult_s2 = matchtags($url,$root,$source_html);
} else {
  $ltresult_s2 = '';
}

// $arrayisthere = preg_split('/--break--/', matchCitationList(matchCitations($source_html)));

// print_r($arrayisthere);

/*
echo 'this is the match citation list'."\n";
echo matchCitationList(matchCitations($source_html));

echo 'end of the match citation list'."\n";
echo 'start of the second match citation'."\n";
*/
if(matchCitations($source_html) !== NULL) {
//echo '<'.$url.'> lsi:citation "'.strip_tags(matchCitations($source_html)).'" .';

$ltresult_s3 = '<'.$url.'> lsi:citation "'.strip_tags(matchCitations($source_html)).'" .';
} else {
  $ltresult_s3 = '';
}

if(matchSpecificSolutions($url,$source_html) !== NULL) {

   $ltresult_s4 = matchSpecificSolutions($url,$source_html);
 //  echo 'lt result 4 is'.$ltresult_s4;
} else {
  $ltresult_s4 = '';
}

/*
if($ltresult_s1 and $ltresult_s2 and $ltresult_s3 !== NULL) { 
$global_result = $global_result.$ltresult_s1.$ltresult_s2.$ltresult_s3;
} else {
  $global_result = $global_result;
}
*/

$global_result = $global_result.$ltresult_s1.$ltresult_s2.$ltresult_s3.$ltresult_s4;
return $global_result."\n";

}
/*
echo 'end of the second match citation'."\n";
*/

function matchSpecificSolutions($url,$argument) {
   $result = '';
   $root = 'http://lunarsettlement.org';
   $srch = "#"
           ."Specific Solutions:.*Citations:"
           ."#siU";
   $srchinside = "#"
                 ."<a href=\"(?<url>.*)\">(?<name>.*)</a>"
                 ."#siU";

   if(preg_match($srch, $argument, $match_outer)) {
        if(preg_match_all($srchinside,$match_outer[0],$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {
         $result = $result.'<'.$url.'> lsi:specificSolution '.'<'.$root.$match['url'].'> .'."\n".
        '<'.$root.$match['url'].'> dc:title "'.$match['name'].'" .'."\n";
       }
     }
   //  $result = '';
   //  print_r($match);
  } else {
     $result = NULL;
  }
  return $result;

}

/*

function matchSpecificSolutions($url,$argument) {
   $result = '';
   $root = 'http://lunarsettlement.org';
   $srch = "#"
           ."Specific Solutions:</h2>.*"
           ."<a href=\""
           ."(?<url>.*)\">(?<name>.*)</a>"
           ."#siU";

   if(preg_match($srch, $argument, $match)) {
       $result = '<'.$url.'> lsi:specificSolution '.'<'.$root.$match['url'].'> .'."\n".
       '<'.$root.$match['url'].'> dc:title "'.$match['name'].'" .';
   //  $result = '';
   //  print_r($match);
  } else {
     $result = NULL;
  }
  return $result;

}        
*/

function matchCitationList($argument) {
  $result = '';
  $srch = "#"
          ."<p>(?<url>.*)"
          ."</p>"
          ."#siU";


if(preg_match_all($srch,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {


          $result = $result.$match['url'].' . --break--'."\n";

     }
   } else {
     $result = NULL;
   }
   return $result;
  
}

// 1 and 2


function matchCitations($html) {
   $srch = "#"
         //  ."<div id=\"main-content\" class=\"wiki-content\">.*"
           ."Citations:</h2>"
           .".*"
           ."</div>.*</div>"
           ."#siU";  
   if(preg_match($srch, $html, $match)) {
       $result = $match[0];
   //  $result = '';
   //  print_r($match);
  } else {
     $result = NULL;
  }
  return $result;
}



function matchtags($url,$root,$name) {
  $result = '';
  $srch = "#"
          ."<a class=\"aui-label-split-main\" href=\"(?<url>.*)\" rel=\"tag\">(?<name>.*)</a>"
          ."#siU";

 if(preg_match_all($srch,$name,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {


          $result = $result.'<'.$url.'> lsi:label '.'<'.$root.$match['url'].'> .'."\n".
   '<'.$root.$match['url'].'> dc:title "'.$match['name'].'" .'."\n";

     }
   } else {
     $result = NULL;
   }
   return $result;
}

function matchsolutioncategories($html) {
  $srch = "#"
          ."Solution Categories:</h2>"
          .".*"
          ."<h2"
          ."#siU";
  
  if(preg_match($srch, $html, $match)) {
       $result = $match[0];
   //  $result = '';
   //  print_r($match);
  } else {
     $result = NULL;
  }
  return $result;
}

function matchListSolnCat($url,$root,$argument) {
   $result = '';
   $searchlist = "#"
              ."<li><a href=\"(?<url>.*)\">(?<name>.*)</a></li>"
              ."#siU";
   if(preg_match_all($searchlist,$argument,$matches, PREG_SET_ORDER)) {
       foreach ($matches as $key=>$match) {


             $result = $result.'<'.$url.'> lsi:containsSolutionCategory '.'<'.$root.$match['url'].'> .'."\n".
                       '<'.$root.$match['url'].'> a lsi:SolutionCategory .'."\n".
              '<'.$root.$match['url'].'> dc:title "'.$match['name'].'" . '."\n";

     }
   } else {
     $result = NULL;
   }
   return $result;
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
   $result = NULL;   
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


             $result = $result.'<'.$root.$match['url'].'> a lsi:Roadblock .'."\n".
                       '<'.$root.$match['url'].'> dct:title '.'"'.$match['name'].'" .'."\n";

     }
   } else {
     $result = NULL;
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
     $result = NULL;
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
     $result = NULL;
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
     $result = NULL;
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
     $result = NULL;
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
     $result = NULL;
 }
  return $result;
}

function matchsolutiondescription($argument) {
  $result = '';
  $srch = "#"
        //  ."class=\"confluenceTh\"><strong>Description</strong></th>.*"
          ."data-macro-name=\"text-data\">"
          ."(?<description>.*)"
          ."</p>"
          ."#siU";
    if(preg_match($srch, $argument, $match)) {
      $result = $match['description'];
    } else {
        $result = NULL;
    //  $result = 'No joy!';
    }
   return $result;
}

function matchdescription($argument) {
   $result = '';
   $srch = "#"
           ."<th colspan=\"1\" "
           ."class=\"confluenceTh\">Description</th>.*"
           ."data-macro-name=\"text-data\">"
           ."(?<description>.*)"
           ."</p>"
           ."#siU";
    if(preg_match($srch, $argument, $match)) {
      $result = $match['description'];
    } else {
        $result = NULL;
    //  $result = 'No joy!';
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
      $result = NULL;
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
       $resultmo = NULL;
     }

      foreach($months as $key => $value) {
    if(preg_match('/'.preg_quote($key).'/',$result,$matches)) {
      $respect = preg_replace('/'.preg_quote($key).'/',$months[$key],$resultmo);
    }
  }


    } else {
       $result = NULL;
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
