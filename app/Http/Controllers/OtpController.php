<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Otp;
use Twilio\Rest\Client;
use Carbon\Carbon;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:15',
        ]);

        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(10);

        Otp::updateOrCreate(
            ['phone_number' => $request->phone_number],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        $twilio = new  Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $twilio->messages->create($request->phone_number, [
            'from' => env('TWILIO_PHONE'),
            'body' => "Your OTP code is: $otp. It expires in 10 minutes."
        ]);

        return response()->json(['success' => true]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp' => 'required|numeric',
        ]);

        $otpRecord = Otp::where('phone_number', $request->phone_number)
                        ->where('otp', $request->otp)
                        ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        return redirect(route('dashboard'))->with('success', 'Registration successful!');
    }
}

