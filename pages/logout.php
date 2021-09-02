<?php

global $Account, $Connect;
$conn = $Connect->connectToDB();
$Account->isNotLoggedIn();

echo "<h2>注销</h2>";

$Account->logOut($_GET['last_page']);