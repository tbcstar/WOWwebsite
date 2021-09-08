<?php
global $Connect;

class server {
	
public static function serverStatus($realm_id) 
	{
 ?>

        
        
	   <?php
	
	   
		//Get players online
		if ($GLOBALS['serverStatus']['playersOnline']==TRUE) 
		{
			$Connect->connectToRealmDB($realm_id);
			$getChars = $conn->query("SELECT COUNT(online) FROM characters WHERE online=1");
			$pOnline = $getChars->data_seek(0);
			echo '
					',$pOnline,'
				  ';
		}
		
		
		
	
	
  }
}
?>