<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth page.
     */
    public function redirect(Request $request): RedirectResponse
    {
        // Store intended URL before OAuth redirect (Referer is lost during OAuth flow)
        if ($request->header('referer') && !$request->session()->has('url.intended')) {
            $referer = $request->header('referer');
            $ignore = ['/login', '/register', '/forgot-password'];
            $path = parse_url($referer, PHP_URL_PATH);
            if (!in_array($path, $ignore)) {
                $request->session()->put('url.intended', $referer);
            }
        }

        return Socialite::driver('google')
            ->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $e) {
            return redirect()->route('login')
                ->with('error', 'Sesi login Google expired. Silakan coba lagi.');
        }

        // Check if user exists by google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // Check if user exists by email (linking existing account)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Link Google account to existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }
        } else {
            // Update avatar if changed
            if ($googleUser->getAvatar() !== $user->google_avatar) {
                $user->update([
                    'google_avatar' => $googleUser->getAvatar(),
                ]);
            }
        }

        // Check if user is blocked
        if ($user->is_blocked) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah diblokir. Hubungi admin untuk info lebih lanjut.');
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard.index', absolute: false));
    }
}
