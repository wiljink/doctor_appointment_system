<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Show the checkout page for an appointment.
     */
    public function checkout(Appointment $appointment): View|RedirectResponse
    {
        // Authorization check
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isPatient() && $appointment->patient_id !== $user->id) {
                abort(403, 'Unauthorized access to this booking payment.');
            }
        }

        if ($appointment->payment_status === 'paid') {
            return redirect()->route('payment.receipt', $appointment)
                ->with('status', 'This appointment consultation fee has already been paid.');
        }

        $appointment->load(['doctor.user', 'department', 'patient']);

        return view('payment.checkout', compact('appointment'));
    }

    /**
     * Process simulated or live payment.
     */
    public function process(Request $request, Appointment $appointment): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isPatient() && $appointment->patient_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:card,digital_wallet,cash'],
            'card_holder' => ['nullable', 'string', 'max:100', 'required_if:payment_method,card'],
            'card_number' => ['nullable', 'string', 'max:25', 'required_if:payment_method,card'],
            'card_expiry' => ['nullable', 'string', 'max:7', 'required_if:payment_method,card'],
            'card_cvv' => ['nullable', 'string', 'min:3', 'max:4', 'required_if:payment_method,card'],
        ]);

        $dateCode = Carbon::now()->format('Ymd');
        $txnId = 'TXN-'.$dateCode.'-'.strtoupper(Str::random(6));

        if ($validated['payment_method'] === 'cash') {
            $appointment->update([
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'transaction_id' => 'PENDING-DESK-'.strtoupper(Str::random(5)),
            ]);

            return redirect()->route('booking.confirmation', $appointment)
                ->with('status', 'Selected pay at clinic desk. Please settle payment at the reception upon arrival.');
        }

        // Card or Digital Wallet (Simulated Stripe / Apple Pay / PayPal)
        $appointment->update([
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'] === 'digital_wallet' ? 'digital_wallet' : 'card',
            'transaction_id' => $txnId,
            'paid_at' => Carbon::now(),
        ]);

        return redirect()->route('payment.receipt', $appointment)
            ->with('status', 'Payment of $'.number_format((float) $appointment->fee, 2).' processed successfully!');
    }

    /**
     * Show official printable payment receipt.
     */
    public function receipt(Appointment $appointment): View
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isPatient() && $appointment->patient_id !== $user->id) {
                abort(403, 'Unauthorized access to this receipt.');
            }
        }

        $appointment->load(['doctor.user', 'department', 'patient']);

        return view('payment.receipt', compact('appointment'));
    }
}
