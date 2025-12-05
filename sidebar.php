<?php
// Assegurar que a sessão esteja iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir session.php para ter acesso às funções
$sessionPath = dirname(__FILE__) . "/admin/session.php";
if (file_exists($sessionPath)) {
    include_once($sessionPath);
}
?>

<div class="sidebar">
    <div class="sidebar-content">
        <a href="/sistema/" class="sidebar-btn">🏠 Home</a>
        
        <?php
        if (isUserAdmin()) {
            echo '<a href="/sistema/admin/" class="sidebar-btn admin-btn">⚙️ Admin</a>';
        }
        ?>
    </div>
</div>
