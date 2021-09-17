    <div id="overlay"></div>
    <div id="wrapper">
    
<?php include "header.php" ?>
    
    <div id="leftcontent">
    <div class="box_two">
    
<?php
    global $Account, $Website, $Server, $Character, $Database;
    ?>
    
    <div class='box_two_title'>副本重置</div>
    让你重置你的角色的副本CD。<hr/>
    <?php
    $Account->isNotLoggedIn();
    
    $service = "reset";
    
    if ( DATA['service'][$service]['price'] == 0 )
        {
            echo '<span class="attention">副本重置是免费的。</span>';
        }
    else
{ ?>
    <span class="attention">重置副本费用 
    <?php echo DATA['service'][$service]['price'] .' '. $Website->convertCurrency(DATA['service'][$service]['currency']); ?></span>
    <?php 
    if (DATA['service'][$service]['currency'] == "vp")
    {
    	echo "<span class='currency'>投票积分：".$Account->loadVP($_SESSION['cw_user'])."</span>";
    }
    elseif ( DATA['service'][$service]['currency'] == "dp" )
    {
        echo "<span class='currency'>" . DATA['website']['donation']['coins_name'] . ": " . $Account->loadDP($_SESSION['cw_user']) . "</span>";
    }
}
    
    if (isset($_POST['ir_step1']) || isset($_POST['ir_step2'])) 
    {
    	echo '选择服务器： <b>'.$Server->getRealmName($_POST['ir_realm']).'</b><br/><br/>';
    }
    else
    {
    ?>
    选择服务器：
    &nbsp;
    <form action="?page=instancereset" method="post">
    <table>
        <tr>
        <td>
            <select name="ir_realm">
                <?php
                $result = $Database->select("realms", "name,char_db")->get_result();
                while ($row = $result->fetch_assoc())
                {
                    if (isset($_POST['ir_realm']) && $_POST['ir_realm'] == $row['char_db'])
                    {
                        echo '<option value="' . $row['char_db'] . '" selected>';
                    }
                    else
                    {
                        echo '<option value="' . $row['char_db'] . '">';
                    }
                        echo $row['name'] . '</option>';
                    }
                ?>
            </select>
        </td>
        <td>
            <?php
            if (!isset($_POST['ir_step1']) && !isset($_POST['ir_step2']) && !isset($_POST['ir_step3']))
                echo '<input type="submit" value="Continue" name="ir_step1">';
            ?>
        </td>
        </tr>
    </table>
    </form>
    <?php
    }
    if(isset($_POST['ir_step1']) || isset($_POST['ir_step2']) || isset($_POST['ir_step3']))
    {
    	if (isset($_POST['ir_step2'])) 
    		echo '选择角色:<b>'.$Character->getCharName($_POST['ir_char'],$Server->getRealmId($_POST['ir_realm']))
    		.'</b><br/><br/>';
    else
    {		
    ?>
    选择角色： 
    &nbsp;
    <form action="?page=instancereset" method="post">
    <table>
    <tr>
    <td>
    <input type="hidden" name="ir_realm" value="<?php echo $_POST['ir_realm']; ?>">
    <select name="ir_char">
    	 <?php
    	 $acc_id = $Account->getAccountID($_SESSION['username']);
    	 $Database->selectDB($_POST['ir_realm']);
    	 $result = $Database->select("characters", "name, guid", null, "account=$acc_id")->get_result();
    
    	 while($row = $result->fetch_assoc())
    	 {
    		if (isset($_POST['ir_char']) && $_POST['ir_char'] == $row['guid'])
            {
                echo '<option value="' . $row['guid'] . '" selected>';
            }
            else
            {
                echo '<option value="' . $row['guid'] . '">';
            }
    			
    		echo $row['name'].'</option>'; 
    	 }
    	 ?>
    </select>
    </td>
    <td>
    <?php
    if(!isset($_POST['ir_step2']) && !isset($_POST['ir_step3']))
    	echo '<input type="submit" value="Continue" name="ir_step2">';
    ?>
    </td>
    </tr>
    </table>
    </form>
    <?php	
    	}
    }
    if (isset($_POST['ir_step2']) || isset($_POST['ir_step3']))
    { ?>
    选择副本：
    &nbsp;
    <form action="?page=instancereset" method="post">
    <table>
    <tr>
    <td>
    <input type="hidden" name="ir_realm" value="<?php echo $_POST['ir_realm']; ?>">
    <input type="hidden" name="ir_char" value="<?php echo $_POST['ir_char']; ?>">
    <select name="ir_instance">
    	 <?php
    	 $guid = $Database->conn->escape_string($_POST['ir_char']);
    	 $Database->selectDB($_POST['ir_realm']);
    
    	 $result = $Database->select("character_instance", "instance", null, "guid=$guid AND permanent=1")->get_result();
    	 if ($result->num_rows ==0) 
    	 {
    		 echo "<option value='#'>没有副本需要重置！</option>";
    		 $nope = true;
    	 }
    	 else
    	 {
    		 while($row = $result->fetch_assoc()) 
    		 {
    			 $getI     = $Database->select("instance", "id, map, difficulty", null, "id=". $row['instance'])->get_result();
    			 $instance = $getI->fetch_assoc(); 
    			 
    			 $Database->selectDB("webdb");
    			 $getName = $Database->select("instance_data", "name", null, "map='". $instance['map'] ."'")->get_result();
    			 $name = $getName->fetch_assoc();
    			 
    			 if(empty($name['name']))
    			 	$name = "Unknown Instance";
    			 else
    			 	$name = $name['name'];	
    				
    			if ($instance['difficulty'] == 0)
                {
                    $difficulty = "10-man Normal";
                }
                elseif ($instance['difficulty'] == 1)
                {
                    $difficulty = "25-man Normal";
                }
                elseif ($instance['difficulty'] == 2)
                {
                    $difficulty = "10-man Heroic";
                }
                elseif ($instance['difficulty'] == 3)
                {
                    $difficulty = "25-man Heroic";
                }
    			 
    			 echo '<option value="'.$instance['id'].'">'.$name.' <i>('.$difficulty.')</i></option>';
    	 }
     }
    ?>
    </select>
    </td>
    <td>
    <?php
    if(!isset($_POST['ir_step1']) && !isset($nope))
    	echo '<input type="submit" value="Reset Instance" name="ir_step3">';
    ?>
    </td>
    </tr>
    </table>
    </form>
    <?php	
    }
    
    if(isset($_POST['ir_step3']))
    {
    	$guid     = $Database->conn->escape_string($_POST['ir_char']);
        $instance = $Database->conn->escape_string($_POST['ir_instance']);
    	
    	if (DATA['service'][$service]['currency'] == "vp")
    	{
    		if ($Account->hasVP($_SESSION['cw_user'], DATA['service'][$service]['price']) == false)
    			echo '<span class="alert">你没有足够的投票积分！';
    		else
    		{
    			$Database->selectDB($_POST['ir_realm']);
    			$Database->conn->query("DELETE FROM instance WHERE id='".$instance."'");
    			$Account->deductVP($Account->getAccountID($_SESSION['cw_user']), DATA['service'][$service]['price']);
    			echo '<span class="approved">副本CD已重置！</span>';
    		}
    	}
    	elseif ( DATA['service'][$service]['currency'] == "dp" )
    		if ( $Account->hasDP($_SESSION['cw_user'], DATA['service'][$service]['price']) == false )
            {
    			echo '<span class="alert">你的捐赠积分不够'.DATA['website']['donation']['coins_name'];
            }
    		else
    		{
    			$Database->selectDB($_POST['ir_realm']);
    			$Database->conn->query("DELETE FROM instance WHERE id='".$instance."'");
    			$Account->deductDP($Account->getAccountID($_SESSION['cw_user']),DATA['service'][$service]['price']);
    			echo '<span class="approved">副本CD已重置！</span>';
    			
    			$Account->logThis("Performed an Instance reset on ".$Character->getCharName($guid,$Server->getRealmId($_POST['ir_realm'])),"instancereset",
    			$Server->getRealmId($_POST['ir_realm']));
    		}
    }
    ?>
    <br/>
    <a href="?page=instancereset">重新开始</a>
    
    </div>
    <div id="footer">
    {footer}
    </div>
    </div>
    
    <div id="rightcontent">     
    {login}          
    {serverstatus}  			
    </div>
    </div>
