<?php

namespace App\Notifications;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Appointment $appointment
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $date = $this->appointment->appointment_date->format('l, F j, Y');
        $time = Carbon::parse($this->appointment->appointment_time)->format('h:i A');
        $doctorName = $this->appointment->doctor->user->name ?? 'Specialist';
        $room = $this->appointment->doctor->room_number ?? '101';

        return (new MailMessage)
            ->subject("Reminder: Medical Appointment on {$date} at {$time} - MediCare")
            ->greeting("Hello {$notifiable->name},")
            ->line('This is a friendly reminder for your upcoming medical appointment.')
            ->line("**Ticket Number:** #{$this->appointment->appointment_number}")
            ->line("**Doctor:** Dr. {$doctorName}")
            ->line("**Date & Time:** {$date} at {$time}")
            ->line("**Room:** {$room}")
            ->action('View Appointment Details', route('patient.appointments.show', $this->appointment))
            ->line('Please arrive 10 minutes early. Contact us immediately if you need to reschedule.')
            ->salutation('Best regards, MediCare Clinic Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $doctorName = $this->appointment->doctor->user->name ?? 'Specialist';
        $time = Carbon::parse($this->appointment->appointment_time)->format('h:i A');

        return [
            'appointment_id' => $this->appointment->id,
            'appointment_number' => $this->appointment->appointment_number,
            'title' => 'Appointment Reminder',
            'message' => "Reminder: You have an upcoming consultation with Dr. {$doctorName} at {$time}.",
            'url' => route('patient.appointments.show', $this->appointment),
            'type' => 'reminder',
        ];
    }
}
