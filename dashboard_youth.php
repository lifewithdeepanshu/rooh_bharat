<?php
require_once 'includes/header.php';
require_once 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'youth') {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary);">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Rooh<span style="color:var(--accent)">Bharat</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard_citizen.php">Feed</a></li>
        <li class="nav-item"><a class="nav-link" href="post_issue.php">Post Issue</a></li>
        <li class="nav-item"><a class="nav-link active" href="dashboard_youth.php">Civic Gigs</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><span class="nav-link">Youth: <?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
        <li class="nav-item"><a class="nav-link btn btn-sm btn-danger text-white ms-2 px-3" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold mb-0">Active Civic Gigs</h2>
            <p class="text-muted">Take up tasks, earn points, and build your civic portfolio.</p>
        </div>
        <div class="col-auto">
            <div class="card bg-warning text-dark border-0 shadow-sm">
                <div class="card-body py-2 px-4 text-center">
                    <div class="small fw-bold text-uppercase">My Points</div>
                    <?php
                    $points_query = "SELECT points FROM users WHERE id = $user_id";
                    $res = $conn->query($points_query);
                    $points = $res->num_rows > 0 ? $res->fetch_assoc()['points'] : 0;
                    ?>
                    <h4 class="mb-0 fw-bold"><i class="bi bi-star-fill"></i> <?php echo $points; ?></h4>
                </div>
            </div>
        </div>
    </div>
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['msg']); ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Mock Gigs for MVP -->
    <div class="row">
        <?php
        $sql = "SELECT g.*, 
                (SELECT status FROM gig_applications WHERE gig_id = g.id AND user_id = $user_id) as my_status 
                FROM gigs g ORDER BY g.created_at DESC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-light text-dark border"><i class="bi bi-tag"></i> Surveying</span>
                                <span class="badge bg-success"><i class="bi bi-cash"></i> ₹<?php echo $row['reward_money']; ?> + <?php echo $row['reward_points']; ?> pts</span>
                            </div>
                            <h5 class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="text-muted small mb-4"><?php echo htmlspecialchars($row['description']); ?></p>
                            
                            <?php if($row['my_status']): ?>
                                <button class="btn btn-secondary w-100 py-2 fw-semibold" disabled>
                                    <?php echo ucfirst($row['my_status']); ?>
                                </button>
                            <?php else: ?>
                                <form action="api/apply_gig.php" method="POST">
                                    <input type="hidden" name="gig_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-outline-primary w-100 py-2 fw-semibold">Apply Now</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info border-0 shadow-sm">No active gigs available at the moment. Please check back later!</div></div>';
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
