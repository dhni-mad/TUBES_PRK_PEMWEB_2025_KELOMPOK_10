<?php
$full_name = $_SESSION['full_name'] ?? 'Administrator';
$username = $_SESSION['username'] ?? 'admin';
?>
<header class="main-header">
    <div class="header-left">
        <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
    </div>
    <div class="header-right">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo strtoupper(substr($full_name, 0, 1)); ?>
            </div>
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($full_name); ?></span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
    </div>
</header>
