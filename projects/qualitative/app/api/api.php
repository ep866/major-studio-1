<?php
// set max exec time at 2 hours
// check php ini on server for restrictions
ini_set('max_execution_time', 3600 * 2);

$timeAtStart = time();

$log = 'data/log.txt';
//$currentLog = file_get_contents($log);

exec('zip -r archives/archive_'. date("Y_m_d") .'.zip "data"');
$currentLog = "************ Archive created *************** \n";
$currentLog .= "\n\n";

$routeFolder = "data/raw/";
$cleanFolder = "data/clean/";
$keywordsFolder = "data/keywords/";

$dict = [
  ["q" => "politics", "fname" => "politics"],
  ["q" => "economy", "fname" => "economy"],
  ["q" => "science", "fname" => "science"],
  ["q" => "religion", "fname" => "religion"],
  ["q" => "health,healthcare", "fname" => "health"],
  ["q" => "equality", "fname" => "equality"],
  ["q" => "civil%20rights", "fname" => "civil_rights"],
  ["q" => "human%20rights", "fname" => "human_rights"],
  ["q" => "technology", "fname" => "technology"],
  ["q" => "art,design,fashion", "fname" => "art"],
  ["q" => "culture,lifestyle", "fname" => "culture"],
  ["q" => "movies,television", "fname" => "movies"],
  ["q" => "sports", "fname" => "sports"],
  ["q" => "president", "fname" => "president"],
  ["q" => "senate", "fname" => "senate"],
  ["q" => "rights", "fname" => "rights"],
  ["q" => "income", "fname" => "income"],
  ["q" => "feminist", "fname" => "feminist"],
  ["q" => "lgbt", "fname" => "lgbt"],
  ["q" => "life", "fname" => "life"],
  ["q" => "sex,sexual", "fname" => "sex"],
  ["q" => "career,job", "fname" => "career"],
  ["q" => "family", "fname" => "family"],
  ["q" => "child,children", "fname" => "children"],
  ["q" => "discrimination", "fname" => "discrimination"],
  ["q" => "crime,violence", "fname" => "crime"]
];

$femaleRootQ = "female";
$maleRootQ = "male";


function fetcher($rootQ, $group, $threshold) {
  global $dict;
  global $currentLog;

  // for each topic
  for($topic = 0; $topic < sizeof($dict); $topic++) {
    // this is the temp storage for the duration query
    $storage = [];

    // build query
    $query = $rootQ . "," . ($dict[$topic]["q"]);
    $filename = $group . ($dict[$topic]["fname"]);

    $currentLog .= "\n\n****************************************************\n";
    $currentLog .= "Start query ". $query;
    $currentLog .= "\n****************************************************\n";

    // define length of request - 10 times loop for 100 result
    for( $page = 0; $page <= $threshold; $page++ ) {
      if( $page == $threshold ) {
        // check data
          checkData($query, $filename, function() {
            global $currentLog;
            $currentLog .= "\n Done checking \n\n\n";
          });
        // process data
          postProcess($filename);
      } else {
        // make request; pass storage by reference
        makeRequest($query, $page, $storage, $filename);
        sleep(3);
      }
    }
    // sleep 10 before making the next query
    sleep(10);
  }

  $currentLog .= "\n@@@@@@@@@ FETCHED ALL " . $rootQ . " RESULTS @@@@@@@@@@@\n\n";

  echo "<br>@@@@@@@@@ FETCHED ALL " . $rootQ . " RESULTS @@@@@@@@@@@<br><br>";
}



// update log at runtime
  // put file_put_contents($log, $currentLog) in functions;

// empty folder -> app errors in real time
// check log manually
// email log to self every day

// fetch defaults
  // fetchDefault($femaleRootQ, "female_", 10);
  // sleep(20);
  // fetchDefault($maleRootQ, "male_", 10);

// fetch categories
  // fetcher($femaleRootQ, "female_", 10);
  // // nap for 2 minutues
  // sleep(120);
  // fetcher($maleRootQ, "male_", 10);

  // $storage = [];
  // makeRequest("female,lgbt", 100, $storage, "test");


