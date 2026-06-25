<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CustomProductRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function print(Order $order)
    {
        $pdf = Pdf::loadView('invoices.print', compact('order'))->setPaper('a4', 'portrait');
        return $pdf->download('invoice-'.$order->payment_code.'.pdf');
    }

    public function printCustomDpInvoice(CustomProductRequest $customRequest)
    {
        // Pastikan DP sudah dibayar
        if (!$customRequest->isDpPaid()) {
            abort(404, 'Invoice tidak ditemukan');
        }

        $pdf = Pdf::loadView('invoices.custom-dp', compact('customRequest'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('dp-invoice-'.$customRequest->dp_payment_code.'.pdf');
    }

    public function printCustomFullInvoice(CustomProductRequest $customRequest)
    {
        // Pastikan pembayaran penuh sudah dilakukan
        if (!$customRequest->isFullyPaid()) {
            abort(404, 'Invoice tidak ditemukan');
        }

        $pdf = Pdf::loadView('invoices.custom-full', compact('customRequest'))
            ->setPaper('a3', 'portrait');

        return $pdf->download('full-invoice-'.$customRequest->full_payment_code.'.pdf');
    }
}
