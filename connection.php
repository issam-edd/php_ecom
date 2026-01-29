<?php
session_start();
$connect = false;
if (isset($_SESSION['user'])) {
    $connect = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <title>BMS Login</title>
    <style>
        .login-container {
            max-width: 400px;
            margin: 2rem auto;
        }

        .login-card {
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

    <div class="container login-container">
        <div class="card login-card">
            <div class="card-body p-4">
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <i class="fas fa-store brand-icon mb-3"></i>
                    <h3 class="fw-bold">Welcome to BMS</h3>
                    <p class="text-muted">Please login to your account</p>
                </div>

                <?php
                if (isset($_POST['connection'])) {
                    $login = $_POST['login'];
                    $password = $_POST['password'];

                    if (!empty($login) && !empty($password)) {
                        require_once 'include/database.php';
                        $sqlState = $pdo->prepare('SELECT * FROM user WHERE login=? AND password=?');
                        $sqlState->execute([$login, $password]);

                        if ($sqlState->rowCount() >= 1) {
                            $_SESSION['user'] = $sqlState->fetch();
                            header('location:admin.php');
                        } else {
                ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Invalid login credentials!
                            </div>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Please enter both login and password!
                        </div>
                <?php
                    }
                }
                ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user text-muted me-2"></i>Login
                        </label>
                        <input type="text"
                            class="form-control"
                            name="login"
                            placeholder="Enter your username"
                            value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock text-muted me-2"></i>Password
                        </label>
                        <input type="password"
                            class="form-control"
                            name="password"
                            placeholder="Enter your password">
                    </div>

                    <div class="d-grid">
                        <button type="submit"
                            class="btn btn-primary"
                            name="connection">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Don't have an account?
                        <a href="index.php" class="text-primary text-decoration-none">Register</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>