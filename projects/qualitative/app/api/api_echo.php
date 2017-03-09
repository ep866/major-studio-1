<?php

exec('zip -r archives/archive_'. date("Y_m_d") .'.zip "data"');
echo "************ Archive created ***************";
echo "<br><br>";


$routeFolder = "data/raw/";
$cleanFolder = "data/clean/";
$keywordsFolder = "data/keywords/";


$dict = [
  ["q" => "politics",
   "fname" => "politics"
  ],
  [
  "q" => "culture",
  "fname" => "culture"
  ]
];


$femaleRootQ = "female";
$maleRootQ = "male";


function fetcher($rootQ, $group, $threshold) {
  global $dict;

  // for each topic
  for($topic = 0; $topic < sizeof($dict); $topic++) {
    // this is the temp storage for the duration query
    $storage = [];

    // build query
    $query = $rootQ . "," . ($dict[$topic]["q"]);
    $filename = $group . ($dict[$topic]["fname"]);

    echo "<br><br>****************************************************<br>";
    echo "Start query ". $query;
    echo "<br>****************************************************<br><br>";

    // define length of request - 10 times loop for 100 result
    for( $page = 0; $page <= $threshold; $page++ ) {
      if( $page == $threshold ) {
        // check data
          checkData($query, $filename, function() {
            echo "<br>Done checking <br><br>";
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

  echo "<br><br>@@@@@@@@@ FETCHED ALL " . $rootQ . " RESULTS @@@@@@@@@@@<br><br>";
}


// fecth defaults
  // fetchDefault($femaleRootQ, "female_", 2);
  // sleep(20);
  // fetchDefault($maleRootQ, "male_", 2);

// fecth categories
  // fetcher($femaleRootQ, "female_", 2);
  // // nap for 2 minutues
  // sleep(120);
  // fetcher($maleRootQ, "male_", 2);

  // $storage = [];
  // makeRequest("female,lgbt", 100, $storage, "test");


function fetchDefault($rootQ, $group, $threshold) {

    $storage = [];

    // build query
    $query = $rootQ;
    $filename = $group . "default";

    echo "<br><br>****************************************************<br>";
    echo "Start query ". $query;
    echo "<br>****************************************************<br><br>";

    // define length of request - 10 times loop for 100 result
    for( $page = 0; $page <= $threshold; $page++ ) {
      if( $page == $threshold ) {
        // check data
          checkData($query, $filename, function() {
            echo "<br>Done checking <br><br>";
          });
        // process data
          postProcess($filename);
      } else {
        // make request; pass storage by reference
        makeRequest($query, $page, $storage, $filename);
        sleep(5);
      }
    }

  echo "<br><br>@@@@@@@@@ FETCHED ALL " . $rootQ . " RESULTS @@@@@@@@@@@<br><br>";
}






function makeRequest($q, $pageN, &$store ,$filename) {
  // echo "********* Start fetching ". $q ." Data **********";
   global $routeFolder;

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

      echo "<br> Fetched " . $q . " " . $pageN . "<br>";

      // echo "***************** end fetching *****************";
   } else {
      echo "<br><br>Failed to fetch " . $q . " number " . $pageN . "<br><br>";

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

  $storage = [];

  echo "****************************************************<br>";
  echo "Start checking ". $filename;
  echo "<br>****************************************************";
  echo "<br><br>";

   $string = file_get_contents($routeFolder . $filename . ".json");
   $json_a = json_decode($string, true);

   foreach($json_a as $key => $store) {

    $thisKey = key($json_a[$key]);

    echo "@@@@@@@ " . $thisKey ." @@@@@@@";

      // check if store is an assoc array; if it has a key
      // then it contains a message or error from api
      if(sizeof($store,1) > 1 && !key($store[$thisKey])) {
         echo "<br>";
         echo "Saving " . $thisKey . "<br><br>";

         $storage[] = $store;

      } else {
         $missingKey = array_keys($store)[0];

         echo "<br>***************************************** <br>";
         echo "Missing data at " . $missingKey;
         echo "<br>*****************************************";

            // make request
            $thisKey = explode("_", $missingKey);

            // make request and rewrite data
            makeRequest($q, $thisKey[1], $storage, $filename . "_checked");
            sleep(3);
      }
   }

   // make final copy
   $myfile = fopen($routeFolder . $filename . "_checked.json", "w") or die("Unable to open file!");

      echo "****************************************************<br>";
      echo $filename . " data are checked and fetched!";
      echo "<br>****************************************************";
      echo "<br><br>";

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

  $string = file_get_contents($routeFolder . $filename . "_checked.json");
  $json_a = json_decode($string, true);

  $result = [];
  $keywords = [];

  echo "****************************************************<br>";
  echo "Start processing ". $filename;
  echo "<br>****************************************************<br>";

   foreach($json_a as $key => $store) {
      foreach($store as $store_key => $each_store) {

         echo "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@<br><br>";
         echo "Processing articles for page " . $store_key . "<br><br>";

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
           echo "FAILED TO PROCESS " . $store_key;
           echo "<br><br>@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@<br><br>";
         }
      }
   }

   $myfile = fopen($cleanFolder . $filename . ".json", "w") or die("Unable to open file!");
   fwrite($myfile, json_encode($result));
   fclose($myfile);

   $myfile = fopen($keywordsFolder . $filename . ".json", "w") or die("Unable to open file!");
   fwrite($myfile, json_encode($keywords));
   fclose($myfile);

  echo "****************************************************<br>";
  echo "Processing of ". $filename . " done!";
  echo "<br>****************************************************<br>";

}







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
