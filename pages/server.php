<?php
global $Database;

class server {
	
public static function serverStatus($realm_id) 
	{
 ?>

        
        
	   <?php
	
	   
		//Get players online
		if ($GLOBALS['serverStatus']['playersOnline']==TRUE) 
		{
			$Connect->realm($realm_id);
			$getChars = $Database->select( COUNT(online) FROM characters WHERE online=1");
			$pOnline = $getChars->data_seek(0);
			echo '
					',$pOnline,'
				  ';
		}
		
		
		
	
	
  }
}
?>