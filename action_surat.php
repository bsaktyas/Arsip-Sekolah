<?php 
include("template/head.php");
checkSession();

// Initialize variables with checks for existence
$act = isset($_GET["act"]) ? $_GET["act"] : ''; 
$jenis = isset($_GET["jenis"]) ? $_GET["jenis"] : ''; 
$id = isset($_POST["id"]) ? $_POST["id"] : ''; 
$data = "";

// Handle the action if $act is not empty
if (!empty($act)) {
    unset($_POST["lapiran"]); // Ensure we don't send 'lapiran' in the POST data
    $post = $_POST; // Store POST data

    // Handle file upload
    if (!empty($_FILES["lampiran"]["name"])) {
        $upload = doUpload($_FILES["lampiran"]);
        if ($upload["error"]) {
            setFlash(false, "Gagal Upload Lampiran");
            redirect("action_surat.php?method=" . $act . "&surat=" . $jenis . "&id=" . $id);
            die;
        } else {
            $post["lampiran"] = $upload["filename"]; // Store the filename
        }
    }

    $hasil = "kosong"; // Initialize result variable
    if ($act == "tambah") {
        unset($post['id']); // Unset ID for new entries
        $hasil = setInsert("surat_" . $jenis, $post); // Insert into the database
    } else if ($act == "edit") {
        $hasil = setUpdate("surat_" . $jenis, $post, ["id" => $post["id"]]); // Update the record
    }

    // Check the result of the insert or update
    if ($hasil === "kosong") {
        redirect("index.php");
    } else {
        if ($hasil) {
            redirect("surat_" . $jenis . ".php");
        } else {
            setFlash(false, "Gagal " . $act . " Surat " . $jenis);
            redirect("action_surat.php?method=" . $act . "&surat=" . $jenis . "&id=" . $id);
        }
    }
    die;
}

// For editing, retrieve the data
if (isset($_GET["method"]) && $_GET["method"] == "edit") {
    $data = getData("SELECT * FROM surat_" . $_GET["surat"] . " WHERE id = " . intval($_GET["id"])); // Ensure id is an integer
    if (empty($data)) {
        redirect("index.php");
    } else {
        $data = $data[0]; // Get the first row
    }
}

// Retrieve surat types
$jenis_surat = getData("SELECT * FROM jenis_surat ORDER BY jenis ASC");
?>

<body id="page-top">
    <div id="wrapper">
        <?php include("template/sidebar.php"); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("template/topbar.php"); ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800" style="text-transform: capitalize;"><?= htmlspecialchars($_GET["method"]) ?> Surat <?= htmlspecialchars($_GET["surat"]) ?></h1>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form method="post" action="action_surat.php?act=<?= htmlspecialchars($_GET["method"]) ?>&jenis=<?= htmlspecialchars($_GET["surat"]) ?>" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label for="">No Surat</label>
                                            <input type="text" class="form-control" name="no_surat" placeholder="Masukan No Surat" required value="<?= !empty($data['no_surat']) ? htmlspecialchars($data['no_surat']) : '' ?>">
                                            <input type="hidden" class="form-control" name="id" value="<?= !empty($data['id']) ? htmlspecialchars($data['id']) : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Perihal</label>
                                            <input type="text" class="form-control" name="perihal" placeholder="Masukan perihal" required value="<?= !empty($data['perihal']) ? htmlspecialchars($data['perihal']) : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Lampiran</label>
                                            <input type="file" class="form-control" name="lampiran">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Kepada</label>
                                            <input type="text" class="form-control" name="kepada" placeholder="Masukan Kepada" required value="<?= !empty($data['kepada']) ? htmlspecialchars($data['kepada']) : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Dari</label>
                                            <input type="text" class="form-control" name="dari" placeholder="Masukan pengirim" required value="<?= !empty($data['dari']) ? htmlspecialchars($data['dari']) : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Jenis Surat</label>
                                            <select class="form-control select2" name="id_jenis" required>
                                                <option value="">Pilih Jenis</option>
                                                <?php foreach ($jenis_surat as $item) : ?>
                                                    <option value="<?= $item["id"] ?>" <?= !empty($data['id_jenis']) && $data['id_jenis'] == $item["id"] ? 'selected' : '' ?>><?= htmlspecialchars($item["jenis"]) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Tanggal Surat</label>
                                            <input type="date" class="form-control" name="tgl_surat" required value="<?= !empty($data['tgl_surat']) ? htmlspecialchars($data['tgl_surat']) : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Isi Surat</label>
                                            <textarea class="form-control" id="isi" name="isi_surat"><?= !empty($data['isi_surat']) ? htmlspecialchars($data['isi_surat']) : '' ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("template/footer.php"); ?>
        </div>
    </div>
    <?php include("template/script.php"); ?>
</body>
<script>
    let data = <?= json_encode($data) ?>;
    if (data != "") {
        $.each(data, function (index, item) { 
            if (index == "lampiran") return; // Skip lampiran if exists
            $("[name=" + index + "]").val(item).trigger("change");
            if (index == "tgl_surat") {
                $("[name=" + index + "]").val(item.substr(0, 10)).trigger("change");
            }
        });
    }
</script>
</html>
