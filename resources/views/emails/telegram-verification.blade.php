@extends('emails.layout')

@section('status')
    Kode Verifikasi Telegram
@endsection
@section('status-class')
    status-paid
@endsection

@section('content')
    <p style="font-size: 15px; color: #374151; line-height: 1.6;">
        Halo <strong>{{ $userName }}</strong>,
        <br><br>
        Berikut adalah kode verifikasi untuk menghubungkan akun Telegram kamu:
    </p>

    <div style="text-align: center; margin: 28px 0;">
        <div
            style="
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 8px;
            padding: 18px 36px;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
        ">
            {{ $code }}
        </div>
    </div>

    <p style="font-size: 14px; color: #6b7280; line-height: 1.6; text-align: center;">
        Kode ini berlaku selama <strong>10 menit</strong>.
        <br>
        Jangan bagikan kode ini kepada siapapun.
    </p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

    <p style="font-size: 13px; color: #9ca3af; line-height: 1.6; text-align: center;">
        Jika kamu tidak meminta kode ini, abaikan email ini.
    </p>
@endsection
