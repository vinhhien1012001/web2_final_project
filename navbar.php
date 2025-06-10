<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="bi bi-hospital me-2"></i>
            <span>Hospital Management</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'doctors.php' ? 'active' : '' ?>" href="doctors.php">
                        <i class="bi bi-person-badge"></i> Doctors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'patients.php' ? 'active' : '' ?>" href="patients.php">
                        <i class="bi bi-people"></i> Patients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'appointments.php' ? 'active' : '' ?>" href="appointments.php">
                        <i class="bi bi-calendar-check"></i> Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'medications.php' ? 'active' : '' ?>" href="medications.php">
                        <i class="bi bi-capsule"></i> Medications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'feedbacks.php' ? 'active' : '' ?>" href="feedbacks.php">
                        <i class="bi bi-star"></i> Feedbacks
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        <?php echo $_SESSION['email']; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
