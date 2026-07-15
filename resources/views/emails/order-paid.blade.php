@extends('emails.layout')

@section('status')
    Pembayaran Berhasil
@endsection
@section('status-class')
    status-paid
@endsection

@section('content')
    <p style="font-size: 15px; color: #374151; line-height: 1.6;">
        Halo <strong>{{ $order->customer_name }}</strong>,
        <br><br>
        Pembayaran pesanan Anda telah kami terima. Berikut ringkasan pesanan Anda:
    </p>

    <div class="info-box">
        <table class="info-row" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="info-label">No. Pesanan</td>
                <td class="info-value">{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Pesanan</td>
                <td class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td class="info-label">Waktu Pembayaran</td>
                <td class="info-value">
                    {{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td class="info-label">Metode Pembayaran</td>
                <td class="info-value">QRIS</td>
            </tr>
        </table>
        <hr class="divider">
        @foreach ($order->items as $item)
            <table class="info-row" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="info-label">{{ $item->product_name }} ({{ $item->variant_name }}){!! $item->quantity > 1 ? ' &times;' . $item->quantity : '' !!}</td>
                    <td class="info-value">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            </table>
        @endforeach
        <hr class="divider">
        @if ($order->payment_fee && $order->payment_fee > 0)
            <table class="info-row" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="info-label">Harga Produk</td>
                    <td class="info-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Biaya Admin (QRIS)</td>
                    <td class="info-value">Rp {{ number_format($order->payment_fee, 0, ',', '.') }}</td>
                </tr>
            </table>
            <hr class="divider">
            <table class="total-row" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="total-label">Total Dibayar</td>
                    <td class="total-value">Rp {{ number_format($order->total_amount + $order->payment_fee, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        @else
            <table class="total-row" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="total-label">Total Dibayar</td>
                    <td class="total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        @endif
    </div>

    <p style="font-size: 14px; color: #374151; line-height: 1.6; text-align: center;">
        Pesanan Anda sedang diproses. Kami akan mengirim email lagi saat pesanan selesai.
    </p>

    <div class="btn-wrap">
        <a href="{{ route('shop.order', $order->order_number) }}" class="btn">Lihat Pesanan</a>
    </div>
@endsection
