<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use Carbon\Carbon;

class AppointmentSlotService
{
    /**
     * @return array{
     *     doctor_id: int,
     *     date: string,
     *     has_schedule: bool,
     *     on_leave: bool,
     *     message: string|null,
     *     slots: list<array{
     *         start_time: string,
     *         end_time: string,
     *         label: string,
     *         is_booked: bool,
     *         is_past: bool,
     *         is_available: bool
     *     }>
     * }
     */
    public function getAvailableSlots(Doctor $doctor, string $dateString): array
    {
        $date = Carbon::parse($dateString)->startOfDay();
        $today = Carbon::today();

        if ($date->lt($today)) {
            return [
                'doctor_id' => $doctor->id,
                'date' => $date->toDateString(),
                'has_schedule' => false,
                'on_leave' => false,
                'message' => 'Appointments cannot be booked for past dates.',
                'slots' => [],
            ];
        }

        if (! $doctor->is_available) {
            return [
                'doctor_id' => $doctor->id,
                'date' => $date->toDateString(),
                'has_schedule' => false,
                'on_leave' => false,
                'message' => 'This doctor is currently unavailable for consultations.',
                'slots' => [],
            ];
        }

        // Check if doctor has a registered leave on this date
        $onLeave = DoctorLeave::where('doctor_id', $doctor->id)
            ->whereDate('leave_date', $date->toDateString())
            ->exists();

        if ($onLeave) {
            return [
                'doctor_id' => $doctor->id,
                'date' => $date->toDateString(),
                'has_schedule' => false,
                'on_leave' => true,
                'message' => 'Doctor is on scheduled leave on this date.',
                'slots' => [],
            ];
        }

        // Find doctor's schedule for this day of week (0 = Sunday ... 6 = Saturday)
        $dayOfWeek = $date->dayOfWeek;
        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (! $schedule) {
            return [
                'doctor_id' => $doctor->id,
                'date' => $date->toDateString(),
                'has_schedule' => false,
                'on_leave' => false,
                'message' => 'Doctor is not available on '.$date->format('l').'s.',
                'slots' => [],
            ];
        }

        // Fetch existing booked appointments on this date
        $bookedTimes = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date->toDateString())
            ->whereNotIn('status', ['cancelled'])
            ->pluck('appointment_time')
            ->map(function ($time): string {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        $slots = [];
        $slotDuration = $schedule->slot_duration_minutes > 0 ? $schedule->slot_duration_minutes : 30;

        $startTime = Carbon::parse($date->toDateString().' '.$schedule->start_time);
        $endTime = Carbon::parse($date->toDateString().' '.$schedule->end_time);

        $breakStart = $schedule->break_start_time ? Carbon::parse($date->toDateString().' '.$schedule->break_start_time) : null;
        $breakEnd = $schedule->break_end_time ? Carbon::parse($date->toDateString().' '.$schedule->break_end_time) : null;

        $currentTimeCursor = $startTime->copy();
        $now = Carbon::now();

        while ($currentTimeCursor->copy()->addMinutes($slotDuration)->lte($endTime)) {
            $slotStart = $currentTimeCursor->copy();
            $slotEnd = $currentTimeCursor->copy()->addMinutes($slotDuration);

            // Check if slot falls in doctor's break time
            $isBreak = false;
            if ($breakStart && $breakEnd) {
                if ($slotStart->gte($breakStart) && $slotStart->lt($breakEnd)) {
                    $isBreak = true;
                }
            }

            if (! $isBreak) {
                $startFormatted = $slotStart->format('H:i');
                $endFormatted = $slotEnd->format('H:i');

                $isBooked = in_array($startFormatted, $bookedTimes, true);
                $isPast = $date->isToday() && $slotStart->lte($now);
                $isAvailable = ! $isBooked && ! $isPast;

                $slots[] = [
                    'start_time' => $startFormatted,
                    'end_time' => $endFormatted,
                    'label' => $slotStart->format('g:i A').' - '.$slotEnd->format('g:i A'),
                    'is_booked' => $isBooked,
                    'is_past' => $isPast,
                    'is_available' => $isAvailable,
                ];
            }

            $currentTimeCursor->addMinutes($slotDuration);
        }

        return [
            'doctor_id' => $doctor->id,
            'date' => $date->toDateString(),
            'has_schedule' => true,
            'on_leave' => false,
            'message' => empty($slots) ? 'No consultation slots available.' : null,
            'slots' => $slots,
        ];
    }
}
