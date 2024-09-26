<?php 
include("template/head.php");
checkSession();

// Initialize variables with checks for existence
$act = isset($_GET["act"]) ? $_GET["act"] : ''; 
$id = isset($_POST["id"]) ? $_POST["id"] : ''; 
$data = "";

if (!empty($act)) {
    if ($act == "delete") {
        if (!empty($id)) { // Check if id is not empty
            setDelete("surat_masuk", ["id" => $id]);
        }
    }
    redirect("surat_masuk.php");
    die;
}
?>

<body id="page-top">
    <div id="wrapper">
        <?php include("template/sidebar.php") ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("template/topbar.php") ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Surat Masuk</h1>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <a type="button" class="btn btn-primary btn-sm float-right" href="action_surat.php?method=tambah&surat=masuk">
                                <i class="fa fa-plus"></i> Tambah
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No.Surat</th>
                                            <th>Tanggal</th>
                                            <th>Kepada</th>
                                            <th>Dari</th>
                                            <th>Jenis Surat</th>
                                            <th>Perihal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch data from the database
                                        $data = getData("SELECT a.*, b.jenis AS jenis_surat FROM surat_masuk a LEFT JOIN jenis_surat b ON a.id_jenis = b.id");
                                        foreach ($data as $index => $item) : ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($item["no_surat"]) ?></td>
                                                <td><?= date_indo("d M Y", strtotime($item["tgl_surat"])) ?></td>
                                                <td><?= htmlspecialchars($item["kepada"]) ?></td>
                                                <td><?= htmlspecialchars($item["dari"]) ?></td>
                                                <td><?= htmlspecialchars($item["jenis_surat"]) ?></td>
                                                <td><?= htmlspecialchars($item["perihal"]) ?></td>
                                                <td>
                                                    <a target="_blank" href="preview_pdf.php?jenis=masuk&id=<?= $item["id"] ?>" class="btn btn-sm btn-success" title="Preview"><i class="fa fa-eye"></i></a>
                                                    <?php if (!empty($item["lampiran"])) : ?>
                                                        <a download href="upload/<?= $item["lampiran"] ?>" class="btn btn-sm btn-primary" title="Download Lampiran"><i class="fa fa-download"></i></a>
                                                    <?php endif ?>
                                                    <a href="action_surat.php?method=edit&surat=masuk&id=<?= $item["id"] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                                                    <a class="btn btn-sm btn-danger btn-delete" title="Hapus" data-index="<?= $index ?>" data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("template/footer.php") ?>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Surat</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="surat_masuk.php?act=delete">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Anda Yakin Akan Menghapus '<span class="no_surat"></span>' ?</label>
                                <input type="hidden" class="form-control" name="id">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include("template/script.php") ?>
</body>

<script>
    let data = <?= json_encode($data) ?>;
    $(".btn-delete").click(function() {
        let dataDetail = data[$(this).data("index")];
        $("#delete [name=id]").val(dataDetail.id);
        $("#delete .no_surat").html(dataDetail.no_surat);
    });
</script>

</html>
