<?php
    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";
    include "../functions.php";
    
    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();
    
    $GameServer->selectDB("webdb");

    # Organized Alphabeticaly

    switch ($_POST['action'])  
    {
    case "dshop":
    {
		$result = $Database->select("shoplog", null, null, "account=". $Database->conn->escape_string($_POST['id']) ." AND shop='donate'")->get_result();
        if ($result->num_rows == 0)
		{
			echo "<b color='red'>没有发现此帐户的日志。</b>";
		}
		else 
		{
		?> <table width="100%">
               <tr>
                   <th>物品</th>
                   <th>角色</th>
                   <th>日期</th>
                   <th>金额</th>
               </tr>
        <?php while ($row = $result->fetch_assoc())
            { ?>
            <tr>
                <td>
                    <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
                        <?php echo $GameServer->getItemName($row['entry']); ?>
                    </a>
                </td>
                <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                <td><?php echo $row['date']; ?></td>   
                <td>x<?php echo $row['amount']; ?></td>
            </tr>
		<?php }
		echo '</table>';
	}

            break;
        }

        case "payments":
        {
            $result = $Database->select("payments_log", "paymentstatus, mc_gross, datecreation", null, "userid=". $Database->conn->escape_string($_POST['id']))->get_result();
            if ($result->num_rows == 0)
            {
                echo "<b color='red'>未找到此帐户的付款信息。</b>";
            }
            else
            { ?> 
                <table width="100%">
                    <tr>
                        <th>金额</th>
                        <th>支付状态</th>
                        <th>日期</th>
                    </tr>
                    <?php
                    while ($row = $result->fetch_assoc())
                    { ?>
                    <tr>
                        <td><?php echo $row['mc_gross']; ?>$</td>
                        <td><?php echo $row['paymentstatus']; ?></td>
                        <td><?php echo $row['datecreation']; ?></td>   
                    </tr>
                    <?php }
                echo "</table>";
            }

            break;
        }

        case "search":
        {
            $input      = $Database->conn->escape_string($_POST['input']);
            $shop       = $Database->conn->escape_string($_POST['shop']); ?>
            <table width="100%">
                <tr>
                    <th>账号</th>
                    <th>角色</th>
                    <th>服务器</th>
                    <th>物品</th>
                    <th>日期</th>
                    <th>金额</th>
                </tr>

                <?php
                //Search via character name...
                $loopRealms = $Database->select("realms", "id")->get_result();
                while ($row = $loopRealms->fetch_assoc())
        		{
                $GameServer->realm($row['id']);
                $result = $Database->select("characters", "guid", null, "name LIKE '%". $input ."%'")->get_result();
                if ($result->num_rows > 0)
            {
                $row    = $result->fetch_assoc();
                $GameServer->selectDB('webdb');
                $result = $Database->select("shoplog", null, null, "shop='". $shop ."' AND char_id=". $row['guid'] .";")->get_result();
    
                while ($row = $result->fetch_assoc())
            { ?>
                <tr class="center">
                    <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                    <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                    <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                    <td>
                        <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
    				 	    <?php echo $GameServer->getItemName($row['entry']);?>
    				 	</a>
    				</td>
                <td><?php echo $row['date']; ?></td>
                <td>x<?php echo $row['amount']; ?></td>
            </tr><?php
            }
		}
	}	
                //Search via account name
                $GameServer->selectDB("logondb");
                $result = $Database->select("account", "id", null, "username LIKE '%$input%'")->get_result();
                if ($result->num_rows > 0)
                {
                    $row    = $result->fetch_assoc();
                    $GameServer->selectDB("webdb", $conn);
                    $result = $Database->select("shoplog", null, null, "shop='$shop' AND account=". $row['id'])->get_result();

                    while ($row = $result->fetch_assoc())
                    { ?>
                        <tr class="center">
                            <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                            <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                            <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                            <td>
                                <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
                                    <?php echo $GameServer->getItemName($row['entry']); ?>
                                </a>
                            </td>
                            <td><?php echo $row['date']; ?></td>
                            <td>x<?php echo $row['amount']; ?></td>
                        </tr><?php
                    }
                }

                //Search via item name
                $GameServer->selectDB('worlddb');
                $result = $Database->select("item_template", "entry", null, "name LIKE '%$input%'")->get_result();
                if ($result->num_rows > 0)
                {
                    $row    = $result->fetch_assoc();
                    $GameServer->selectDB('webdb');
                    $result = $Database->select("shoplog", null, null, "shop='$shop' AND entry=". $row['entry'])->get_result();

                    while ($row = $result->fetch_assoc())
                    { ?>
                        <tr class="center">
                            <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                            <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                            <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                            <td>
                                <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
                                    <?php echo $GameServer->getItemName($row['entry']); ?>
                                </a>
                            </td>
                            <td><?php echo $row['date']; ?></td>
                            <td>x<?php echo $row['amount']; ?></td>
                        </tr><?php
                    }
                }

                //Search via date
                $GameServer->selectDB('webdb');
                $result = $Database->select("shoplog", null, null, "shop='". $shop ."' AND date LIKE '%". $input ."%'")->get_result();

                while ($row = $result->fetch_assoc())
                { ?>
                    <tr class="center">
                        <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                        <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                        <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                        <td>
                            <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
                                <?php echo $GameServer->getItemName($row['entry']); ?>
                            </a>
                        </td>
                        <td><?php echo $row['date']; ?></td>
                        <td>x<?php echo $row['amount']; ?></td>
                    </tr><?php
                }

                if ($input == "Search...")
                {
                    //View last 10 logs
                    $GameServer->selectDB('webdb');
                    $result = $Database->select("shoplog", null, null, "shop='". $shop ."' ORDER BY id DESC LIMIT 10")->get_result();

                    while ($row = $result->fetch_assoc())
                    { ?>
                        <tr class="center">
                            <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                            <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                            <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                            <td>
                                <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
                                    <?php echo $GameServer->getItemName($row['entry']); ?>
                                </a>
                            </td>
                            <td><?php echo $row['date']; ?></td>
                            <td>x<?php echo $row['amount']; ?></td>
                        </tr><?php 
                    }
                } ?>	

            </table><?php

            break;
        } 

        case "vshop":
		{
            $result = $Database->select("shoplog", null, null, "account=". $Database->conn->escape_string($_POST['id']) ." AND shop='vote'")->get_result();
            if ($result->num_rows == 0)
            {
                echo "<b color='red'>No logs was found for this account.</b>";
            }
            else
            { ?>
                <table width="100%">
                    <tr>
                        <th>物品</th>
                        <th>角色</th>
                        <th>日期</th>
                        <th>金额</th>
                    </tr><?php 
                    while ($row = $result->fetch_assoc())
                    { ?>
                        <tr>
                            <td>
                                <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
                                    <?php echo $GameServer->getItemName($row['entry']); ?>
                                </a>
                            </td>
                            <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td>x<?php echo $row['amount']; ?></td>
                        </tr><?php
                    }
                echo "</table>";
            }

            break;
        }
    }