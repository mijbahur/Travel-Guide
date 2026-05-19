<?php
$activePage = $activePage ?? '';
?>
<nav class="navbar">
    <a href="/Travel-Guide/view/scout/dashboard.php" class="brand">&#9992; Travel<span>Guide</span> <small style="font-size:.7rem;opacity:.7">Scout - <?= $_SESSION['name'] ?></small></a> 

    <nav>
        <a href="/Travel-Guide/view/scout/dashboard.php"      class="<?= $activePage==='dashboard'   ?'active':'' ?>">Dashboard</a>
        <a href="/Travel-Guide/view/scout/create_request.php" class="<?= $activePage==='create'      ?'active':'' ?>">New Request</a>
        <a href="/Travel-Guide/view/scout/my_requests.php"    class="<?= $activePage==='my-requests' ?'active':'' ?>">My Requests</a>
        <a href="/Travel-Guide/view/scout/approved_posts.php" class="<?= $activePage==='approved'    ?'active':'' ?>">Approved Posts</a>
        <!-- <a href="../../controller/logout.php" class="logout">Logout</a> -->
        <a href="/Travel-Guide/view/profile/index.php">Profile</a>
        <a href="../../controller/auth/AuthController.php?action=logout" class="btn-nav">Logout</a>
         
    </nav>
</nav>
