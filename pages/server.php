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
			$getChars = $Database->select("characters", "COUNT(online)", null, "online=1")->get_result();
			$pOnline = $getChars->data_seek(0);
			echo '
					',$pOnline,'
				  ';
		}
		
		
		
	
	
  }
}
?>