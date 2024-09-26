<?php
session_start();
include("config/config.php");
isLoggin();

$act = @$_GET["act"];
if (!empty($act)) {
    if (!empty($act) && $act == "login") {
        $username = htmlspecialchars($_POST["username"]);
        $password = $_POST["password"];

        // Create a connection to the database
        $conn = new mysqli($config["host"], $config["user"], $config["password"], $config["dbname"]);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare a SQL statement with parameterized queries
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;

        if ($num_rows > 0) {
            // Login successful, store user data in session
            $dataLogin = $result->fetch_assoc();
            $_SESSION = $dataLogin;
            $_SESSION["flash"] = "toastrshow('success', 'Selamat Datang Kembali!', '')";
            redirect("index.php");
        } else {
            // Login failed, display error message
            $_SESSION["flash"] = "toastrshow('warning', 'Gagal!', 'Username / Password Salah!')";
            redirect("login.php");
        }

        // Close connection
        $conn->close();
    }
    redirect("index.php");
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Admin - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user" method="post" action="login.php?act=login">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username" placeholder="username" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        <hr>
                                    </form>
                                    <hr>
                                </div>
                            </div>
                            <div class="col-lg-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
