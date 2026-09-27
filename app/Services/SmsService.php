<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SmsService
{
    /**
     * Send or simulate an SMS notification.
     *
     * @return array{success: bool, to: string, message: string, sent_at: string}
     */
    public function send(string $to, string $message): array
    {
        // Format destination phone
        $cleanedPhone = preg_replace('/[^\d+]/', '', $to) ?: $to;

        // Log the SMS for tracking and verification
        Log::info('[SMS DISPATCH] To: '.$cleanedPhone.' | Body: '.$message);

        // Twilio or gateway integration hook (falls back cleanly to mock simulation if credentials not set)
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        if ($sid && $token && $from && class_exists('\Twilio\Rest\Client')) {
            try {
                // @phpstan-ignore-next-line
                $client = new Client($sid, $token);
                $client->messages->create($cleanedPhone, [
                    'from' => $from,
                    'body' => $message,
                ]);
            } catch (\Throwable $e) {
                Log::warning('[SMS ERROR] Failed to send via Twilio: '.$e->getMessage());
            }
        }

        return [
            'success' => true,
            'to' => $cleanedPhone,
            'message' => $message,
            'sent_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    /**
     * Send appointment booking SMS to patient.
     *
     * @return array{success: bool, to: string, message: string, sent_at: string}|null
     */
    public function sendBookingConfirmation(Appointment $appointment): ?array
    {
        $patient = $appointment->patient;
        if (! $patient || ! $patient->phone) {
            return null;
        }

        $date = $appointment->appointment_date->format('d M Y');
        $time = Carbon::parse($appointment->appointment_time)->format('h:i A');
        $doc = $appointment->doctor->user->name ?? 'Doctor';

        $msg = "MediCare Clinic: Appointment #{$appointment->appointment_number} confirmed with Dr. {$doc} on {$date} at {$time}. Room {$appointment->doctor->room_number}. Please arrive 10m early.";

        return $this->send($patient->phone, $msg);
    }

    /**
     * Send appointment reminder SMS to patient.
     *
     * @return array{success: bool, to: string, message: string, sent_at: string}|null
     */
    public function sendReminder(Appointment $appointment): ?array
    {
        $patient = $appointment->patient;
        if (! $patient || ! $patient->phone) {
            return null;
        }

        $date = $appointment->appointment_date->format('d M Y');
        $time = Carbon::parse($appointment->appointment_time)->format('h:i A');
        $doc = $appointment->doctor->user->name ?? 'Doctor';

        $msg = "MediCare Reminder: You have an appointment tomorrow {$date} at {$time} with Dr. {$doc} (Room {$appointment->doctor->room_number}). Ref: {$appointment->appointment_number}.";

        return $this->send($patient->phone, $msg);
    }

    /**
     * Send appointment status change SMS to patient.
     *
     * @return array{success: bool, to: string, message: string, sent_at: string}|null
     */
    public function sendStatusUpdate(Appointment $appointment, string $status, ?string $reason = null): ?array
    {
        $patient = $appointment->patient;
        if (! $patient || ! $patient->phone) {
            return null;
        }

        $humanStatus = ucfirst($status);
        $reasonText = $reason ? " Reason: {$reason}." : '';
        $msg = "MediCare Clinic: Your appointment #{$appointment->appointment_number} has been updated to {$humanStatus}.{$reasonText}";

        return $this->send($patient->phone, $msg);
    }
}
