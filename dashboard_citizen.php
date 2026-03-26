<?php
require_once 'includes/header.php';
require_once 'config/db.php';
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['citizen', 'youth'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--accent);">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Rooh<span style="color:var(--secondary)">Bharat</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="dashboard_citizen.php">Feed</a></li>
        <li class="nav-item"><a class="nav-link" href="post_issue.php">Post Issue</a></li>
        <?php if($_SESSION['role'] == 'youth'): ?>
        <li class="nav-item"><a class="nav-link" href="dashboard_youth.php">Civic Gigs</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-sm btn-danger text-white ms-2 px-3" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold mb-0">Local Civic Feed</h2>
            <p class="text-muted mb-0">Issues around you sorted by priority</p>
        </div>
        <div class="col-auto me-2">
            <?php
            $pts_sql = "SELECT points FROM users WHERE id = $user_id";
            $pts_res = $conn->query($pts_sql);
            $points = $pts_res->num_rows > 0 ? $pts_res->fetch_assoc()['points'] : 0;
            $badge = $points >= 100 ? '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Civic Champion</span>' : '<span class="badge bg-secondary"><i class="bi bi-person"></i> Citizen</span>';
            ?>
            <div class="bg-light rounded-2 px-3 py-2 border shadow-sm">
                <span class="fw-bold me-2"><i class="bi bi-award text-primary shadow-sm" style="font-size: 1.1rem;"></i> <?php echo $points; ?> pts</span>
                <?php echo $badge; ?>
            </div>
        </div>
        <div class="col-auto">
            <a href="post_issue.php" class="btn btn-primary shadow-sm"><i class="bi bi-plus-circle"></i> New Issue</a>
        </div>
    </div>
    
    <div class="row">
        <?php
        $sql = "SELECT i.*, u.name as author_name, 
                (SELECT COUNT(*) FROM upvotes WHERE issue_id = i.id AND user_id = $user_id) as my_upvote 
                FROM issues i 
                JOIN users u ON i.user_id = u.id 
                ORDER BY i.upvotes_count DESC, i.created_at DESC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $status_color = $row['status'] == 'resolved' ? 'success' : ($row['status'] == 'in_progress' ? 'warning' : 'danger');
                $my_upvote = $row['my_upvote'] > 0;
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <?php if($row['photo_url']): ?>
                            <img src="<?php echo htmlspecialchars($row['photo_url']); ?>" class="card-img-top" alt="Issue photo" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-card-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-<?php echo $status_color; ?> text-uppercase text-xs"><?php echo $row['status']; ?></span>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> Location attached</small>
                            </div>
                            <h5 class="card-title fw-bold text-truncate" title="<?php echo htmlspecialchars($row['title']); ?>">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h5>
                            <p class="card-text text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <div class="small fw-semibold text-muted">
                                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($row['author_name']); ?>
                                </div>
                                <button class="btn btn-sm <?php echo $my_upvote ? 'btn-success' : 'btn-outline-success'; ?> upvote-btn" data-id="<?php echo $row['id']; ?>">
                                    <i class="bi bi-arrow-up-circle<?php echo $my_upvote ? '-fill' : ''; ?>"></i> 
                                    <span class="upvote-count"><?php echo $row['upvotes_count']; ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info border-0 shadow-sm">No issues reported yet. Be the first to start a civic action!</div></div>';
        }
        ?>
    </div>
</div>

<script>
document.querySelectorAll('.upvote-btn').forEach(button => {
    button.addEventListener('click', function() {
        const issueId = this.getAttribute('data-id');
        const countSpan = this.querySelector('.upvote-count');
        const icon = this.querySelector('i');
        const isUpvoted = this.classList.contains('btn-success');
        
        fetch('api/upvote.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'issue_id=' + issueId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                countSpan.textContent = data.new_count;
                if(data.action === 'added') {
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');
                    icon.classList.remove('bi-arrow-up-circle');
                    icon.classList.add('bi-arrow-up-circle-fill');
                } else {
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-success');
                    icon.classList.remove('bi-arrow-up-circle-fill');
                    icon.classList.add('bi-arrow-up-circle');
                }
            } else {
                alert(data.message);
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
