<?php

namespace App\Notifications;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBookedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Appointment $appointment,
        public string $recipientRole = 'patient'
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
        $department = $this->appointment->department->name ?? 'General Medicine';
        $room = $this->appointment->doctor->room_number ?? '101';

        $mail = (new MailMessage)
            ->subject("Appointment Confirmed: #{$this->appointment->appointment_number} - MediCare Clinic")
            ->greeting("Hello {$notifiable->name},")
            ->line('Your clinic appointment has been successfully scheduled and confirmed.')
            ->line("**Appointment Ticket:** #{$this->appointment->appointment_number}")
            ->line("**Doctor:** Dr. {$doctorName} ({$department})")
            ->line("**Date & Time:** {$date} at {$time}")
            ->line("**Consultation Room:** {$room}")
            ->line('**Consultation Fee:** $'.number_format((float) $this->appointment->fee, 2).' ('.ucfirst($this->appointment->payment_status).')');

        if ($this->recipientRole === 'patient') {
            $mail->action('View Appointment Pass', route('patient.appointments.show', $this->appointment));
        } else {
            $mail->action('View Doctor Schedule', route('doctor.appointments'));
        }

        return $mail->line('Please arrive 10 minutes before your scheduled slot. If you need to reschedule or cancel, you can manage your appointment from your patient portal.')
            ->salutation('Best regards, MediCare Clinic Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $doctorName = $this->appointment->doctor->user->name ?? 'Specialist';
        $date = $this->appointment->appointment_date->format('M d, Y');
        $time = Carbon::parse($this->appointment->appointment_time)->format('h:i A');

        return [
            'appointment_id' => $this->appointment->id,
            'appointment_number' => $this->appointment->appointment_number,
            'title' => 'Appointment Booked',
            'message' => "Appointment #{$this->appointment->appointment_number} with Dr. {$doctorName} on {$date} at {$time} is confirmed.",
            'url' => route('patient.appointments.show', $this->appointment),
            'type' => 'appointment_booked',
        ];
    }
}
