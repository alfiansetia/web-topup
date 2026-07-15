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
                <td class="info-label">Waktu Selesai</td>
                <td class="info-value">
                    {{ $order->completed_at ? $order->completed_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}
                </td>
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
                    <td class="total-label">Total</td>
                    <td class="total-value">Rp {{ number_format($order->total_amount + $order->payment_fee, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        @else
            <table class="total-row" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="total-label">Total</td>
                    <td class="total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        @endif
    </div>

    @php
        $hasAccounts = $order->items->contains(function ($item) {
            return $item->assignedItems && $item->assignedItems->count() > 0;
        });
    @endphp

    @if ($hasAccounts)
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <p style="font-size: 15px; font-weight: 700; color: #1e40af; margin: 0 0 14px 0;">&#128274; Akun Anda</p>
            @foreach ($order->items as $item)
                @if ($item->assignedItems && $item->assignedItems->count() > 0)
                    <div style="margin-bottom: 14px;">
                        <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 8px 0;">
                            {{ $item->product_name }} ({{ $item->variant_name }})
                            {!! $item->quantity > 1 ? ' &times;' . $item->quantity : '' !!}
                        </p>
                        @foreach ($item->assignedItems as $ai)
                            <div
                                style="background: #ffffff; border: 1px solid #dbeafe; border-radius: 8px; padding: 12px; margin-bottom: 6px;">
                                <pre
                                    style="margin: 0; font-size: 13px; color: #1f2937; font-family: 'Courier New', monospace; white-space: pre-wrap; word-break: break-all;">{{ $ai->content }}</pre>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    @php
        $instructions = $order->items
            ->filter(function ($item) {
                return $item->product && $item->product->instruction_use;
            })
            ->unique('product_id');
    @endphp

    @if ($instructions->count() > 0)
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin-top: 16px;">
            <p style="font-size: 14px; font-weight: 600; color: #166534; margin: 0 0 10px 0;">&#128203; Cara Penggunaan</p>
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
