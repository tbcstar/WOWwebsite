<?php
account::isNotLoggedIn();

echo "<h2>注销</h2>";

account::logOut($_GET['last_page']);
?>