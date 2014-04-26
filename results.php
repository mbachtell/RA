<?php 
      error_reporting(0);
      
			include 'connect.php';
      
			// VARIABLE PROCESSING
      $source = strip_tags(htmlspecialchars($_GET["source"]));
      $query = ucwords(strtolower(strip_tags(htmlspecialchars($_GET["query"]))));
      $encodedQuery = urlencode($query);
			$languageCodes = strip_tags(htmlspecialchars($_GET['languageCodes']));     
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
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  </head>
  <body>
    <div id="container">
<!--  style="width:800px"-->
			<?php 
						include 'header.php';
						$languagePiece = explode(",", $languageCodes);
						$languageCount = count($languagePiece);
						$languageString = "";
						for($i=0;$i<=$languageCount;$i++){
//						     echo "<p>$languagePiece[$i]</p>";
								 $piecesPieces = explode(":", $languagePiece[$i]);
								 $explodeForCount = count(explode(" ", $piecesPieces[1]));
								 $languageString .= "\"$piecesPieces[1]\"|";
						}
						$languageString = substr($languageString, 0, -4);
						$languageStringNoPipes = preg_replace("/\|/Us", " OR ", $languageString);
						$encodedString = urlencode($languageString);
						$encodedStringNoPipes = urlencode($languageStringNoPipes);

						echo "<h3>Search String</h3>";
						echo "<p>$languageString</p>";

						echo "<h3>Suggested Searching Locations</h3>";
						$suggestionResult = mysql_query("SELECT * FROM suggestioncategories ORDER BY category");
						while ($suggestionRow = mysql_fetch_array($suggestionResult)){
									echo "<h4>{$suggestionRow['category']}</h4>";
									// LOOK UP THE URLS TO SUGGEST
									$urlQueryResult = mysql_query("SELECT * FROM suggestions WHERE categoryId='{$suggestionRow['id']}' ORDER BY name");
									echo "<ul>";
									while ($urlRow = mysql_fetch_array($urlQueryResult)){
												echo "<li><a href=\"{$urlRow['url']}";
												if ($urlRow['pipes'] == "0"){
													 echo "$encodedStringNoPipes\">";
												}
												else{
														 echo "$encodedString\">";
												}
												echo"$printUrl{$urlRow['name']}</a>";
												if ($urlRow['rss_feed'] != ""){
													 echo " | <a href=\"{$urlRow['rss_feed']}";
														 if ($urlRow['pipes'] == "0"){
  													 		echo "$encodedStringNoPipes\">";
  													 }
      											 else{
      											 		echo "$encodedString\">";
      											 }
													 echo "RSS Feed</a>"; 
												}
												echo"</li>";
									}
									echo "</ul>";
						}
						echo "<hr/>";

						include 'footer.php';
			?>
    </div>
  </body>
</html>
