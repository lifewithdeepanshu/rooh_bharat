<?php
require_once 'includes/header.php';
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['citizen', 'youth'])) {
    header("Location: index.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--accent);">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard_citizen.php">Rooh<span style="color:var(--secondary)">Bharat</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard_citizen.php">Feed</a></li>
        <li class="nav-item"><a class="nav-link active" href="post_issue.php">Post Issue</a></li>
        <?php if($_SESSION['role'] == 'youth'): ?>
        <li class="nav-item"><a class="nav-link" href="dashboard_youth.php">Civic Gigs</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link btn btn-sm btn-danger text-white ms-2 px-3" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="mb-0 fw-bold" style="color: var(--primary);">Report a Civic Issue</h3>
                </div>
                <div class="card-body p-4">
                    <form action="api/post_issue.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title/Summary</label>
                            <input type="text" name="title" class="form-control" required placeholder="E.g., Pothole on MG Road">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Detailed Description</label>
                            <textarea name="description" class="form-control" rows="4" required placeholder="Describe the issue..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Photo Proof</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                        
                        <!-- Hidden inputs for Location -->
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        
                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-secondary" id="getLocationBtn" onclick="getLocation()">
                                <i class="bi bi-geo-alt"></i> Attach My Location
                            </button>
                            <span id="locationStatus" class="ms-2 text-muted small">Required</span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 fs-5" id="submitBtn" disabled>Post Issue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function getLocation() {
    const status = document.getElementById('locationStatus');
    const btn = document.getElementById('getLocationBtn');
    
    if (navigator.geolocation) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Locating...';
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        status.innerHTML = "Geolocation is not supported by this browser.";
        status.className = "ms-2 text-danger small";
    }
}

function showPosition(position) {
    document.getElementById('latitude').value = position.coords.latitude;
    document.getElementById('longitude').value = position.coords.longitude;
    
    document.getElementById('locationStatus').innerHTML = "Location attached ✓";
    document.getElementById('locationStatus').className = "ms-2 text-success small fw-bold";
    document.getElementById('getLocationBtn').className = "btn btn-success";
    document.getElementById('getLocationBtn').innerHTML = '<i class="bi bi-check-circle"></i> Location Attached';
    
    document.getElementById('submitBtn').disabled = false;
}

function showError(error) {
    const status = document.getElementById('locationStatus');
    status.className = "ms-2 text-danger small";
    document.getElementById('getLocationBtn').innerHTML = '<i class="bi bi-geo-alt"></i> Try Again';
    switch(error.code) {
        case error.PERMISSION_DENIED:
            status.innerHTML = "User denied Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            status.innerHTML = "Location info unavailable."
            break;
        case error.TIMEOUT:
            status.innerHTML = "Request timed out."
            break;
        case error.UNKNOWN_ERROR:
            status.innerHTML = "Unknown error occurred."
            break;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
