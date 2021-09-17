<?php
    global $GamePage; 
?>
<div class="box_right_title"><?php echo $GamePage->titleLink(); ?> &raquo; 商品管理</div>
<table width="100%">
        <tr valign="top">
             <td style="text-align: left; width: 300px;">
                <h3>修改唯一性物品</h3>
             <p/>条目<br/>
            <input type="text" style="width: 200px;" id="modsingle_entry"/><br/>价格<br/>
            <input type="text" style="width: 200px;" id="modsingle_price"/><br/>商城<br/>

             <select style="width: 205px;" id="modsingle_shop">
                     <option value="vote">投票商店</option>
                     <option value="donate">公益商城</option>
             </select><br/>

             <input type="submit" value="Update" onclick="modSingleItem()"/>
             <input type="submit" value="Remove" onclick="delSingleItem()"/>
             </td>

             <td style="text-align: left; width: 300px;"><h3>修改重复性物品</h3>
             <p/>
             物品等级<br/>
             <select style="width: 140px;" id="modmulti_il_from">
                    <?php
                        for ($i = 1; $i <= DATA['website']['item_level'][3]; $i++)
                        {
						    echo "<option>".$i."</option>";
					    } ?>
             </select>
             &
             <select style="width: 140px;" id="modmulti_il_to">
                    <?php
                        for ($i = DATA['website']['item_level'][3]; $i >= 1; $i--)
                        {
						    echo "<option>".$i."</option>";
					    } ?>
            </select>
            <br/>价格<br/>
            <input type="text" style="width: 200px;" id="modmulti_price"/><br/>品质<br/>

             <select style="width: 205px;" id="modmulti_quality">
                     <option value="all">全部</option>
                     <option value="0">灰色</option>
                     <option value="1">白色</option>
                     <option value="2">绿色</option>
                     <option value="3">蓝色</option>
                     <option value="4">紫色</option>
                     <option value="5">橙色</option>
            </select>
            <br/>类型<br/>
             <select id="modmulti_type" style="width: 205px;">
                                <option value="all">全部</option>
                                <option value="0">消耗品</option>
                                <option value="1">背包</option>
                                <option value="2">武器</option>
                                <option value="3">珠宝</option>
                                <option value="4">护甲</option>
                                <option value="15">其它</option>
                                <option value="16">图腾</option>
                                <option value="15-5">坐骑</option>
                                <option value="15-2">宠物</option>
            </select>	
            <br/>商城<br/>
             <select style="width: 205px;" id="modmulti_shop">
                     <option value="vote">投票商店</option>
                     <option value="donate">公益商城</option>
             </select><br/>

             <input type="submit" value="Update" onclick="modMultiItem()"/>
             <input type="submit" value="Remove" onclick="delMultiItem()"/>
             </td>
        </tr>
</table>