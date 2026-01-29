<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <title>BMS Registration</title>
    <style>
        .register-container {
            max-width: 400px;
            margin: 2rem auto;
        }

        .register-card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .brand-icon {
            font-size: 2.5rem;
            color: var(--bs-primary);
        }
    </style>
</head>

<body class="bg-light">
    <?php include 'include/nav.php' ?>

    <div class="container register-container">
        <div class="card register-card">
            <div class="card-body p-4">
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <i class="fas fa-store brand-icon mb-3"></i>
                    <h3 class="fw-bold">Create Account</h3>
                    <p class="text-muted">Join BMS Ecommerce today</p>
                </div>

                <?php
                if (isset($_POST['add'])) {
                    $login = $_POST['login'];
                    $password = $_POST['password'];
                    $confirm_password = $_POST['confirm_password'] ?? '';

                    if (!empty($login) && !empty($password)) {
                        if ($password === $confirm_password) {
                            require_once 'include/database.php';

                            // Check if login already exists
                            $checkLogin = $pdo->prepare('SELECT COUNT(*) FROM user WHERE login = ?');
                            $checkLogin->execute([$login]);
                            $loginExists = $checkLogin->fetchColumn();

                            if ($loginExists) {
                ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Username already exists!
                                </div>
                            <?php
                            } else {
                                $date = date('Y-m-d');
                                $sqlState = $pdo->prepare('INSERT INTO user VALUES(null,?,?,?)');
                                $sqlState->execute([$login, $password, $date]);
                                header('location:connection.php');
                            }
                        } else {
                            ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Passwords do not match!
                            </div>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Please fill in all fields!
                        </div>
                <?php
                    }
                }
                ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user text-muted me-2"></i>Username
                        </label>
                        <input type="text"
                            class="form-control"
                            name="login"
                            placeholder="Choose a username"
                            value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock text-muted me-2"></i>Password
                        </label>
                        <input type="password"
                            class="form-control"
                            name="password"
                            placeholder="Create a password">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock text-muted me-2"></i>Confirm Password
                        </label>
                        <input type="password"
                            class="form-control"
                            name="confirm_password"
                            placeholder="Confirm your password">
                    </div>

                    <div class="d-grid">
                        <button type="submit"
                            class="btn btn-primary"
                            name="add">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Already have an account?
                        <a href="connection.php" class="text-primary text-decoration-none">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>