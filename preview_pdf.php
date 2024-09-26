<?php
include("config/config.php");

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

checkSession();
require_once 'plugins/tcpdf/tcpdf.php';

// Sanitize and validate input
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0; // Ensure it's an integer
$jenis = isset($_GET["jenis"]) ? htmlspecialchars($_GET["jenis"]) : '';

if ($id <= 0 || empty($jenis)) {
    die("Invalid parameters.");
}

// Fetch data from the database
$data = getData("SELECT a.*, b.jenis AS jenis_surat FROM surat_$jenis a LEFT JOIN jenis_surat b ON a.id_jenis = b.id WHERE a.id = $id");

if (empty($data)) {
    die("No data found for the provided ID.");
}
$data = $data[0]; // Get the first row

// Start output buffering
ob_start();

// Create PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage("P", "A4");
$pdf->SetFont("helvetica", "", 12);

// Prepare HTML content
$html = '
    <table width="100%">
        <tr>
            <td align="right">Malang, ' . date_indo("d F Y", strtotime($data["tgl_surat"])) . '</td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table width="50%">
                    <tr>
                        <td width="20%">Nomor</td>
                        <td width="5%">:</td>
                        <td>' . htmlspecialchars($data["no_surat"]) . '</td>
                    </tr>
                    <tr>
                        <td width="20%">Lampiran</td>
                        <td width="5%">:</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td width="20%">Perihal</td>
                        <td width="5%">:</td>
                        <td>' . htmlspecialchars($data["perihal"]) . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>Kepada Yth.</td></tr>
        <tr><td>' . htmlspecialchars($data["kepada"]) . '</td></tr>
        <tr><td>Di tempat</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>' . nl2br(htmlspecialchars($data["isi_surat"])) . '</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td style="float:right">
                <table width="100%">
                    <tr>
                        <td width="55%"></td>
                        <td align="center">
                            Hormat Kami<br><br><br><br>
                            ' . htmlspecialchars($data["dari"]) . '
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
';

// Write HTML to PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('surat_' . $jenis . '_' . $id . '.pdf', 'I');

// End output buffering and flush output
ob_end_flush(); // Send the output buffer contents
