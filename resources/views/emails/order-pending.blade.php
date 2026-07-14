@extends('emails.layout')

@section('content')
    <span class="badge badge-yellow">⏳ Menunggu Pembayaran</span>

    <p style="font-size: 15px; color: #374151; line-height: 1.6;">
        Halo <strong>{{ $order->customer_name }}</strong>,
        <br><br>
        Pesanan Anda berhasil dibuat. Silakan lakukan pembayaran sebelum <strong>1 jam</strong> agar pesanan tidak dibatalkan otomatis.
    </p>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">No. Pesanan</span>
            <span class="info-value">{{ $order->order_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
        </div>
        <hr class="divider">
        @foreach($order->items as $item)
        <div class="info-row">
            <span class="info-label">{{ $item->product_name }} ({{ $item->variant_name }})</span>
            <span class="info-value">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <hr class="divider">
        @if($order->payment_fee && $order->payment_fee > 0)
        <div class="info-row">
            <span class="info-label">Harga Produk</span>
            <span class="info-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Biaya Admin (QRIS)</span>
            <span class="info-value">Rp {{ number_format($order->payment_fee, 0, ',', '.') }}</span>
        </div>
        <hr class="divider">
        <div class="total-row">
            <span class="total-label">Total Pembayaran</span>
            <span class="total-value">Rp {{ number_format($order->total_amount + $order->payment_fee, 0, ',', '.') }}</span>
        </div>
        @else
        <div class="total-row">
            <span class="total-label">Total Pembayaran</span>
            <span class="total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <p style="font-size: 13px; color: #6b7280; text-align: center; margin: 0 0 8px;">
        Scan QRIS yang tersedia di halaman pesanan Anda:
    </p>

    <div class="btn-wrap">
        <a href="{{ route('shop.order', $order->order_number) }}" class="btn">Bayar Sekarang</a>
    </div>
@endsection
