<?php

namespace App\Http\Controllers;

use App\Models\Log\TransactionRent;
use App\Models\TransactionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function App\Helper\formatExcel_Idr;
use function App\Helper\styleExcel_Calibry;
use function App\Helper\styleExcel_Heading;
use function App\Helper\styleExcel_TableBorder;
use function App\Helper\styleExcel_TextMiddle;

class ReportController extends Controller
{
    function index()
    {
        return view('Pages.Report.index', [
            'title' =>  "Laporan",
            'pageTitle' =>  "Laporan"
        ]);
    }

    function downloadExcel(Request $request)
    {
        $styling = array();
        $merge = array();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Rekap Laporan");

        $transactions = TransactionHeader::with(['room', 'details'])->whereBetween('tanggal', [$request->s, $request->e])
            ->get();

        $rents = TransactionRent::with(['room'])->whereBetween('tgl', [$request->s, $request->e])
            ->orderBy('created_at')
            ->get();

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        $sheet->setCellValue("A1", "REKAP LAPORAN");
        $sheet->setCellValue("A2", (Carbon::parse($request->s)->isoFormat("DD MMMM YYYY") . '-' . Carbon::parse($request->e)->isoFormat("DD MMMM YYYY")));

        $merge[] = "A1:H1";
        $merge[] = "A2:H2";
        $styling[] = ['col' => "A1:A2", 'style' => styleExcel_Heading()];
        $styling[] = ['col' => "A1:A2", 'style' => styleExcel_TextMiddle()];
        $styling[] = ['col' => "A2:H2", 'style' => styleExcel_TableBorder('bottom')];

        $sheet->setCellValue("A4", "SEWA KAMAR");
        $merge[] = "A4:H4";
        $styling[] = ['col' => "A4", 'style' => styleExcel_Calibry("14pt", true)];

        $sheet->setCellValue("A5", "NO.");
        $merge[] = "A5:A6";
        $sheet->setCellValue("B5", "No. Kamar");
        $merge[] = "B5:B6";
        $sheet->setCellValue("C5", "Kategori");
        $merge[] = "C5:C6";
        $sheet->setCellValue("D5", "Jenis Transaksi");
        $merge[] = "D5:D6";
        $sheet->setCellValue("E5", "Tanggal Pemesanan");
        $merge[] = "E5:E6";
        $sheet->setCellValue("F5", "Status");
        $merge[] = "F5:F6";
        $sheet->setCellValue("G5", "Jumlah");
        $merge[] = "G5:H5";
        $sheet->setCellValue("G6", "Pemasukan");
        $sheet->setCellValue("H6", "Pengeluaran");

        $styling[] = ['col' => "A5:H6", 'style' => styleExcel_Calibry("14pt", true)];
        $styling[] = ['col' => "A5:H6", 'style' => styleExcel_TextMiddle()];
        $styling[] = ['col' => "A5:H6", 'style' => styleExcel_TableBorder()];

        $row = 7;
        $startRowRent = $row;
        $no = 1;

        if ($rents) {
            foreach ($rents as $key => $value) {
                $sheet->setCellValue("A$row", $no);
                $sheet->setCellValue("B$row", ($value->room ? $value->room->number_room : "-"));
                $sheet->setCellValue("C$row", ($value->room ? $value->room->category->name : "-"));
                if ($value->is_check_in) {
                    $sheet->setCellValue("D$row", "Sewa Kamar");
                    $sheet->setCellValue("F$row", "Check In");
                    $sheet->setCellValue("G$row", $value->jumlah);
                    $sheet->setCellValue("H$row", 0);
                }

                if ($value->is_deposit) {
                    $sheet->setCellValue("D$row", "Sewa Kamar");
                    $sheet->setCellValue("F$row", "Deposit");
                    $sheet->setCellValue("G$row", $value->jumlah);
                    $sheet->setCellValue("H$row", 0);
                }

                if ($value->is_check_out) {
                    $sheet->setCellValue("D$row", "Sewa Kamar");
                    $sheet->setCellValue("F$row", "Check Out");
                    $spreadsheet->getActiveSheet()->setCellValueExplicit("F$row", "Check Out\nNo Rekening : " . $value->rekening, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue("G$row", 0);
                    $sheet->setCellValue("H$row", $value->jumlah);
                }

                if ($value->is_upgrade) {
                    $sheet->setCellValue("D$row", "Sewa Kamar");
                    $sheet->setCellValue("F$row", "Upgrade Kamar");
                    $sheet->setCellValue("G$row", $value->jumlah);
                    $sheet->setCellValue("H$row", 0);
                }

                if ($value->is_downgrade) {
                    $sheet->setCellValue("D$row", "Sewa Kamar");
                    $sheet->setCellValue("F$row", "Downgrade Kamar");
                    $sheet->setCellValue("G$row", 0);
                    $sheet->setCellValue("H$row", 0);
                }

                $sheet->setCellValue("E$row", Carbon::parse($value->tgl)->isoFormat("DD-MM-YYYY"));

                $no++;
                $row++;
            }
        }

        $merge[] = "A$row:F$row";
        $sheet->setCellValue("A$row", "Total");
        $sheet->setCellValue("G$row", "=SUM(G$startRowRent:G" . ($row - 1) . ")");
        $sheet->setCellValue("H$row", "=SUM(H$startRowRent:H" . ($row - 1) . ")");

        $sheet->getStyle("G$startRowRent:H$row")->getNumberFormat()->setFormatCode(formatExcel_Idr());
        $styling[] = ['col' => "A$startRowRent:H$row", 'style' => styleExcel_TableBorder()];

        $row += 2;
        $sheet->setCellValue("A$row", "Layanan Lain");
        $merge[] = "A$row:H$row";
        $styling[] = ['col' => "A$row", 'style' => styleExcel_Calibry("14pt", true)];

        $row++;

        $sheet->setCellValue("A$row", "No.");
        $merge[] = "A$row:A" . ($row + 1);
        $sheet->setCellValue("B$row", "No. Kamar");
        $merge[] = "B$row:B" . ($row + 1);
        $sheet->setCellValue("C$row", "Tanggal Pemesanan");
        $merge[] = "C$row:C" . ($row + 1);
        $sheet->setCellValue("D$row", "Tipe Pembayaran");
        $merge[] = "D$row:D" . ($row + 1);
        $sheet->setCellValue("E$row", "Jenis Layanan");
        $merge[] = "E$row:E" . ($row + 1);
        $sheet->setCellValue("F$row", "Status");
        $merge[] = "F$row:F" . ($row + 1);
        $sheet->setCellValue("G$row", "Jumlah");
        $merge[] = "G$row:I$row";
        $row++;
        $sheet->setCellValue("G$row", "Outstanding");
        $sheet->setCellValue("H$row", "Pemasukan");
        $sheet->setCellValue("I$row", "Pengeluaran");

        $styling[] = ['col' => "A" . ($row - 1) . ":I$row", 'style' => styleExcel_Calibry("14pt", true)];
        $styling[] = ['col' => "A" . ($row - 1) . ":I$row", 'style' => styleExcel_TextMiddle()];
        $styling[] = ['col' => "A" . ($row - 1) . ":I$row", 'style' => styleExcel_TableBorder()];

        $row++;
        $startRowTransaction = $row;
        $no = 1;

        if ($transactions) {
            foreach ($transactions as $key => $value) {
                $sheet->setCellValue("A$row", $no);
                $sheet->setCellValue("B$row", ($value->room ? $value->room->number_room : "-"));
                $sheet->setCellValue("C$row", Carbon::parse($value->tgl)->isoFormat("DD-MM-YYYY"));
                $sheet->setCellValue("D$row", $value->tipe_pembayaran);
                if ($value->is_laundry) {
                    $sheet->setCellValue("E$row", "Laundry");
                }

                if ($value->is_order) {
                    $sheet->setCellValue("E$row", "Food");
                }

                if ($value->is_cleaning) {
                    $sheet->setCellValue("E$row", "Pembersihan");
                }

                if ($value->is_receipt) {
                    $sheet->setCellValue("E$row", "Pembelian Barang");
                }

                if ($value->is_topup) {
                    $sheet->setCellValue("E$row", "Top Up");
                }

                if ($value->pembayaran > 0) {
                    $sheet->setCellValue("F$row", "LUNAS");
                    $sheet->setCellValue("G$row", 0);
                    $sheet->setCellValue("H$row", $value->total);
                    $sheet->setCellValue("I$row", 0);
                } else {
                    $sheet->setCellValue("F$row", "BELUM LUNAS");
                    $sheet->setCellValue("G$row", $value->total);
                    $sheet->setCellValue("H$row", 0);
                    $sheet->setCellValue("I$row", 0);
                }


                if ($value->is_receipt) {
                    $sheet->setCellValue("F$row", "SELESAI");
                    $sheet->setCellValue("G$row", 0);
                    $sheet->setCellValue("H$row", 0);
                    $sheet->setCellValue("I$row", $value->total);
                }

                $no++;
                $row++;
            }
        }

        $merge[] = "A$row:F$row";
        $sheet->setCellValue("A$row", "Total");
        $sheet->setCellValue("G$row", "=SUM(G$startRowTransaction:G" . ($row - 1) . ")");
        $sheet->setCellValue("H$row", "=SUM(H$startRowTransaction:H" . ($row - 1) . ")");
        $sheet->setCellValue("I$row", "=SUM(I$startRowTransaction:I" . ($row - 1) . ")");

        $sheet->getStyle("G$startRowTransaction:I$row")->getNumberFormat()->setFormatCode(formatExcel_Idr());
        $styling[] = ['col' => "A$startRowTransaction:I$row", 'style' => styleExcel_TableBorder()];

        if ($merge) {
            foreach ($merge as $row) {
                $spreadsheet->getActiveSheet()->mergeCells($row);
            }
        }

        if ($styling) {
            foreach ($styling as $row) {
                $sheet->getStyle($row['col'])->applyFromArray($row['style']);
            }
        }

        $writer = new Xlsx($spreadsheet);

        // Tulis ke memory stream
        $handle = fopen('php://temp', 'r+');
        $writer->save($handle);
        rewind($handle);

        // Ambil konten stream
        $excelOutput = stream_get_contents($handle);
        fclose($handle);

        // Kirim response sebagai blob
        return Response::make($excelOutput, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="Rekap Laporan ' . Carbon::parse($request->s)->isoFormat("DDMMYYYY") . ' - ' . Carbon::parse($request->e)->isoFormat("DDMMYYYY") . '.xlsx"',
            'Content-Length' => strlen($excelOutput),
        ]);
    }
}
