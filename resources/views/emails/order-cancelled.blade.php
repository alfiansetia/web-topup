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
        <table class="info-row" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="info-label">No. Pesanan</td>
                <td class="info-value">{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal</td>
                <td class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td class="info-label">Alasan</td>
                <td class="info-value">
                    {{ $order->payment_gateway_status === 'expired' ? 'Kedaluwarsa (lebih dari 1 jam)' : 'Dibatalkan oleh admin' }}
                </td>
            </tr>
            @if ($order->canceled_at)
                <tr>
                    <td class="info-label">Waktu Batal</td>
                    <td class="info-value">{{ $order->canceled_at->format('d M Y, H:i') }}</td>
                </tr>
            @endif
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
        <table class="total-row" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="total-label">Total</td>
                <td class="total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <p style="font-size: 14px; color: #374151; line-height: 1.6; text-align: center;">
        Jika Anda masih ingin membeli, silakan buat pesanan baru.
    </p>

    <div class="btn-wrap">
        <a href="{{ route('shop.home') }}" class="btn">Belanja Lagi</a>
    </div>
@endsection
