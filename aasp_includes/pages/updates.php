<div class="box_right_title">Updates</div>
<script type="text/javascript">
function getLatestVersions() {
	$(".hidden_version").fadeIn("fast");
}
</script>
<table width="100%">
       <tr>
            <td>当前版本：r_01</td><td class="hidden_version">可用版本:r_02</td>
       </tr>
       <tr>
            <td>当前数据库版本:r_01</td><td class="hidden_version">可用数据库版本:r_02</td>
       </tr>
       <tr>
           <td><input type="submit" value="检查可用版本" onclick="getLatestVersions()"/></td>
           <td class="hidden_version"><input type="submit" value="Update" onclick="alert('嘿，这是一个恶作剧。此功能尚未实现！)"/></td>
       </tr>
</table>