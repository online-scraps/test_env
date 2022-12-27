<?php

namespace App\Exports;

use App\Models\ItemQuantityDetail;
use App\Models\StockEntries;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StockEntriesExcel implements FromView, ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //     return StockEntries::all();
    // }

    public function view(): View
    {
        $this->data['stocks'] = $this->reports();
        return view('pdfPages.excelexport', $this->data);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

    public function reports()
    {
        $stocks = DB::table('item_qty_detail as iqd')
            ->join('batch_qty_detail as bqd', 'bqd.item_id', '=', 'iqd.item_id')
            ->join('mst_items as mi', 'iqd.item_id', '=', 'mi.id')
            ->where('bqd.sup_org_id', backpack_user()->sup_org_id)
            ->where('iqd.sup_org_id', backpack_user()->sup_org_id)
            ->where('bqd.batch_qty', '>', 0)
            ->select('iqd.item_qty as item_qty', 'iqd.item_id', 'bqd.batch_no as batch_no', 'bqd.batch_qty as batch_qty', 'mi.name as item_name')
            ->get();


        return $stocks;
    }
}
