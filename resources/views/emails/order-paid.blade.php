@extends('emails.layout')

@section('content')
    <span class="badge badge-green">✅ Pembayaran Berhasil</span>

    <p style="font-size: 15px; color: #374151; line-height: 1.6;">
        Halo <strong>{{ $order->customer_name }}</strong>,
        <br><br>
        Pembayaran pesanan Anda telah kami terima. Berikut ringkasan pesanan Anda:
    </p>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">No. Pesanan</span>
            <span class="info-value">{{ $order->order_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Pesanan</span>
            <span class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Waktu Pembayaran</span>
            <span class="info-value">{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Metode Pembayaran</span>
            <span class="info-value">QRIS</span>
        </div>
        <hr class="divider">
        @foreach($order->items as $item)
        <div class="info-row">
            <span class="info-label">{{ $item->product_name }} ({{ $item->variant_name }})</span>
            <span class="info-value">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <hr class="divider">
        <div class="total-row">
            <span class="total-label">Total Dibayar</span>
            <span class="total-value">Rp {{ number_format($order->payment_fee ? $order->total_amount + $order->payment_fee : $order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <p style="font-size: 14px; color: #374151; line-height: 1.6; text-align: center;">
        Pesanan Anda sedang diproses. Kami akan mengirim email lagi saat pesanan selesai.
    </p>

    <div class="btn-wrap">
        <a href="{{ route('shop.order', $order->order_number) }}" class="btn">Lihat Pesanan</a>
    </div>
@endsection
