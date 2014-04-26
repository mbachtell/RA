<?php 

      // ESTABLISH CONNECTION
      $link = mysql_connect('localhost', 'USERNAME', 'PASSWORD');
      $db_selected = mysql_select_db('ra', $link);
			mysql_set_charset('utf8',$db_selected);  		
			mysql_query("SET NAMES 'utf8'", $link);

      //IDENTIFY MYSELF
      ini_set( 'user_agent', 'RA: Research Assistant' );

/*
			set_include_path('~/pear/' . PATH_SEPARATOR
      . get_include_path());
*/
?>
