<?php include("template/head.php");
checkSession();
?>

<body id="page-top">
    <div id="wrapper">
        <?php include("template/sidebar.php")  ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("template/topbar.php") ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Rekap Surat</h1>

                    <div class="card shadow mb-4">
                        <!-- <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
                        </div> -->
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
                                            <th>Keterangan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $data = getData("select a.* , b.jenis as jenis_surat from (select *, 'masuk' as surat  from surat_masuk union all select *, 'keluar' as surat  from surat_keluar) a left join jenis_surat b on a.id_jenis = b.id");
                                        foreach ($data as $index => $item) : ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= $item["no_surat"] ?></td>
                                                <td><?= date_indo("d M Y", strtotime($item["tgl_surat"])) ?></td>
                                                <td><?= $item["kepada"] ?></td>
                                                <td><?= $item["dari"] ?></td>
                                                <td><?= $item["jenis_surat"] ?></td>
                                                <td><?= $item["perihal"] ?></td>
                                                <td><?= $item["keterangan"] ?></td>
                                                <td>
                                                    <a href="preview_pdf.php?jenis=<?= $item['surat'] ?>&id=<?= $item["id"] ?>" class="btn btn-sm btn-success" title="Preview"><i class="fa fa-eye"></i></a>
                                                    <?php if (!empty($item['lampiran'])) : ?>
                                                        <a download href="upload/<?= $item["lampiran"] ?>" class="btn btn-sm btn-primary" title="Download Lampiran"><i class="fa fa-download"></i></a>
                                                    <?php endif; ?>
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
    </div>
    <?php include("template/script.php") ?>
</body>

</html>