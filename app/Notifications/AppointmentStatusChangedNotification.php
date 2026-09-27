<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Appointment $appointment,
        public string $status,
        public ?string $reason = null
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
        $statusLabel = ucfirst($this->status);
        $doctorName = $this->appointment->doctor->user->name ?? 'Specialist';

        $mail = (new MailMessage)
            ->subject("Appointment Status Update: #{$this->appointment->appointment_number} is now {$statusLabel}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The status of your appointment #{$this->appointment->appointment_number} with Dr. {$doctorName} has been updated to: {$statusLabel}.");

        if ($this->reason) {
            $mail->line("**Reason / Details:** {$this->reason}");
        }

        if ($this->status === 'cancelled') {
            $mail->line('If this cancellation was in error or you wish to re-book for another date, please visit our online booking portal.');
        } elseif ($this->status === 'completed') {
            $mail->line('Your medical consultation notes and electronic prescription (Rx) are now available in your patient portal.');
        }

        return $mail->action('View My Appointments', route('patient.dashboard'))
            ->salutation('Best regards, MediCare Clinic Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabel = ucfirst($this->status);

        return [
            'appointment_id' => $this->appointment->id,
            'appointment_number' => $this->appointment->appointment_number,
            'title' => "Appointment {$statusLabel}",
            'message' => "Appointment #{$this->appointment->appointment_number} is now {$statusLabel}.".($this->reason ? " Reason: {$this->reason}" : ''),
            'url' => route('patient.appointments.show', $this->appointment),
            'type' => 'status_change',
        ];
    }
}
