<?php

namespace App\Http\Controllers;

use App\Models\FoodSnack;
use App\Models\Stock;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use function App\Helper\formatExcel_Idr;
use function App\Helper\generateNoTrans;
use function App\Helper\styleExcel_Calibry;
use function App\Helper\styleExcel_TableBorder;
use function App\Helper\styleExcel_TextMiddle;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $stocks = Stock::with(['foodSnack'])->get();
        return view('Pages.Receipt.index', [
            'title' =>  "Barang Masuk",
            'pageTitle' =>  "Barang Masuk",
            // 'stocks'    =>  $stocks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $foodSnacks = FoodSnack::getData()->get();
        $transaction = TransactionHeader::where('nobukti', $request->nobukti)->first();

        return view('Pages.Receipt.create', [
            'title' =>  "Barang Masuk",
            'pageTitle' =>  "Barang Masuk",
            'foodSnacks'    =>  $foodSnacks,
            'transaction'    =>  $transaction,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nobukti = $request->nobukti;

        $mode = 'update';

        $validator = Validator::make(['kodebrg' => $request->kodebrg], [
            'kodebrg'   =>  'required',
        ], [
            'kodebrg.required'  =>  'Silahkan pilih barang yang ada disebelah kanan'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                ]
            ]);
        }
        if ($nobukti == '') {
            $nobukti = generateNoTrans('RC');
            $mode = "insert";
        }

        DB::beginTransaction();

        $header = [
            "nobukti"   =>  $nobukti,
            "tanggal"   =>  Carbon::parse($request->tanggal)->isoFormat("YYYY-MM-DD"),
            'user_id'   =>  auth()->user()->id,
        ];

        $detail = [
            "nobukti"   =>  $nobukti,
            "code_item" =>  $request->kodebrg,
            "type"  =>  "IN",
            "category"  =>  $request->kategori,
            "qty"   =>  $request->jumlah,
            "harga_beli"    =>  $request->hargaBeli,
        ];

        if ($mode == 'insert') {
            if (TransactionHeader::create($header)) {
                if (TransactionDetail::create($detail)) {
                    DB::commit();
                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  "Data berhasil disimpan",
                            'nobukti'   =>  $nobukti,
                        ]
                    ]);
                }

                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "Data gagal disimpan - Transaksi Detail",
                        'nobukti'   =>  $nobukti,
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Data gagal disimpan - Transaksi Header",
                    'nobukti'   =>  $nobukti,
                ]
            ]);
        }


        if (TransactionHeader::create($header)) {
            if (TransactionDetail::create($detail)) {
                DB::commit();
                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  "Data berhasil disimpan",
                        'nobukti'   =>  $nobukti,
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Data gagal disimpan - Transaksi Detail",
                    'nobukti'   =>  $nobukti,
                ]
            ]);
        }
    }

    /**
     * Posting a newly transaction resource in storage.
     */
    public function posting(Request $request)
    {
        $detailTransaction = TransactionDetail::where('nobukti', $request->nobukti)
            ->get();

        DB::beginTransaction();

        $totalUpdate = 0;
        if ($detailTransaction) {
            foreach ($detailTransaction as $key => $value) {
                $stock = Stock::where('code_item', $value->code_item)->first();

                if (Stock::where('id', $stock->id)->update([
                    'qty'   =>  $stock->qty + $value->qty,
                    'harga_beli'    =>  $value->harga_beli,
                ])) {
                    $totalUpdate++;
                }
            }
        }

        if ($totalUpdate > 0) {
            DB::commit();
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil diposting",
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal diposting",
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionHeader $receipt)
    {
        DB::beginTransaction();
        if (TransactionDetail::where('nobukti', $receipt->nobukti)->delete()) {
            if (TransactionHeader::find($receipt->id)->delete()) {
                DB::commit();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  "Data berhasil dihapus",
                    ]
                ]);
            }
            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Data gagal dihapus",
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal dihapus",
            ]
        ]);
    }

    public function deleteDetail(TransactionDetail $detail)
    {
        if (TransactionDetail::where('id', $detail->id)->delete()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil dihapus",
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal dihapus",
            ]
        ]);
    }

    function getDetailData(Request $request)
    {
        $dataHeader = TransactionHeader::where('nobukti', $request->nobukti)
            ->first();

        $dataDetails = collect(TransactionDetail::with(['foodSnack'])->where('nobukti', $request->nobukti)
            ->get())->chunk(10);

        $results = array();
        $no = 1;

        if ($dataDetails) {
            foreach ($dataDetails as $key => $chunk) {
                foreach ($chunk as $c => $value) {
                    $btnList = "#";

                    if ($dataHeader->status == 1) {
                        $btnList = '<div class="d-flex gap-2">
                                        <button class="btn btn-danger" title="Hapus Data" onclick="fnReceipt.onDeleteDetail(\'' . $value->id . '\',\'' . csrf_token() . '\')">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7h16" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /><path d="M10 12l4 4m0 -4l-4 4" /></svg>
                                        </button>
                                    </div>';
                    }

                    $results[] = [
                        $no,
                        $value->code_item,
                        $value->foodSnack->name,
                        $value->qty,
                        $value->harga_beli,
                        number_format($value->qty * $value->harga_beli),
                        $btnList,
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'data'  =>  $results,
        ]);
    }

    function generateReport(Request $request)
    {
        $items = FoodSnack::get();

        if ($request->item != 'undefined') {
            $items = FoodSnack::where('code_item', $request->item)->first();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report Inventory');

        $merge = [];
        $styling = [];

        $sheet->getColumnDimension('A')->setWidth(140, 'px');
        $sheet->getColumnDimension('B')->setWidth(200, 'px');
        $sheet->getColumnDimension('C')->setWidth(140, 'px');
        $sheet->getColumnDimension('D')->setWidth(80, 'px');
        $sheet->getColumnDimension('E')->setWidth(80, 'px');
        $sheet->getColumnDimension('F')->setWidth(80, 'px');
        $sheet->getColumnDimension('G')->setWidth(110, 'px');
        $sheet->getColumnDimension('H')->setWidth(110, 'px');
        $sheet->getColumnDimension('I')->setWidth(110, 'px');
        $sheet->getColumnDimension('J')->setWidth(110, 'px');

        $sheet->setCellValue("A1", "REPORT INVENTORY");
        $merge[] = "A1:I1";
        $styling[] = ['col' => "A1", 'style' => styleExcel_Calibry('20', true)];
        $sheet->setCellValue("A2", "TANGGAL");
        $sheet->setCellValue("B2", (": " . Carbon::parse($request->s)->isoFormat("DD-MM-YYYY") . " s/d " . Carbon::parse($request->e)->isoFormat("DD-MM-YYYY")));
        $styling[] = ['col' => "A2:B2", 'style' => styleExcel_Calibry('14', false)];
        $sheet->setCellValue("A3", "ITEM");

        // column
        $sheet->setCellValue("A5", "KODE BARANG");
        $merge[] = "A5:A6";
        $sheet->setCellValue("B5", "NAMA BARANG");
        $merge[] = "B5:B6";
        $sheet->setCellValue("C5", "TANGGAL");
        $merge[] = "C5:C6";
        $sheet->setCellValue("D5", "JUMLAH");
        $merge[] = "D5:E5";
        $sheet->setCellValue("F5", "HARGA SATUAN");
        $merge[] = "F5:G5";
        $sheet->setCellValue("H5", "SUB TOTAL");
        $merge[] = "H5:I5";
        $sheet->setCellValue("D6", "MASUK");
        $sheet->setCellValue("E6", "KELUAR");
        $sheet->setCellValue("F6", "BELI");
        $sheet->setCellValue("G6", "JUAL");
        $sheet->setCellValue("H6", "PENGELUARAN");
        $sheet->setCellValue("I6", "PEMASUKAN");
        $styling[] = ['col' => "A5:I6", 'style' => styleExcel_Calibry('12', true)];
        $styling[] = ['col' => "A5:I6", 'style' => styleExcel_TableBorder()];
        $styling[] = ['col' => "A5:I6", 'style' => styleExcel_TextMiddle()];
        if ($request->item == 'undefined') {
            $baris = 7;
            $awalBaris = $baris;
            $sheet->setCellValue("B3", ": ALL");
            $styling[] = ['col' => "A3:B3", 'style' => styleExcel_Calibry('14', false)];

            foreach ($items as $i => $item) {
                $detailItem = TransactionHeader::getDataReportDetail(Carbon::parse($request->s)->isoFormat('YYYY-MM-DD'), Carbon::parse($request->e)->isoFormat('YYYY-MM-DD'), $item->code_item)->get();

                $sheet->setCellValue("A$baris", $item->code_item);
                $sheet->setCellValue("B$baris", $item->name);

                foreach ($detailItem as $det => $detail) {
                    $sheet->getCell("C$baris")->setValueExplicit(Carbon::parse($detail->tanggal)->isoFormat("YYYY-MM-DD"), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_ISO_DATE);
                    $sheet->getStyle("C$baris")
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                    $sheet->setCellValue("D$baris", $detail->qty_in);
                    $sheet->setCellValue("E$baris", $detail->qty_out);
                    $sheet->setCellValue("F$baris", $detail->harga_beli);
                    $sheet->getStyle("F$baris")->getNumberFormat()->setFormatCode(formatExcel_Idr());
                    $sheet->setCellValue("G$baris", $detail->harga_jual);
                    $sheet->getStyle("G$baris")->getNumberFormat()->setFormatCode(formatExcel_Idr());
                    $sheet->setCellValue("H$baris", "=D$baris*F$baris");
                    $sheet->getStyle("H$baris")->getNumberFormat()->setFormatCode(formatExcel_Idr());
                    $sheet->setCellValue("I$baris", "=E$baris*G$baris");
                    $sheet->getStyle("I$baris")->getNumberFormat()->setFormatCode(formatExcel_Idr());

                    $baris++;
                }
                $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_TableBorder('right')];
                $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_TableBorder('left')];
            }
            $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_Calibry(10, false)];
            $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_TableBorder('top')];
            $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_TableBorder('bottom')];
        }

        if ($request->item != 'undefined') {
            $baris = 7;
            $awalBaris = $baris;
            $sheet->setCellValue("B3", ": " . $items->code_item . ' ' . $items->name);
            $styling[] = ['col' => "A3:B3", 'style' => styleExcel_Calibry('14', false)];

            $detailItem = TransactionHeader::getDataReportDetail(Carbon::parse($request->s)->isoFormat('YYYY-MM-DD'), Carbon::parse($request->e)->isoFormat('YYYY-MM-DD'), $items->code_item)->get();

            $sheet->setCellValue("A$baris", $items->code_item);
            $sheet->setCellValue("B$baris", $items->name);

            foreach ($detailItem as $det => $detail) {
                $sheet->getCell("C$baris")->setValueExplicit(Carbon::parse($detail->tanggal)->isoFormat("YYYY-MM-DD"), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_ISO_DATE);
                $sheet->getStyle("C$baris")
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                $sheet->setCellValue("D$baris", $detail->qty_in);
                $sheet->setCellValue("E$baris", $detail->qty_out);
                $sheet->setCellValue("F$baris", $detail->harga_beli);
                $sheet->getStyle("F$baris")->getNumberFormat()->setFormatCode(formatExcel_Idr());
                $sheet->setCellValue("G$baris", $detail->harga_jual);
                $sheet->getStyle("G$baris")->getNumberFormat()->setFormatCode(formatExcel_Idr());
                $sheet->setCellValue("H$baris", "=D$baris*F$baris");
                $sheet->getStyle("H$baris")->getNumberFormat()->setFormatCode(formatExcel_Idr());
                $sheet->setCellValue("I$baris", "=E$baris*G$baris");
                $sheet->getStyle("I$baris")->getNumberFormat()->setFormatCode(formatExcel_Idr());

                $baris++;
            }
            $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_TableBorder('right')];
            $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_TableBorder('left')];
        }

        $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_Calibry(10, false)];
        $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_TableBorder('top')];
        $styling[] = ['col' => "A$awalBaris:I" . ($baris - 1), 'style' => styleExcel_TableBorder('bottom')];

        // Todo merge
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

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Report Inventory ' . Carbon::parse($request->s)->isoFormat('YYYYMMDD') . ' - ' . Carbon::parse($request->e)->isoFormat('YYYYMMDD') . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output', 0);
        exit;
    }
}
