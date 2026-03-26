<?php
require_once 'includes/header.php';
require_once 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'official') {
    header("Location: index.php");
    exit();
}

// Fetch data for charts
// 1. Issue Status Distribution
$issue_status_query = $conn->query("SELECT status, COUNT(*) as count FROM issues GROUP BY status");
$status_data = ['open' => 0, 'in_progress' => 0, 'resolved' => 0];
while($row = $issue_status_query->fetch_assoc()) $status_data[$row['status']] = $row['count'];

// 2. Top 5 Prioritized Issues
$top_issues_query = $conn->query("SELECT title, upvotes_count FROM issues ORDER BY upvotes_count DESC LIMIT 5");
$top_titles = [];
$top_upvotes = [];
while($row = $top_issues_query->fetch_assoc()) {
    $top_titles[] = substr($row['title'], 0, 15) . '...';
    $top_upvotes[] = $row['upvotes_count'];
}

// 3. Gig Application Statuses
$gig_status_query = $conn->query("SELECT status, COUNT(*) as count FROM gig_applications GROUP BY status");
$gig_data = ['applied' => 0, 'completed' => 0, 'rejected' => 0];
while($row = $gig_status_query->fetch_assoc()) $gig_data[$row['status']] = $row['count'];
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--secondary);">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Rooh<span style="color:var(--primary)">Bharat</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="dashboard_official.php">Issues Hub & Analytics</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><span class="nav-link">Official: <?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
        <li class="nav-item"><a class="nav-link btn btn-sm btn-danger text-white ms-2 px-3" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['msg']); ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <h2 class="fw-bold mb-4">Official Analytics Dashboard</h2>
    
    <!-- Charts Section -->
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-center text-muted">Issues by Status</h6>
                    <canvas id="issueStatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-center text-muted">Top 5 Prioritized Issues</h6>
                    <canvas id="topIssuesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-center text-muted">Youth Gig Applications</h6>
                    <canvas id="gigStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts for Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Issue Status Doughnut Chart
        new Chart(document.getElementById('issueStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Open', 'In Progress', 'Resolved'],
                datasets: [{
                    data: [<?php echo $status_data['open']; ?>, <?php echo $status_data['in_progress']; ?>, <?php echo $status_data['resolved']; ?>],
                    backgroundColor: ['#dc3545', '#ffc107', '#198754']
                }]
            },
            options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });

        // Top Issues Bar Chart
        new Chart(document.getElementById('topIssuesChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($top_titles); ?>,
                datasets: [{
                    label: 'Upvotes',
                    data: <?php echo json_encode($top_upvotes); ?>,
                    backgroundColor: '#0d6efd',
                    borderRadius: 4
                }]
            },
            options: { scales: { y: { beginAtZero: true, suggestedMax: 5 } }, plugins: { legend: { display: false } } }
        });

        // Gig Status Pie Chart
        new Chart(document.getElementById('gigStatusChart'), {
            type: 'pie',
            data: {
                labels: ['Applied', 'Completed', 'Rejected'],
                datasets: [{
                    data: [<?php echo $gig_data['applied']; ?>, <?php echo $gig_data['completed']; ?>, <?php echo $gig_data['rejected']; ?>],
                    backgroundColor: ['#ffc107', '#198754', '#6c757d']
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    </script>
    
    <h2 class="fw-bold mb-4">Jurisdiction Issues</h2>
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Issue</th>
                            <th>Reporter</th>
                            <th>Priority (Upvotes)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT i.*, u.name as reporter_name 
                                FROM issues i 
                                JOIN users u ON i.user_id = u.id 
                                ORDER BY i.status = 'open' DESC, i.upvotes_count DESC";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $status_badge = $row['status'] == 'resolved' ? 'success' : ($row['status'] == 'in_progress' ? 'warning' : 'danger');
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($row['title']); ?>">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </div>
                                        <div class="small text-muted"><i class="bi bi-geo-alt"></i> Lat: <?php echo round($row['latitude'], 4); ?>, Lng: <?php echo round($row['longitude'], 4); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['reporter_name']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill"><i class="bi bi-arrow-up"></i> <?php echo $row['upvotes_count']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $status_badge; ?> text-uppercase"><?php echo $row['status']; ?></span>
                                    </td>
                                    <td>
                                        <form action="api/update_status.php" method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                            <select name="status" class="form-select form-select-sm" style="width: auto;">
                                                <option value="open" <?php echo $row['status']=='open'?'selected':'';?>>Open</option>
                                                <option value="in_progress" <?php echo $row['status']=='in_progress'?'selected':'';?>>In Progress</option>
                                                <option value="resolved" <?php echo $row['status']=='resolved'?'selected':'';?>>Resolved</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center py-4 text-muted">No issues reported in your jurisdiction yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
        <h2 class="fw-bold mb-0">Gig Applications (Youth)</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createGigModal"><i class="bi bi-plus-circle"></i> Create New Gig</button>
    </div>
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Gig Title</th>
                            <th>Applicant (Youth)</th>
                            <th>Rewards</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $gig_sql = "SELECT ga.*, g.title, g.reward_points, g.reward_money, u.name as youth_name 
                                    FROM gig_applications ga 
                                    JOIN gigs g ON ga.gig_id = g.id 
                                    JOIN users u ON ga.user_id = u.id 
                                    ORDER BY ga.status = 'applied' DESC, ga.created_at DESC";
                        $gig_result = $conn->query($gig_sql);
                        
                        if ($gig_result && $gig_result->num_rows > 0) {
                            while($grow = $gig_result->fetch_assoc()) {
                                $g_status_badge = $grow['status'] == 'completed' ? 'success' : ($grow['status'] == 'applied' ? 'warning' : 'secondary');
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grow['title']); ?></td>
                                    <td><?php echo htmlspecialchars($grow['youth_name']); ?></td>
                                    <td><?php echo $grow['reward_points']; ?> pts / ₹<?php echo $grow['reward_money']; ?></td>
                                    <td><span class="badge bg-<?php echo $g_status_badge; ?> text-uppercase"><?php echo $grow['status']; ?></span></td>
                                    <td>
                                        <?php if($grow['status'] == 'applied'): ?>
                                        <div class="d-flex gap-2">
                                            <form action="api/reward_gig.php" method="POST">
                                                <input type="hidden" name="app_id" value="<?php echo $grow['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve & Reward"><i class="bi bi-check-lg"></i> Complete</button>
                                            </form>
                                            <form action="api/reject_gig.php" method="POST">
                                                <input type="hidden" name="app_id" value="<?php echo $grow['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject"><i class="bi bi-x-lg"></i> Reject</button>
                                            </form>
                                        </div>
                                        <?php else: ?>
                                            <span class="text-muted small">Processed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center py-4 text-muted">No youth applications yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Gig Modal -->
    <div class="modal fade" id="createGigModal" tabindex="-1" aria-labelledby="createGigLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createGigLabel">Create a New Civic Gig</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="api/post_gig.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gig Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="E.g., Park Clean-up Weekend">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="Describe the responsibilities..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Reward Points</label>
                                <input type="number" name="reward_points" class="form-control" required min="1" max="500" value="50">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Reward Money (₹)</label>
                                <input type="number" name="reward_money" class="form-control" min="0" value="0" step="0.01">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fs-5 mt-2">Publish Gig</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
