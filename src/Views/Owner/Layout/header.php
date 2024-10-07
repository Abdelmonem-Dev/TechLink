<div class="navbar">
    <div class="left">
        <a href="Dashboard.php" class="logo">TechLink</a>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
    </div>
    <div class="right">
        <div class="notifications">🔔</div>
        <?php if ($isAuthenticated): ?>
            <form action="../auth/logout.php" method="post" style="display: inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        <?php endif; ?>
    </div>
</div>
