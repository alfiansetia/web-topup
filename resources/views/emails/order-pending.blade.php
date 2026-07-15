@extends('emails.layout')

@section('status')
    Menunggu Pembayaran
@endsection
@section('status-class')
    status-pending
@endsection

@section('content')
    <p style="font-size: 15px; color: #374151; line-height: 1.6;">
        Halo <strong>{{ $order->customer_name }}</strong>,
        <br><br>
        Pesanan Anda berhasil dibuat. Silakan lakukan pembayaran sebelum <strong>1 jam</strong> agar pesanan tidak
        dibatalkan otomatis.
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
        </table>
        <hr class="divider">
        @foreach ($order->items as $item)
            <table class="info-row" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="info-label">{{ $item->product_name }}
                        ({{ $item->variant_name }})
                        {!! $item->quantity > 1 ? ' &times;' . $item->quantity : '' !!}</td>
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
                    <td class="total-label">Total Pembayaran</td>
                    <td class="total-value">Rp
                        {{ number_format($order->total_amount + $order->payment_fee, 0, ',', '.') }}</td>
                </tr>
            </table>
        @else
            <table class="total-row" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="total-label">Total Pembayaran</td>
                    <td class="total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        @endif
    </div>

    @if ($order->payment_ref)
        <div style="text-align: center; margin: 20px 0;">
            <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">
                Scan QRIS untuk membayar:
            </p>
            <div
                style="background: #ffffff; display: inline-block; padding: 16px; border-radius: 12px; border: 2px solid #e5e7eb;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($order->payment_ref) }}"
                    alt="QRIS Payment" width="220" height="220" style="display: block;" />
            </div>
            <p style="font-size: 12px; color: #9ca3af; margin: 10px 0 0 0;">
                Scan menggunakan GoPay, OVO, Dana, ShopeePay, atau e-wallet lainnya
            </p>
        </div>
    @endif

    <p style="font-size: 13px; color: #6b7280; text-align: center; margin: 0 0 8px;">
        Atau bayar melalui link di bawah:
    </p>

    <div class="btn-wrap">
        <a href="{{ route('shop.order', $order->order_number) }}" class="btn">Bayar Sekarang</a>
    </div>
@endsection
