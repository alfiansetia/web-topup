@extends('emails.layout')

@section('status')
    Pesanan Selesai
@endsection
@section('status-class')
    status-completed
@endsection

@section('content')

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
            <span
                class="info-value">{{ $order->completed_at ? $order->completed_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</span>
        </div>
        <hr class="divider">
        @foreach ($order->items as $item)
            <div class="info-row">
                <span class="info-label">{{ $item->product_name }}
                    ({{ $item->variant_name }})
                    {{ $item->quantity > 1 ? ' &times;' . $item->quantity : '' }}</span>
                <span class="info-value">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach
        <hr class="divider">
        <div class="total-row">
            <span class="total-label">Total</span>
            <span class="total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    @php
        $instructions = $order->items
            ->filter(function ($item) {
                return $item->product && $item->product->instruction_use;
            })
            ->unique('product_id');
    @endphp

    @if ($instructions->count() > 0)
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin-top: 16px;">
            <p style="font-size: 14px; font-weight: 600; color: #166534; margin: 0 0 10px 0;">📋 Cara Penggunaan</p>
            @foreach ($instructions as $item)
                <div style="margin-bottom: 8px;">
                    <p style="font-size: 13px; font-weight: 600; color: #374151; margin: 0;">{{ $item->product_name }}</p>
                    <p style="font-size: 13px; color: #4b5563; margin: 2px 0 0 0;">{{ $item->product->instruction_use }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    <p style="font-size: 14px; color: #374151; line-height: 1.6; text-align: center; margin-top: 16px;">
        Terima kasih telah berbelanja di <strong>{{ config('app.name') }}</strong>! 🙏
        <br>
        Jika ada pertanyaan, jangan ragu untuk menghubungi kami.
    </p>

    <div class="btn-wrap">
        <a href="{{ route('shop.order', $order->order_number) }}" class="btn">Lihat Detail Pesanan</a>
    </div>
@endsection
