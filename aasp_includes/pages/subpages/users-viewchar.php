<?php global $Page, $Server, $Account, $Character, $conn; ?>
<div class="box_right_title"><?php echo $Page->titleLink(); ?> &raquo; 角色管理</div>
角色选择:  <?php echo $Account->getCharName($_GET['guid'],$_GET['rid']); ?>
<?php
$Server->connectToRealmDB($_GET['rid']);

$usersTotal = mysqli_query($conn, "SELECT name,race,account,class,level,money,leveltime,totaltime,online,latency,gender FROM characters WHERE guid='".$_GET['guid']."'");
$row = mysqli_fetch_assoc($usersTotal);
?>
<hr/>
<table style="width: 100%;">
    <tr>
        <td>角色名称</td>
        <td><input type="text" value="<?php echo $row['name']; ?>" class="noremove" id="editchar_name"/></td>
    </tr>
    <tr>
        <td>账号</td>
        <td><input type="text" value="<?php echo $Account->getAccName($row['account']); ?>" class="noremove" id="editchar_accname"/>
        <a href="?p=users&s=manage&user=<?php echo strtolower($Account->getAccName($row['account'])); ?>">查看</a></td>
    </tr>
    <tr>
        <td>种族</td>
        <td>
        	<select id="editchar_race">
            	<option <?php if($row['race'] == 1) echo 'selected'; ?> value="1">人类</option>
                <option <?php if($row['race'] == 3) echo 'selected'; ?> value="3">矮人</option>
                <option <?php if($row['race'] == 4) echo 'selected'; ?> value="4">暗夜精灵</option>
                <option <?php if($row['race'] == 7) echo 'selected'; ?> value="7">侏儒</option>
                <option <?php if($row['race'] == 11) echo 'selected'; ?> value="11">德莱尼</option>
                 <?php if($GLOBALS['core_expansion'] >= 3) ?>
                	<option <?php if($row['race'] == 22) echo 'selected'; ?> value="22">狼人</option>
                <option <?php if($row['race'] == 2) echo 'selected'; ?> value="2">兽人</option>
                <option <?php if($row['race'] == 6) echo 'selected'; ?> value="6">牛头人</option>
                <option <?php if($row['race'] == 8) echo 'selected'; ?> value="8">巨魔</option>
                <option <?php if($row['race'] == 5) echo 'selected'; ?> value="5">亡灵</option>
    			<option <?php if($row['race'] == 10) echo 'selected'; ?> value="10">血精灵</option>
                <?php if($GLOBALS['core_expansion'] >= 3) ?>
                	<option <?php if($row['race'] == 9) echo 'selected'; ?> value="9">地精</option>
                <?php if($GLOBALS['core_expansion'] >= 4) ?>
                	<option <?php if($row['race'] == NULL) echo 'selected'; ?> value="NULL">熊猫人</option>
            </select>
        </td>
    </tr>
    <tr>   
        <td>职业</td>
        <td>
        	<select id="editchar_class">
            	<option <?php if($row['class'] == 1) echo 'selected'; ?> value="1">战士</option>
                <option <?php if($row['class'] == 2) echo 'selected'; ?> value="2">圣骑士</option>
                <option <?php if($row['class'] == 11) echo 'selected'; ?> value="11">德鲁伊</option>
                <option <?php if($row['class'] == 3) echo 'selected'; ?> value="3">猎人</option>
                <option <?php if($row['class'] == 5) echo 'selected'; ?> value="5">牧师</option>
                 <?php if($GLOBALS['core_expansion'] >= 2) ?>
                	<option <?php if($row['class'] == 6) echo 'selected'; ?> value="6">死亡骑士</option>
                <option <?php if($row['class'] == 9) echo 'selected'; ?> value="9">术士</option>
                <option <?php if($row['class'] == 7) echo 'selected'; ?> value="7">萨满</option>
                <option <?php if($row['class'] == 4) echo 'selected'; ?> value="4">盗贼</option>
                <option <?php if($row['class'] == 8) echo 'selected'; ?> value="8">法师</option>
                <?php if($GLOBALS['core_expansion'] >= 4) ?>
                	<option <?php if($row['class'] == 12) echo 'selected'; ?> value="12">武僧</option>
                <?php if($GLOBALS['core_expansion'] >= 5) ?>
                    <option <?php if($row['class'] == 13) echo 'selected'; ?> value="13">恶魔猎手</option>
            </select>
        </td>
    </tr>
    <tr>   
        <td>性别</td>
        <td>
        	<select id="editchar_gender">
            	<option <?php if($row['gender'] == 0) echo 'selected'; ?> value="0">男性</option>
                <option <?php if($row['gender'] == 1) echo 'selected'; ?> value="1">女性</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>等级</td>
        <td><input type="text" value="<?php echo $row['level']; ?>" class="noremove" id="editchar_level"/></td>
    </tr>
    <tr>    
        <td>钱（金币）</td>
        <td><input type="text" value="<?php echo floor($row['money'] / 10000); ?>" class="noremove" id="editchar_money"/></td>
    </tr>
    <tr>
        <td>练级时长</td>
        <td><input type="text" value="<?php echo $row['leveltime']; ?>" disabled="disabled"/></td>
    </tr>
    <tr>    
        <td>总时长</td>
        <td><input type="text" value="<?php echo $row['totaltime']; ?>" disabled="disabled"/></td>
    </tr>
    <tr>
        <td>状态</td>
        <td>
    	<?php 
            if ($row['online']==0)
                echo '<input type="text" value="离线" disabled="disabled"/>';
		    else
                echo '<input type="text" value="在线" disabled="disabled"/>'; 
    	?>              
        </td>
    </tr>
    <tr>    
        <td>延迟</td>
        <td><input type="text" value="<?php echo $row['latency']; ?>" disabled="disabled"/></td>
    </tr>
    <tr>
    	<td></td>
        <td><input type="submit" value="Save" onclick="editChar('<?php echo $_GET['guid']; ?>','<?php echo $_GET['rid']; ?>')"/> 
        	<i>* 注意</i>: 如果角色是在线的，你不能编辑任何数据。</td>
    </tr>
</table>
<hr/>