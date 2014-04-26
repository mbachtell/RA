<?php 
    error_reporting(0);

			include_once 'connect.php';

/*
  			// MEDIA WIKI PARSING PEAR PACKAGE
  			include_once 'pear/Text/Wiki/mediawiki.php';
  			$rules = array('Heading', 'Paragraph', 'Wikilink');
  			$wiki = new Text_Wiki_Mediawiki;
  			$wiki->setRenderConf('xhtml', 'Wikilink', 'view_url', 'index_b.php?&tk=go&languageVariable=en&query=');   
  			$wiki->setRenderConf('xhtml', 'Wikilink', 'pages', false);  
  			$wiki->setRenderConf('xhtml', 'Url', 'target', false); 
  			// END MEDIAWIKI PARSING PACKAGE     
*/			
//				include 'renderWikipedia.php';
      
			// VARIABLE PROCESSING
      $tk = strip_tags(htmlspecialchars($_GET["tk"]));
      $source = strip_tags(htmlspecialchars($_GET["source"]));
			$qwert = strip_tags($_GET["query"]);
      $query = ucwords(strtolower(strip_tags(htmlspecialchars($_GET["query"]))));
//			$termForWikiAPI = preg_replace("/ /Us", "_", ucwords(strtolower(strip_tags($_GET["query"]))));
//			$termForWikiAPI = urlencode(ucwords(strtolower(strip_tags($_GET["query"]))));

			$languageVariable = strip_tags(htmlspecialchars($_GET["languageVariable"]));
			if ($languageVariable == "en"){
						$termForDisplay = trim(ucwords(strtolower(preg_replace("/_/Us", " ", strip_tags(htmlspecialchars($_GET["query"]))))));
						//$termForWikiAPI = urlencode(ucwords(strtolower(strip_tags($_GET["query"]))));
						$termForWikiAPI = preg_replace("/ /Us", "_", ucwords(strtolower(trim(strip_tags($_GET["query"])))));
			}
			else{
					 $termForDisplay = strip_tags($_GET["query"]);
					 $termForWikiAPI = urlencode(trim(strip_tags($_GET["query"])));
			}
      if ($languageVariable == ""){$languageVariable = "en";}
			$encodedQuery = urlencode($query);
      
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>
      RA: the Research Assistant by Architeuthis Inc.
    </title>
    <link href="CSS/IMS.css" media="all" rel="stylesheet" type="text/css">
		<script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
		<script type="text/javascript" src="javascript/toggle.js"></script>
		<script type="text/javascript" src="javascript/manipulateText.js"></script>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  </head>
  <body>
    <div id="container" style="width:100%;">
			<?php 
					include 'header.php';
				
          if ($tk == "go"){
						 // RECORD THE SEARCH TERM FOR THE HISTORY BOOKS
						 $ip = $_SERVER['REMOTE_ADDR']; 
						 $dateToRecord = strtotime("now");
						 $searchTermInsert = "INSERT INTO searchTerms (id, ipAddress, timeStamp, searchTerm) VALUES ('', '$ip', '$dateToRecord', '$termForDisplay')";
						 mysql_query($searchTermInsert);
				echo "<div id=\"googleStack\" style=\"float:right;width:35%;border-style:groove;\"><h4>Google Trend Data</h4>
					 			 <script type=\"text/javascript\" src=\"//www.google.com/trends/embed.js?hl=en-US&q=$qwert&content=1&cid=TIMESERIES_GRAPH_0&export=5&w=500&h=330\"></script><br/>
  				 			 <script type=\"text/javascript\" src=\"//www.google.com/trends/embed.js?hl=en-US&q=$qwert&content=1&cid=GEO_MAP_0_0&export=5&w=500&h=530\"></script>
					 			 <script type=\"text/javascript\" src=\"//www.google.com/trends/embed.js?hl=en-US&q=$qwert&content=1&cid=TOP_QUERIES_0_0&export=5&w=300&h=420\"></script>
				 </div> 
				";       
						 
						 // DISPLAY
             echo "<h3>$qwert</h3>";
      
             // WIKIPEDIA LOOKUP					 
             $wikipediaURL = "http://$languageVariable.wikipedia.org/w/api.php?format=xml&action=query&titles=$termForWikiAPI&prop=revisions&rvprop=content";
						 	//echo $wikipediaURL;
			    	   $file = file_get_contents($wikipediaURL); 
			
								// HANDLE REDIRECTS
								if (preg_match("/REDIRECT.*\]/i", $file, $redirectMatches)){
									 $redirectMatch = $redirectMatches[0];
									 $redirect = urlencode(trim(preg_replace("/REDIRECT|\[|\]/i", "", $redirectMatch)));
									 $termForWikiAPI = $redirect;
									 $redirectURL = "http://$languageVariable.wikipedia.org/w/api.php?format=xml&action=query&titles=$redirect&prop=revisions&rvprop=content";
									 $file = file_get_contents($redirectURL);
								}

								// HANDLE DISAMBIGUATION
																
                  // SUMMARY
                  preg_match_all("/\}\}.*\=\=/Us", $file, $summaryMatches);
									$summaryMatches = $summaryMatches[0];
									$summaryMatchCount = count($summaryMatches);
                  $summary = $summaryMatches[0];
									$summary = preg_replace("/\{\{Infobox .*\}\}/Us", "", $summary);
									preg_match_all("/'''.*==/Us", $summary, $newSummaryMatches);
									$summary = html_entity_decode($newSummaryMatches[0][0]);
									
									// CLEAN WIKIPEDIA MARKUP
									$summary = preg_replace("/\{\{[cC]ite.*\}\}/Us", "", $summary);
                  
									$summary = preg_replace("/&nbsp;/Us", " ", $summary);
                  $summary = preg_replace("/\n/Us", "<br/>", $summary);

                  $summaryPieces = explode("|<br/>", $summary);

                  $summary = $summaryPieces[0];
//									$newSummary = $wiki->transform($summary, 'Xhtml');
									$newSummary = html_entity_decode(preg_replace("/&lt;ref&gt;.*&lt;\/ref&gt;|&lt;ref.*&lt;\/ref&gt;|==/Us", "", $summary));
									$newSummary = preg_replace("/[\[|\]|\{|\}|\'\'\']/Us", "", $newSummary);
//									echo $newSummary;
									echo"<a href=\"#\" id=\"slick-toggle2\" name=\"slick-toggle2\">Definition</a>";
                  echo "<div id=\"slickbox2\">$newSummary</div>";
									
/*
									// WHAT LINKS HERE? / PROVIDE FOR TERM SUGGESTIONS
									 $getSuggestions = "http://$languageVariable.wikipedia.org/w/api.php?action=query&list=backlinks&bltitle=$termForWikiAPI&format=xml&bllimit=300&blnamespace=0";
									 $otherTerms = file_get_contents($getSuggestions);
									 preg_match_all("/title=\".*\"/Us", $otherTerms, $suggestions);
									 $suggestionsArrayZero = $suggestions[0];
									 $suggestionCount = count($suggestionsArrayZero);
									 asort($suggestionsArrayZero);
									 $suggestions = $suggestionsArrayZero;
									 $suggestionPrint = "";
									 for($s=0;$s<$suggestionCount;$s++){
									 		$suggestionsToPrint = preg_replace("/title=|\"/Us", "", $suggestions[$s]);
											$encodedSuggestion = urlencode($suggestionsToPrint);
											$suggestionPrint .= "<li><a href=\"index.php?query=$encodedSuggestion&tk=go\">$suggestionsToPrint</a></li>\n";									      
									 }
									echo "<p><a href=\"#\" id=\"slick-toggle3\" name=\"slick-toggle3\">Term Suggestions</a></p>";
                  echo "<div id=\"slickbox3\"><ul>$suggestionPrint</ul></div>";									 
*/											 
                  echo "<h4>Language Selection</h4>";
                  

									
									// MATCH ALL OF THE LANGUAGES
//									preg_match_all("/\[\[[a-z]{2,3}:.*\]\]/Us", $file, $matches);
//                  $matches = $matches[0];
//									if ((!$matches)||($matches == "")){
										 // HIT THE WIKIPEDIA API AGAIN
										 // http://en.wikipedia.org/w/api.php?format=xml&action=query&titles=Arsenic&prop=langlinks&lllimit=500&rvprop=content
										 $languageAPICall = "http://$languageVariable.wikipedia.org/w/api.php?format=xml&action=query&titles=$termForWikiAPI&prop=langlinks&lllimit=500&rvprop=content";
										 $file2 = file_get_contents($languageAPICall);
    									// <ll lang="br" xml:space="preserve">Arsenik</ll>
											preg_match_all("/<ll.*<\/ll>/Us", $file2, $matches2);
                      $matches = $matches2[0];
											$matches = preg_replace("/\<ll lang=\"/Us", "", $matches);
											$matches = preg_replace("/\" xml:space=\"preserve\"\>/Us", ":", $matches);
											$matches = preg_replace("/\<\/ll\>/Us", "", $matches);

//									}
                  
									// MATCH PROPER ENGLISH LANGUAGE TERM
									preg_match("/title=\".*\"/Us", $file, $englishMatch);
									$englishMatch = preg_replace("/title=|\"/Us", "", $englishMatch[0]);						
									
									array_push($matches, "eng:$englishMatch");
                  asort($matches);
                  $matchCount = count($matches);
									echo "$matchCount languages available";

									$halfMatchCount = round($matchCount/2, 0, PHP_ROUND_HALF_EVEN);
																	
                  $languages = array();
                  $languageTerm = array();
                  $termAllArray = array();
                  $concatonatedLanguageArray = array();
                  
					        for ($i=0; $i <= $matchCount; $i++){
                      $languageTerm = preg_replace("/\[/Us", "", $matches[$i]);
                      $languageTerm = preg_replace("/\]/Us", "", $languageTerm);                
                      $pieces = explode(":", $languageTerm);
                      $languageZ = $pieces[0]; $term = $pieces[1];
											$term = trim(preg_replace("/\([^)]*\)/", "", $term));                

                      // LANGUAGE LOOKUP
                      $languageQuery = "SELECT languageName, sixThirtyNineThree FROM languages2 WHERE sixThirtyNineOne='$languageZ' OR sixThirtyNineThree='$languageZ'";
											$languageResult = mysql_query($languageQuery);
                      $languageArray = mysql_fetch_array($languageResult);
                      $language = $languageArray[0];
											$languageCode = $languageArray[1];
											if ($language != ""){   
                      //   echo "<p>Language: $language \ Term: $term</p>";
                          $languages[] = $language;
                          $languageTerm[$language] = $term;
                          $terms .= "$term, ";
                          $termAllArray[languageCode] = $languageZ;
                          $termAllArray[languageName] = $language;
                          $termAllArray [term] = $term;
                          $concatonatedLanguageArray[] = $termAllArray;
                      }
                  }
//									print_r($concatonatedLanguageArray);
                  sort($languages);
                  $titleSort = array();

                  function build_sorter($key) {
                      return function ($a, $b) use ($key) {
                          return strnatcmp($a[$key], $b[$key]);
                      };
                  }

                  usort($concatonatedLanguageArray, build_sorter('languageName'));
                  $concatonatedLanguageArray = array_chunk($concatonatedLanguageArray, $halfMatchCount);
//									print_r($concatonatedLanguageArray);
									echo "
									<div id=\"c_b\">
									<form id=\"languages\" name=\"languages\" action=\"results.php\" method=\"GET\">
									<table>
									<tr><td style=\"vertical-align:text-top;\">
									";
                  foreach ($concatonatedLanguageArray[0] as $item) {
                      echo "\n\t<input type=\"checkbox\" name=\"terms[]\" id=\"{$item['term']}\" value=\"{$item['languageCode']}:{$item['term']}\"><span title=\"{$item['term']}\">{$item['languageName']}</span><br/>";
                  }
									echo "</td>\n<td style=\"vertical-align:text-top;\"><div style=\"display:table-cell; vertical-align:text-top\">";
                  foreach ($concatonatedLanguageArray[1] as $item) {
                      echo "\n\t<input type=\"checkbox\" name=\"terms[]\" id=\"{$item['term']}\" value=\"{$item['languageCode']}:{$item['term']}\"><span title=\"{$item['term']}\">{$item['languageName']}</span><br/>";
                  }
                  echo "</div></div></td>
									<td style=\"vertical-align:text-top;\">
											<strong>Keywords</strong><br/><textarea rows=\"4\" cols=\"40\" id=\"t\" name=\"terms\"></textarea>
											<br/>
											<input type=\"reset\" /><input type=\"Submit\" value=\"Submit\" />
											<input type=\"hidden\" id=\"languageCodes\" name=\"languageCodes\" />
									</form>
									</td>
									</tr></table>
									";
          }
          include 'footer.php';
					?>
    </div>
  </body>
</html>