function fetchDefault($rootQ, $group, $threshold) {
    global $currentLog;

    $storage = [];

    // build query
    $query = $rootQ;
    $filename = $group . "default";

    $currentLog .= "****************************************************\n";
    $currentLog .= "Start default query ". $query;
    $currentLog .= "\n****************************************************\n";

    // define length of request - 10 times loop for 100 result
    for( $page = 0; $page <= $threshold; $page++ ) {
      if( $page == $threshold ) {
        // check data
          checkData($query, $filename, function() {
            global $currentLog;
            $currentLog .= "\n Done checking \n\n\n";
          });
        // process data
          postProcess($filename);
      } else {
        // make request; pass storage by reference
        makeRequest($query, $page, $storage, $filename);
        sleep(5);
      }
    }

    $currentLog .= "\n\n @@@@@@@@@ FETCHED ALL " . $rootQ . " RESULTS @@@@@@@@@@@ \n\n";
    echo "<br>@@@@@@@@@ FETCHED ALL " . $rootQ . " RESULTS @@@@@@@@@@@<br><br>";
}


function makeRequest($q, $pageN, &$store ,$filename) {
   global $routeFolder;
   global $currentLog;

   $curl = curl_init();
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   $query = array(
     "api-key" => "b231eafa711647fb8f36343901e6e47c",//"41553f34c5234f16b68507c8ab48160a",
     "q" => $q,
     "page" => $pageN
   );
   curl_setopt($curl, CURLOPT_URL,
     "https://api.nytimes.com/svc/search/v2/articlesearch.json" . "?" . http_build_query($query)
   );
   $result = json_decode(curl_exec($curl));

   // create object
   if( $result && array_key_exists("status", $result) && $result -> status == "OK") {
      if($pageN == "0") {
         $obj = ["key_0" => $result -> response -> docs];
      } else {
         $obj = ["key_" . $pageN => $result -> response -> docs];
      }

      // add result to finalset
      array_push($store, $obj);

      $currentLog .= "\n Fetched " . $q . " " . $pageN . "\n";

      // echo "***************** end fetching *****************";
   } else {
      $currentLog .= "\n\n Failed to fetch " . $q . " number " . $pageN . "\n\n";

      if( $result && array_key_exists("status", $result) ) {
        if($pageN == "0") {
           $obj = ["key_0" => $result];
        } else {
           $obj = ["key_" . $pageN => $result];
        }
      } else {
        if($pageN == "0") {
           $obj = ["key_0" => $result];
        } else {
           $obj = ["key_" . $pageN => $result];
        }
      }

      // add empty result to finalset
      array_push($store, $obj);
   }

      // save request
      $myfile = fopen($routeFolder . $filename . ".json", "w") or die("Unable to open file!");
      fwrite($myfile, json_encode($store));
      fclose($myfile);
}


function checkData($q, $filename, $cb) {
  global $routeFolder;
  global $currentLog;

  $storage = [];

  $currentLog .= "**************************************************** \n";
  $currentLog .= "Start checking ". $filename;
  $currentLog .= "\n****************************************************";
  $currentLog .= "\n\n";

   $string = file_get_contents($routeFolder . $filename . ".json");
   $json_a = json_decode($string, true);

   foreach($json_a as $key => $store) {

    $thisKey = key($json_a[$key]);

    $currentLog .= "@@@@@@@ " . $thisKey ." @@@@@@@";

      // check if store is an assoc array; if it has a key
      // then it contains a message or error from api
      if(sizeof($store,1) > 1 && !key($store[$thisKey])) {
         $currentLog .= "\n";
         $currentLog .= "Saving " . $thisKey . "\n\n";

         $storage[] = $store;

      } else {
         $missingKey = array_keys($store)[0];

         $currentLog .= "\n***************************************** \n";
         $currentLog .= "Missing data at " . $missingKey;
         $currentLog .= "\n*****************************************";

            // make request
            $thisKey = explode("_", $missingKey);

            // make request and rewrite data
            makeRequest($q, $thisKey[1], $storage, $filename . "_checked");
            sleep(3);
      }
   }

   // make final copy
   $myfile = fopen($routeFolder . $filename . "_checked.json", "w") or die("Unable to open file!");

      $currentLog .= "****************************************************\n";
      $currentLog .= $filename . " data are checked and fetched!";
      $currentLog .= "\n****************************************************";
      $currentLog .= "\n\n";

      fwrite($myfile, json_encode($storage));
      fclose($myfile);

      // echo "****************************************************<br>";
      // echo $filename ." checked data are written!";
      // echo "<br>****************************************************";
      // echo "<br><br>";

    return $cb();
}


