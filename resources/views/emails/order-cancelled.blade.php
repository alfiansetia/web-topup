@extends('emails.layout')

@section('status')
    Pesanan Dibatalkan
@endsection
@section('status-class')
    status-cancelled
@endsection

@section('content')
    <p style="font-size: 15px; color: #374151; line-height: 1.6;">
        Halo <strong>{{ $order->customer_name }}</strong>,
        <br><br>
        @if ($order->payment_gateway_status === 'expired')
            Pesanan Anda telah <strong>dibatalkan otomatis</strong> karena tidak dibayar dalam waktu 1 jam.
        @else
            Pesanan Anda telah <strong>dibatalkan</strong>.
        @endif
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
        <div class="info-row">
            <span class="info-label">Alasan</span>
            <span
                class="info-value">{{ $order->payment_gateway_status === 'expired' ? 'Kedaluwarsa (lebih dari 1 jam)' : 'Dibatalkan oleh admin' }}</span>
        </div>
        <hr class="divider">
        @foreach ($order->items as $item)
            <div class="info-row">
                <span class="info-label">{{ $item->product_name }}
                    ({{ $item->variant_name }})
                    {!! $item->quantity > 1 ? ' &times;' . $item->quantity : '' !!}</span>
                <span class="info-value">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    <p style="font-size: 14px; color: #374151; line-height: 1.6; text-align: center;">
        Jika Anda masih ingin membeli, silakan buat pesanan baru.
    </p>

    <div class="btn-wrap">
        <a href="{{ route('shop.home') }}" class="btn">Belanja Lagi</a>
    </div>
@endsection
