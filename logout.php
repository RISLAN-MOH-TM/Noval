<?php
require_once 'includes/user_manager.php';

SessionManager::init();
SessionManager::destroy();

header('Location: index.php');
exit;
?>