<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class MfaSetupController extends Controller
{
    public function index()
    {
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $user = \Illuminate\Support\Facades\Auth::user();

        // 1. Generate a brand new secret key and save it to the session temporarily
        $secretKey = $google2fa->generateSecretKey();
        session()->put('mfa_setup_secret', $secretKey);

        // 2. Generate the QR Code data linking to their specific email
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'DALC SPED System',
            $user->email,
            $secretKey
        );

        // 3. Convert it to a viewable image
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(250),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCodeImage = $writer->writeString($qrCodeUrl);

        return view('auth.mfa-setup', compact('qrCodeImage', 'secretKey'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $google2fa = new Google2FA();
        $secretKey = session()->get('mfa_setup_secret');

        // Verify the 6-digit code matches the secret
        $isValid = $google2fa->verifyKey($secretKey, $request->one_time_password);

        if ($isValid) {
            // It worked! Save the secret to the database permanently
            $user = Auth::user();
            $user->update(['two_factor_secret' => $secretKey]);
            
            session()->forget('mfa_setup_secret');

            return redirect('/')->with('success', 'Security complete! Your account is now fully protected.');
        }

        return back()->withErrors(['one_time_password' => 'Invalid code. Please try again.']);
    }
}