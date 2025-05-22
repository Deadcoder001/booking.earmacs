<?php
session_start();
session_unset();
session_destroy();
header("Location: agent_login.php");
exit;
