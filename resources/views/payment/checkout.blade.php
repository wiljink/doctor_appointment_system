@extends('layouts.app')

@section('title', 'Consultation Fee Checkout - MediCare Clinic')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    paymentMethod: 'card',
    cardHolder: '{{ Auth::user()->name ?? '' }}',
    cardNumber: '',
    cardExpiry: '',
    cardCvv: '',
    fillTestCard() {
        this.cardHolder = '{{ Auth::user()->name ?? 'Jane Doe' }}';
        this.cardNumber = '4242 •••• •••• 4242';
        this.cardExpiry = '12/28';
        this.cardCvv = '888';
    }
}">
    <!-- Progress Header -->
    <div class="mb-8">
        <nav class="flex items-center text-sm font-medium text-slate-500 mb-3 space-x-2">
            <a href="{{ route('patient.dashboard') }}" class="hover:text-primary-600">Portal</a>
            <span>/</span>
            <a href="{{ route('patient.appointments.show', $appointment) }}" class="hover:text-primary-600">Appointment #{{ $appointment->appointment_number }}</a>
            <span>/</span>
            <span class="text-slate-900 font-semibold">Payment Checkout</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Consultation Fee Checkout</h1>
                <p class="text-slate-500 mt-1">Complete your secure payment to finalize your consultation booking reservation.</p>
            </div>
            <div class="hidden sm:flex items-center space-x-1 text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full text-xs font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>256-Bit Encrypted</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Payment Method & Form -->
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('payment.process', $appointment) }}" method="POST" id="checkout-form" class="space-y-6">
                @csrf

                <!-- Payment Method Selector -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center">
                        <span class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold mr-2">1</span>
                        Choose Payment Method
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center transition-all text-center"
                               :class="paymentMethod === 'card' ? 'border-primary-600 bg-primary-50/40 text-primary-900 shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_method" value="card" class="sr-only" x-model="paymentMethod">
                            <svg class="w-7 h-7 mb-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="text-sm font-bold">Credit / Debit Card</span>
                            <span class="text-xs text-slate-500 mt-0.5">Stripe / Visa / MC</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center transition-all text-center"
                               :class="paymentMethod === 'digital_wallet' ? 'border-primary-600 bg-primary-50/40 text-primary-900 shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_method" value="digital_wallet" class="sr-only" x-model="paymentMethod">
                            <svg class="w-7 h-7 mb-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span class="text-sm font-bold">Digital Wallet</span>
                            <span class="text-xs text-slate-500 mt-0.5">Apple / Google Pay</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center transition-all text-center"
                               :class="paymentMethod === 'cash' ? 'border-primary-600 bg-primary-50/40 text-primary-900 shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_method" value="cash" class="sr-only" x-model="paymentMethod">
                            <svg class="w-7 h-7 mb-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-sm font-bold">Pay at Clinic</span>
                            <span class="text-xs text-slate-500 mt-0.5">Cash / Reception Desk</span>
                        </label>
                    </div>
                </div>

                <!-- Card Details Section -->
                <div x-show="paymentMethod === 'card'" x-transition class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900 flex items-center">
                            <span class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold mr-2">2</span>
                            Card Information
                        </h2>
                        <button type="button" @click="fillTestCard()" class="text-xs font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 px-2.5 py-1 rounded-md border border-primary-200 flex items-center">
                            ⚡ Autofill Demo Card
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Cardholder Name</label>
                        <input type="text" name="card_holder" x-model="cardHolder" placeholder="e.g. John Doe"
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Card Number</label>
                        <div class="relative">
                            <input type="text" name="card_number" x-model="cardNumber" placeholder="4242 •••• •••• 4242"
                                   class="w-full rounded-xl border-slate-200 text-sm font-mono focus:border-primary-500 focus:ring-primary-500 py-2.5 pl-3 pr-16">
                            <div class="absolute right-3 top-2.5 flex items-center space-x-1 text-slate-400">
                                <span class="text-xs font-bold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200">VISA</span>
                                <span class="text-xs font-bold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200">MC</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Expiration (MM/YY)</label>
                            <input type="text" name="card_expiry" x-model="cardExpiry" placeholder="12/28" maxlength="5"
                                   class="w-full rounded-xl border-slate-200 text-sm font-mono focus:border-primary-500 focus:ring-primary-500 py-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Security Code (CVC)</label>
                            <input type="password" name="card_cvv" x-model="cardCvv" placeholder="•••" maxlength="4"
                                   class="w-full rounded-xl border-slate-200 text-sm font-mono focus:border-primary-500 focus:ring-primary-500 py-2.5">
                        </div>
                    </div>
                </div>

                <!-- Digital Wallet Section -->
                <div x-show="paymentMethod === 'digital_wallet'" x-transition class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-4">
                    <div class="text-center py-4">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Instant One-Click Authorization</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Authenticate via your Apple ID, Google Account, or biometric wallet credentials.</p>
                    </div>
                </div>

                <!-- Cash Section -->
                <div x-show="paymentMethod === 'cash'" x-transition class="bg-amber-50/60 rounded-2xl border border-amber-200 p-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h4 class="text-sm font-bold text-amber-900">Pay at the Clinic Reception</h4>
                            <p class="text-xs text-amber-700 mt-1">Your appointment will remain confirmed and reserved. Please present your ticket number at the clinic reception counter at least 10 minutes prior to your slot to settle the fee in cash.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-primary-500/25 transition-all flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span x-text="paymentMethod === 'cash' ? 'Confirm Reservation & Pay at Counter' : 'Authorize & Pay ${{ number_format($appointment->fee, 2) }}'"></span>
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3 flex items-center justify-center space-x-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Transactions are encrypted with industry-standard TLS protocols</span>
                    </p>
                </div>
            </form>
        </div>

        <!-- Order Summary Column -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sticky top-24 space-y-6">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Appointment Summary</h3>

                <div class="flex items-center space-x-3.5">
                    <img src="{{ $appointment->doctor->user->avatar_url }}" alt="{{ $appointment->doctor->user->name }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 shadow-sm">
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Dr. {{ $appointment->doctor->user->name }}</h4>
                        <p class="text-xs text-primary-600 font-semibold">{{ $appointment->doctor->specialization }}</p>
                        <p class="text-xs text-slate-500">{{ $appointment->department->name }} • Room {{ $appointment->doctor->room_number }}</p>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 space-y-2.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Ticket Reference</span>
                        <span class="font-mono font-bold text-slate-900">#{{ $appointment->appointment_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Scheduled Date</span>
                        <span class="font-semibold text-slate-900">{{ $appointment->appointment_date->format('l, M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Time Slot</span>
                        <span class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Patient Name</span>
                        <span class="font-semibold text-slate-900">{{ $appointment->patient->name }}</span>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="border-t border-slate-100 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Doctor Consultation Fee</span>
                        <span class="font-medium text-slate-900">${{ number_format($appointment->fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Clinic Facility & Record Service</span>
                        <span class="font-medium text-emerald-600">Included ($0.00)</span>
                    </div>
                    <div class="border-t border-slate-200/80 pt-3 flex justify-between items-baseline">
                        <span class="text-base font-bold text-slate-900">Total Amount Due</span>
                        <span class="text-2xl font-black text-primary-600">${{ number_format($appointment->fee, 2) }}</span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('patient.appointments.show', $appointment) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 flex items-center justify-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Back to Appointment Pass</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
