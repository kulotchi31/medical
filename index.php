<?php

include 'backend/login_process.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/login_signup.css"></style>
    <link rel="icon" href="img/NEUST.png" type="image/png">
    <style>
        body {
            background: url('img/top-universities-in-nueva-ecija.webp') no-repeat center center fixed;
            background-size: cover;
        }

        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7); /* 70% opacity */
            z-index: -1;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 75vh;
        }

        .card {
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>

<body>
    <div class="background-overlay"></div>
    <div class="container mt-5">
        <div class="card shadow content">
            <div class="card-header py-3 text-center">
                <img src="img\neust_logo-1.png" alt="Logo" class="neustlogo">
                <h5 class="m-0 font-weight-bold text-primary">NUEVA ECIJA UNIVERSITY OF SCIENCE AND TECHNOLOGY</h5>
                <h6 class="m-0 font-weight-bold text-primary">MEDICAL INVENTORY SYSTEM</h6>
            </div>
            <div class="card-body">
                <form action="backend/login_process" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
            e.preventDefault();
        }
    });
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });


        <?php if (isset($_SESSION['message'])): ?>
            Swal.fire({
                icon: "<?php echo $_SESSION['message_type']; ?>",
                title: "<?php echo $_SESSION['message']; ?>",
            });
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
