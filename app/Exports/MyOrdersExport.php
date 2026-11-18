<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MyOrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    public function collection(): Enumerable
    {
        return Order::with(['book', 'author'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Order #',
            'Date',
            'Book',
            'Quantity',
            'Unit Price (TSh)',
            'Total (TSh)',
            'Delivery Address',
            'Seller',
            'Order Status',
            'Payment Status',
        ];
    }

    public function map($order): array
    {
        return [
            '#' . $order->id,
            $order->created_at->format('Y-m-d H:i'),
            $order->book->title ?? 'N/A',
            $order->quantity,
            number_format($order->price_per_unit, 2, '.', ''),
            number_format($order->total, 2, '.', ''),
            $order->shipping_address ?? '—',
            $order->owner_name ?? '—',
            ucfirst($order->status),
            ucfirst(str_replace('_', ' ', $order->payment_status ?? 'unpaid')),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:{$highestCol}{$highestRow}");

                $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EDE9FE'],
                    ],
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => '4C1D95'],
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center',
                    ],
                ]);

                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$highestCol}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'],
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}