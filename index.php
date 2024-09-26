<?php 
include("template/head.php");
checkSession();

$dashboad = getData("SELECT 
    (SELECT COUNT(id) FROM surat_masuk) AS surat_masuk, 
    (SELECT COUNT(id) FROM surat_keluar) AS surat_keluar, 
    (SELECT COUNT(id) FROM jenis_surat) AS jenis_surat")[0];

$login = $_SESSION;
$act = @$_GET["act"] ?? ''; // Use null coalescing operator for safer handling

if (!empty($act)) {
    unset($_POST["photo"]); // Remove photo from post data if not needed
    $post = $_POST;

    // Password handling
    if (empty($post["password"]) && empty($post["password2"])) {
        unset($post["password"], $post["password2"]);
    } else {
        if ($post["password"] !== $post["password2"]) {
            redirect("index.php");
            die;
        }
        unset($post["password2"]);
        $post["password"] = password_hash($post["password"], PASSWORD_DEFAULT); // Use password_hash for better security
    }

    // File upload handling
    if (!empty($_FILES["photo"]["name"])) {
        $ekstensi_diperbolehkan = ['png', 'jpg', 'jpeg'];
        $nama = $_FILES['photo']['name'];
        $x = explode('.', $nama);
        $ekstensi = strtolower(end($x));

        if (!in_array($ekstensi, $ekstensi_diperbolehkan)) {
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

    // Perform actions based on the specified act
    switch ($act) {
        case "update":
            if (empty($post["nama"]) || empty($post["email"]) || empty($post["username"])) {
                redirect("index.php");
                die;
            }
            setUpdate("admin", $post, ["id" => $_SESSION["id"]]);
            break;
            case "add":
                var_dump($post); // Ini akan menunjukkan data yang diterima
                if (isset($post['nama'], $post['email'], $post['username'], $post['password'])) {
                    $post["password"] = password_hash($post["password"], PASSWORD_DEFAULT); // Hash password
                    if (setInsert("admin", $post)) {
                        redirect("index.php");
                    } else {
                        echo "Gagal menambahkan admin."; // Pesan kesalahan
                    }
                } else {
                    echo "Semua field harus diisi."; // Pesan jika ada field yang kosong
                }
                break;
             
            
        case "delete":
            if (empty($_POST["id"])) {
                redirect("index.php");
                die;
            }
            setDelete("admin", $_POST, ["id" => $_POST["id"]]);
            break;
    }
    redirect("index.php");
    die;
}

// Fetch admin data
$data = getData("SELECT * FROM admin WHERE id = '" . $conn->real_escape_string($login["id"]) . "'")[0];
if (empty($data)) {
    redirect("logout.php");
}

// Fetch all admin data for listing
$data2 = getData("SELECT * FROM admin");

?>

<body id="page-top">
    <div id="wrapper">
        <?php include("template/sidebar.php") ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("template/topbar.php") ?>
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Profil</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Cards for statistics -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Surat Masuk</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($dashboad["surat_masuk"]) ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-inbox fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Surat Keluar</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($dashboad["surat_keluar"]) ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Jenis Surat</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($dashboad["jenis_surat"]) ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Surat</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($dashboad["surat_masuk"] + $dashboad["surat_keluar"]) ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#add" style="margin-left: 10px;">
                                        <i class="fas fa-plus"></i> Add Admin
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info float-right" data-toggle="modal" data-target="#edit">
                                        <i class="fas fa-edit"></i> Edit Profil
                                    </button>

                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"><i class="fas fa-user-circle"></i> <strong>PROFIL Admin</strong></td>
                                            </tr>
                                            <tr>
                                                <td width="20%"><strong>Nama</strong></td>
                                                <td width="80%"> <?= htmlspecialchars($data["nama"]) ?> </td>
                                            </tr>
                                            <tr>
                                                <td width="20%"><strong>Email</strong></td>
                                                <td width="80%"> <?= htmlspecialchars($data["email"]) ?> </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Foto</strong></td>
                                                <td><img src="<?= htmlspecialchars("upload/" . $data["photo"]) ?>" class="img-fluid" width="200px;"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="col-sm-12">
                                        <table class="table dataTable">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>Username</th>
                                                    <th>Foto</th>
                                                    <th width="100px">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data2 as $index => $item) : ?>
                                                    <tr>
                                                        <td scope="row"><?= $index + 1 ?></td>
                                                        <td><?= htmlspecialchars($item["nama"]) ?></td>
                                                        <td><?= htmlspecialchars($item["email"]) ?></td>
                                                        <td><?= htmlspecialchars($item["username"]) ?></td>
                                                        <td><img src="<?= htmlspecialchars("upload/" . $item["photo"]) ?>" class="img-fluid" width="100px"></td>
                                                        <td>
                                                            <form action="profil.php?act=delete" method="post">
                                                                <input type="hidden" name="id" value="<?= htmlspecialchars($item["id"]) ?>">
                                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?')"><i class="fas fa-trash"></i></button>
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

<!-- Modal Add Admin -->
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="profil.php?act=add" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Admin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="photo">Foto</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
</body>
