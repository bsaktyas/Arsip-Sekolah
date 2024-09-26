<?php
session_start();
include("config/config.php");
checkSession();

// Get user data from session
$user_data = $_SESSION;

// Perform actions based on the specified act
$act = @$_GET["act"] ?? '';

// Initialize post variable
$post = $_POST;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($act)) {
    // Remove photo from post data if not needed
    unset($post["photo"]);

        // Password handling
        if (empty($post["password"]) && empty($post["password2"])) {
            unset($post["password"], $post["password2"]);
        } else {
            if (isset($post["password"]) && isset($post["password2"]) && $post["password"] !== $post["password2"]) {
                redirect("index.php");
                die;
            }
            unset($post["password2"]);
            if (isset($post["password"])) { // Pastikan password ada sebelum di-hash
                $post["password"] = password_hash($post["password"], PASSWORD_DEFAULT);
            }
        }
    // File upload handling
    if (!empty($_FILES["photo"]["name"])) {
        $allowed_extensions = ['png', 'jpg', 'jpeg'];
        $filename = $_FILES['photo']['name'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_extensions)) {
            redirect("profil.php");
            die;
        }

        $upload = doUpload($_FILES["photo"]);
        if ($upload["error"]) {
            redirect("index.php");
            die;
        } else {
            $post["photo"] = $upload["filename"];
        }
    }

    // Perform action based on the 'act' parameter
    switch ($act) {
        case "update":
            setUpdate("admin", $post, ["id" => $user_data["id"]]);
            break;
            case "add":
                // Pastikan semua field yang diperlukan ada
                if (isset($post['nama'], $post['email'], $post['username'], $post['password'])) {
                    var_dump($post); // Debug info
                    $post["password"] = password_hash($post["password"], PASSWORD_DEFAULT); // Hash password
                    if (setInsert("admin", $post)) {
                        // Berhasil insert
                        redirect("index.php");
                    } else {
                        // Gagal insert
                        echo "Error: " . $conn->error; // Tampilkan error
                    }
                } else {
                    echo "Semua field harus diisi."; // Notifikasi jika ada field yang kosong
                }
                break;              
        case "delete":
            setDelete("admin", $_POST, ["id" => $_POST["id"]]);
            break;
    }
    redirect("index.php");
    die;
}

// Fetch admin data
$data = getData("SELECT * FROM admin WHERE id = '" . $conn->real_escape_string($user_data["id"]) . "'")[0];
if (empty($data)) {
    redirect("logout.php");
}

// Fetch all admin data for listing
$data2 = getData("SELECT * FROM admin");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin</title>
    <?php include("template/head.php"); ?>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include("template/sidebar.php") ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("template/topbar.php") ?>
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Profil Admin</h1>
                    </div>

                    <!-- Profil Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            Profil
                            <div class="float-right">
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#edit">Edit Profil</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Nama</strong></td>
                                    <td><?= htmlspecialchars($data["nama"]) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td><?= htmlspecialchars($data["email"]) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Foto</strong></td>
                                    <td><img src="<?= htmlspecialchars("upload/" . $data["photo"]) ?>" class="img-fluid" width="200px;"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Admin List -->
                    <div class="card mb-4">
                        <div class="card-header">Daftar Admin</div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Foto</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data2 as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($item["nama"]) ?></td>
                                            <td><?= htmlspecialchars($item["email"]) ?></td>
                                            <td><?= htmlspecialchars($item["username"]) ?></td>
                                            <td><img src="<?= htmlspecialchars("upload/" . $item["photo"]) ?>" class="img-fluid" width="100px"></td>
                                            <td>
                                                <form action="profil.php?act=delete" method="post">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($item["id"]) ?>">
                                                    <button class="btn btn-danger" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <?php include("template/footer.php") ?>
        </div>
    </div>

    <!-- Modal Edit Profil -->
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="profil.php?act=update" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Profil</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data["nama"]) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data["email"]) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data["username"]) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password2">Konfirmasi Password</label>
                            <input type="password" name="password2" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="photo">Foto</label>
                            <input type="file" name="photo" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <!-- Modal Edit Profil -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="profil.php?act=update" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Profil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data["nama"]) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data["email"]) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data["username"]) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password2">Re-enter Password</label>
                        <input type="password" name="password2" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="photo">Foto</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



</body>
</html>
