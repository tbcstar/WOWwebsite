<?php

global $Account, $Database;
$conn = $Database->database();
$Account->isNotLoggedIn();

echo "<h2>注销</h2>";

$Account->logOut($_GET['last_page']);