function postProcess($filename) {
  global $routeFolder;
  global $cleanFolder;
  global $keywordsFolder;
  global $currentLog;

  $string = file_get_contents($routeFolder . $filename . "_checked.json");
  $json_a = json_decode($string, true);

  $result = [];
  $keywords = [];

  $currentLog .= "****************************************************\n";
  $currentLog .= "Start processing ". $filename;
  $currentLog .= "\n****************************************************\n";

   foreach($json_a as $key => $store) {
      foreach($store as $store_key => $each_store) {

         $currentLog .= "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n\n";
         $currentLog .= "Processing articles for page " . $store_key . "\n\n";

         if( !key($store[$store_key]) ) {
          foreach ($each_store as $key => $value) {
              unset($value["multimedia"]);
              unset($value["byline"]);
              unset($value["word_count"]);
              unset($value["slideshow_credits"]);
              unset($value["print_page"]);

              $keywords[] = [
                 "keywords" => $value["keywords"],
                 "headline" => $value["headline"],
                 "web_url" => $value["web_url"],
                 "date" => $value["pub_date"],
                 "news_desk" => $value["news_desk"]
              ];

              unset($value["keywords"]);

              //echo "Processing article N: " . ($key + 1) . "<br><br>";
              $result[] = $value;
           }
         } else {
           $currentLog .= "FAILED TO PROCESS " . $store_key;
           $currentLog .= "\n\n@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n\n";
         }
      }
   }

   $myfile = fopen($cleanFolder . $filename . ".json", "w") or die("Unable to open file!");
   fwrite($myfile, json_encode($result));
   fclose($myfile);

   $myfile = fopen($keywordsFolder . $filename . ".json", "w") or die("Unable to open file!");
   fwrite($myfile, json_encode($keywords));
   fclose($myfile);

  $currentLog .= "**************************************************** \n";
  $currentLog .= "Processing of ". $filename . " done!";
  $currentLog .= "\n****************************************************\n\n";

}

function time_elapsed_B($secs){
  $bit = array(
      ' year'        => $secs / 31556926 % 12,
      ' week'        => $secs / 604800 % 52,
      ' day'        => $secs / 86400 % 7,
      ' hour'        => $secs / 3600 % 24,
      ' minute'    => $secs / 60 % 60,
      ' second'    => $secs % 60
      );

  foreach($bit as $k => $v){
      if($v > 1)$ret[] = $v . $k . 's';
      if($v == 1)$ret[] = $v . $k;
      }
  array_splice($ret, count($ret)-1, 0, 'and');
  $ret[] = 'ago.';

  return join(' ', $ret);
}

$timeAtFinish = time();
$execTime = time_elapsed_B($timeAtFinish-$timeAtStart);

echo "time_elapsed_B: ". $execTime . "\n";
$currentLog .= "\n\n Execution time: " . $execTime . "\n\n";

file_put_contents($log, $currentLog);

// Front end
// console.log in app  to show length of files for debugging
// display a message in app at 5am in the morning that results are auto updating
// check keywords file for fun stuff and visualize when I have downtime
// I could order by news_desk

// write the repair results function to loop through results while


?>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Log</title>
  <meta name="description" content="">
  <meta name="author" content="">


  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
  <![endif]-->

  <style>
   body {
      font-family: "Helvetica Neue", Arial, sans-serif;
      font-size: 9pt;
      background: #232222;
      color: #fff;
      margin: 40px;
     // text-align: right;
   }
  </style>
</head>

<body>
   <div id="words"></div>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<!--   <script src="app.js"></script>  -->

</body>
</html>
