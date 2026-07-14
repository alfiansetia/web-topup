@extends('emails.layout')

@section('content')
    <span class="badge badge-blue">🎉 Pesanan Selesai</span>

    <p style="font-size: 15px; color: #374151; line-height: 1.6;">
        Halo <strong>{{ $order->customer_name }}</strong>,
        <br><br>
        Pesanan Anda telah selesai diproses. Berikut ringkasan akhir pesanan:
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
            <span class="info-label">Waktu Selesai</span>
            <span class="info-value">{{ $order->completed_at ? $order->completed_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</span>
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
            <span class="total-label">Total</span>
            <span class="total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <p style="font-size: 14px; color: #374151; line-height: 1.6; text-align: center;">
        Terima kasih telah berbelanja di <strong>{{ config('app.name') }}</strong>! 🙏
        <br>
        Jika ada pertanyaan, jangan ragu untuk menghubungi kami.
    </p>

    <div class="btn-wrap">
        <a href="{{ route('shop.order', $order->order_number) }}" class="btn">Lihat Detail Pesanan</a>
    </div>
@endsection
