<?php
    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";
    include "../functions.php";
    
    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();
    
    $GameServer->selectDB("webdb", $conn);

    # Organized Alphabeticaly

    switch ($_POST['action'])  
    {
    case "dshop":
    {
		$result = $conn->query("SELECT * FROM shoplog WHERE account=". $conn->escape_string($_POST['id']) ." AND shop='donate';");
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
                    <a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
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
            $result = $conn->query("SELECT paymentstatus, mc_gross, datecreation FROM payments_log WHERE userid=". $conn->escape_string($_POST['id']) .";");
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
            $input      = $conn->escape_string($_POST['input']);
            $shop       = $conn->escape_string($_POST['shop']); ?>
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
                $loopRealms = $conn->query("SELECT id FROM realms;");
                while ($row = $loopRealms->fetch_assoc())
        		{
                $GameServer->connectToRealmDB($row['id']);
                $result = $conn->query("SELECT guid FROM characters WHERE name LIKE '%". $input ."%';");
                if ($result->num_rows > 0)
            {
                $row    = $result->fetch_assoc();
                $GameServer->selectDB('webdb');
                $result = $conn->query("SELECT * FROM shoplog WHERE shop='". $shop ."' AND char_id=". $row['guid'] .";");
    
                while ($row = $result->fetch_assoc())
            { ?>
                <tr class="center">
                    <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                    <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                    <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                    <td>
                        <a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
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
                $result = $conn->query("SELECT id FROM account WHERE username LIKE '%". $input ."%';");
                if ($result->num_rows > 0)
                {
                    $row    = $result->fetch_assoc();
                    $GameServer->selectDB("webdb", $conn);
                    $result = $conn->query("SELECT * FROM shoplog WHERE shop='". $shop ."' AND account=". $row['id'] .";");

                    while ($row = $result->fetch_assoc())
                    { ?>
                        <tr class="center">
                            <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                            <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                            <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                            <td>
                                <a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
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
                $result = $conn->query("SELECT entry FROM item_template WHERE name LIKE '%". $input ."%';");
                if ($result->num_rows > 0)
                {
                    $row    = $result->fetch_assoc();
                    $GameServer->selectDB('webdb');
                    $result = $conn->query("SELECT * FROM shoplog WHERE shop='". $shop ."' AND entry=". $row['entry'] .";");

                    while ($row = $result->fetch_assoc())
                    { ?>
                        <tr class="center">
                            <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                            <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                            <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                            <td>
                                <a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
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
                $result = $conn->query("SELECT * FROM shoplog WHERE shop='". $shop ."' AND date LIKE '%". $input ."%';");

                while ($row = $result->fetch_assoc())
                { ?>
                    <tr class="center">
                        <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                        <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                        <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                        <td>
                            <a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
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
                    $result = $conn->query("SELECT * FROM shoplog WHERE shop='". $shop ."' ORDER BY id DESC LIMIT 10;");

                    while ($row = $result->fetch_assoc())
                    { ?>
                        <tr class="center">
                            <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                            <td><?php echo $GameAccount->getCharName($row['char_id'], $row['realm_id']); ?></td>
                            <td><?php echo $GameServer->getRealmName($row['realm_id']); ?></td>
                            <td>
                                <a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
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
            $result = $conn->query("SELECT * FROM shoplog WHERE account=". $conn->escape_string($_POST['id']) ." AND shop='vote';");
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
                                <a href="http://<?php echo $GLOBALS['tooltip_href']; ?>item=<?php echo $row['entry']; ?>" title="" target="_blank">
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