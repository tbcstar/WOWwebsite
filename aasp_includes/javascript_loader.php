    <script type="text/javascript" src="../aasp_includes/js/interface.js"></script>
    <script type="text/javascript" src="../aasp_includes/js/account.js"></script>
    <script type="text/javascript" src="../aasp_includes/js/server.js"></script>
    <script type="text/javascript" src="../aasp_includes/js/news.js"></script>
    <script type="text/javascript" src="../aasp_includes/js/logs.js"></script>
    <script type="text/javascript" src="../aasp_includes/js/shop.js"></script>
    <?php
        if ( @DATA['website']['core_expansion'] > 2)
        {
    		//核心在WOTLK之上。 使用WoWHead,反之使用cavernoftime
    		echo '<script type="text/javascript" src="https://static.wowhead.com/widgets/power.js"></script>';
        }
        else
        {
    		echo '<script type="text/javascript" src="https://cdn.cavernoftime.com/api/tooltip.js"></script>';
        }
    ?>
    <script type="text/javascript" src="../aasp_includes/js/wysiwyg.js"></script>
    <script type="text/javascript" src="../aasp_includes/js/wysiwyg/wysiwyg.image.js"></script>
    <script type="text/javascript" src="../aasp_includes/js/wysiwyg/wysiwyg.link.js"></script>
    <script type="text/javascript" src="../aasp_includes/js/wysiwyg/wysiwyg.table.js"></script>
    
	<script type="text/javascript">
		$(function (){ $('#wysiwyg').wysiwyg(); });
	</script> 