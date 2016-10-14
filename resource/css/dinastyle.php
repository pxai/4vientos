<?php header("Content-type: text/css"); 

$patharray = preg_split("/\//",$_SERVER['SCRIPT_NAME']);
array_pop($patharray);
array_pop($patharray);
$finalpath=implode($patharray,"/");
?>
/**
* $Id$
* dinastyle.css
* General style
*/


#dirlist li {
	list-style-image: url(<?=$finalpath?>/images/gnome-fs-directory.png);
}

    
.votingpanel  a {
                background-image: url(<?=$finalpath?>/images/stars.png); 
    }
  