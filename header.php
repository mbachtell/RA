<div id="header">
			<div id="noColor">
      <a href="index.php">
  			<h1>
  					<img src="images/ra2.png" alt="RA Logo" />
          RA: the Research Assistant
        </h1>
      </a>
			</div>
			</div>
      <form action="index.php">
        <input size="120" id="query" name="query" placeholder=
        "Type keywords, select sources" type="text"> <input type=
        "submit" value="Submit"> <input type="reset" value=
        "Reset"> <input type="hidden" name="tk" value=
        "go"><br>
        <a href="#" id="slick-toggle" name=
        "slick-toggle">Wikipedia Source</a>
        <div id="slickbox">
				<select id="searchLanguage" name="languageVariable">
				<?php
						 if ((!$languageVariable)||($languageVariable == "")){$languageVariable = "en";} 
							$wikipediaVersionQuery = "SELECT * FROM wikipediaversions ORDER BY Wiki";
							$wikipediaVersionResults = mysql_query($wikipediaVersionQuery);
							while ($wikipediaVersionRow = mysql_fetch_array($wikipediaVersionResults)){
										echo "<option value=\"{$wikipediaVersionRow['Wiki']}\" lang=\"{$wikipediaVersionRow['Wiki']}\""; 
										if ($languageVariable == $wikipediaVersionRow['Wiki']){echo " selected";}
										echo ">{$wikipediaVersionRow['Language']}</option>\n";
							}
				?>
				</select>        </div>
      </form>      
			<hr/>
