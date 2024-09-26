<?php include("template/head.php");
checkSession();
$act = @$_GET["act"];
if (!empty($act)) {
    if ($act == "add") {
        setInsert("jenis_surat", $_POST);
    } else if ($act == "update") {
        setUpdate("jenis_surat", $_POST, ["id" => $_POST["id"]]);
    } else if ($act == "delete") {
        setDelete("jenis_surat", $_POST, ["id" => $_POST["id"]]);
    }
    redirect("jenis_surat.php");
    die;
}
?>

<body id="page-top">
    <div id="wrapper">
        <?php include("template/sidebar.php")  ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("template/topbar.php") ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Jenis Surat</h1>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#tambah">
                                <i class="fa fa-plus"></i> Jenis
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis Surat</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $data = getData("select * from jenis_surat");
                                        foreach ($data as $index => $item) : ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= $item["jenis"] ?></td>
                                                <td>
                                                    <a class="btn btn-sm btn-delete btn-danger" data-index="<?= $index ?>" data-toggle="modal" data-target="#delete"><i class="fa fa-trash text-white"></i></a>
                                                    <a class="btn btn-sm btn-edit btn-warning" data-index="<?= $index ?>" data-toggle="modal" data-target="#edit"><i class="fa fa-edit text-white"></i></a>
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
        <div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jenis</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="jenis_surat.php?act=add">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Jenis</label>
                                <input type="text" class="form-control" name="jenis" placeholder="Masukan Jenis" required>
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
        <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Jenis</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="jenis_surat.php?act=update">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Jenis</label>
                                <input type="text" class="form-control" name="jenis" placeholder="Masukan Jenis" required>
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
        <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Jenis</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="jenis_surat.php?act=delete">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Anda Yakin Akan Menghapus '<span class="jenis"></span>' ?</label>
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
    $(document).ready(function() {
        $('table').dataTable();
    });

    $(".btn-edit").click(function() {
        dataDetail = data[$(this).data("index")];
        $.each(dataDetail, function(index, item) {
            $("#edit [name=" + index + "]").val(item).change()
        });
    })

    $(".btn-delete").click(function() {
        dataDetail = data[$(this).data("index")];
        $.each(dataDetail, function(index, item) {
            $("#delete [name=" + index + "]").val(item).change()
            $("#delete ." + index + "").html(item)
        });
    })
</script>

</html>