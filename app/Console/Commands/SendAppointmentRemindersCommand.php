<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clinic:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email and SMS reminders for upcoming appointments scheduled today or tomorrow';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService): int
    {
        $startDate = Carbon::today();
        $endDate = Carbon::tomorrow();

        $appointments = Appointment::with(['patient', 'doctor.user', 'department'])
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereDate('appointment_date', '>=', $startDate->toDateString())
            ->whereDate('appointment_date', '<=', $endDate->toDateString())
            ->whereNull('reminder_sent_at')
            ->get();

        $count = $appointments->count();
        $this->info("Found {$count} upcoming appointments eligible for reminders.");

        foreach ($appointments as $appointment) {
            $patient = $appointment->patient;
            if ($patient) {
                // Send email & in-app database notification
                $patient->notify(new AppointmentReminderNotification($appointment));

                // Send SMS notification
                $smsService->sendReminder($appointment);

                $appointment->update([
                    'reminder_sent_at' => Carbon::now(),
                ]);

                $this->line("Sent reminder for Appointment #{$appointment->appointment_number} to {$patient->email}");
            }
        }

        $this->info("Successfully dispatched {$count} appointment reminders.");

        return Command::SUCCESS;
    }
}
