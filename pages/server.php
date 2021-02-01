<?php
global $Connect

class server {
	
public static function serverStatus($realm_id) 
	{
 ?>

        
        
	   <?php
	
	   
		//Get players online
		if ($GLOBALS['serverStatus']['playersOnline']==TRUE) 
		{
			$Connect->connectToRealmDB($realm_id);
			$getChars = mysqli_query($conn, "SELECT COUNT(online) FROM characters WHERE online=1");
			$pOnline = mysqli_data_seek($getChars,0);
			echo '
					',$pOnline,'
				  ';
		}
		
		
		
	
	
  }
}
?>