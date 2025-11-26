<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

    <BR><img src="../img/neust.png" alt="Logo" class="neustlogo">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="./m_dashboard.php">
        <div class="sidebar-brand-text mx-3">NEUST STUDENT MEDICAL RECORD</div>
    </a>
    <hr class="sidebar-divider my-1">

    <li class="nav-item">
        <a class="nav-link" href="../frontend/m_dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <div class="sidebar-heading">
        Interface
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMedical"
            aria-expanded="true" aria-controls="collapseMedical">
            <i class="fas fa-fw fa-user-md"></i>
            <span>Medical</span>
        </a>
        <div id="collapseMedical" class="collapse" aria-labelledby="headingMedical" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="../frontend/Data">Add Records</a>
                <a class="collapse-item" href="medical_record_v2">Medical Records</a>
                <a class="collapse-item" href="physical_examination">physical_examination</a>
                <a class="collapse-item" href="../frontend/medical_treatment_record">Treatment Record</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDental" aria-expanded="true"
            aria-controls="collapseDental">
            <i class="fas fa-fw fa-tooth"></i>
            <span>Dental</span>
        </a>
        <div id="collapseDental" class="collapse" aria-labelledby="headingDental" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="../frontend/dental_record">Dental Records</a>
                <a class="collapse-item" href="../frontend/medical_treatment_record">Treatment Record</a>
            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link" href="../frontend/Medical_Certificate">
            <i class="fas fa-fw fa-certificate"></i>
            <span>Medical Certificate</span>
        </a>
    </li>


    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
         <div class="sidebar-heading">
        ADDONS
    </div>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdditional"
                aria-expanded="true" aria-controls="collapseMedical">
                <i class="fas fa-fw fa-plus"></i>
                <span>Additional</span>
            </a>
            <div id="collapseAdditional" class="collapse" aria-labelledby="headingAdditional"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="add_account">
                        <i class="fas fa-fw fa-user-plus"></i>
                        <span>Add Account</span>
                    </a>

                    <a class="collapse-item" href="add_department">
                        <i class="fas fa-fw fa-building"></i>
                        <span>Add Department</span>
                    </a>

                    <a class="collapse-item" href="add_campus">
                        <i class="fas fa-fw fa-university"></i>
                        <span>Add Campus</span>
                    </a>

                    <a class="collapse-item" href="add_case">
                        <i class="fas fa-fw fa-thermometer-three-quarters"></i>
                        <span>Add Cases</span>
                    </a>

                    <a class="collapse-item" href="add_medicine">
                        <i class="fas fa-fw fa-plus-square"></i>
                        <span>Add Medicine</span>
                    </a>
                </div>
            </div>
        </li>






        <li class="nav-item">
            <a class="nav-link" href="reports">
                <i class="fas fa-fw fa-user-plus"></i>
                <span>Reports</span>
            </a>
        </li>

    <?php endif; ?>
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>





    <li class="nav-item mt-auto">
        <form action="../backend/logout.php" method="POST" id="logout-form" style="display: none;">
            <button type="submit" class="nav-link btn btn-link text-white">
                <i class="fas fa-fw fa-sign-out-alt"></i> Logout
            </button>
        </form>
        <a href="../backend/logout.php" class="nav-link" onclick="confirmLogout(event)">
            <i class="fas fa-fw fa-sign-out-alt"></i> Logout
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
</ul>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmLogout(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to log out?",
            imageUrl: '../img/Logout.png',
            imageWidth: 100,
            imageHeight: 100,
            showCancelButton: true,
            cancelButtonText: 'cancel',
            confirmButtonText: 'log out!',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("logout-form").submit();
            }
        });
    }
</script>