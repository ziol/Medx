<?php
session_start();
// Sob session data muche fela hobe
session_unset();
session_destroy();

// Sorasori index.php (Home Page) e pathiye deya hobe
header("Location: index.php");
exit;
?>