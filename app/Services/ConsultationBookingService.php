<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ConsultationSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ConsultationBookingService
{
    public function book(int $studentId, int $consultantId, int $slotId, string $date, int $type, int $paymentStatus = PAYMENT_STATUS_NOT_INIT, ?Appointment $existing = null, ?int $actorId = null): Appointment
    {
        $this->ensureMigration();
        $timezone = User::whereKey($consultantId)->value('consultation_timezone') ?: config('app.timezone', 'UTC');
        return DB::transaction(function () use ($studentId, $consultantId, $slotId, $date, $type, $paymentStatus, $existing, $timezone, $actorId) {
            $consultant = User::whereKey($consultantId)->lockForUpdate()->where('role', USER_ROLE_CONSULTANT)->where('status', STATUS_ACTIVE)->firstOrFail();
            $slot = $consultant->slots()->where('consultation_slots.id', $slotId)->where('consultation_slots.status', STATUS_ACTIVE)->first();
            if (!$slot) throw ValidationException::withMessages(['consultation_slot_id' => 'Choose an active time belonging to this consultant.']);
            $dateValue = Carbon::createFromFormat('!Y-m-d', $date, $timezone);
            if (!$dateValue || $dateValue->toDateString() !== $date || $dateValue->lt(Carbon::today($timezone)) || $dateValue->gt(Carbon::today($timezone)->addYear())) {
                throw ValidationException::withMessages(['date' => 'Choose a valid date within the next 12 months.']);
            }
            if (Carbon::parse($date.' '.$slot->start_time, $timezone)->lessThanOrEqualTo(now($timezone))) throw ValidationException::withMessages(['date' => 'That time has already passed in the consultant timezone.']);
            if ($existing) {
                $previousSlot = ConsultationSlot::findOrFail($existing->consultation_slot_id);
                $previousStart = Carbon::parse($existing->date->toDateString().' '.$previousSlot->start_time, $existing->consultation_timezone ?: $timezone);
                if ($previousStart->lessThan(now($timezone)->addHours(24))) throw ValidationException::withMessages(['appointment' => 'Online rescheduling is available until 24 hours before the appointment. Contact the team for later changes.']);
            }
            if (!in_array($type, [CONSULTATION_TYPE_PHYSICAL, CONSULTATION_TYPE_VIRTUAL], true)) throw ValidationException::withMessages(['consultation_type' => 'Choose an available consultation type.']);
            if (in_array(Carbon::parse($date, $timezone)->dayOfWeek + 1, array_map('intval', $consultant->day_off ?? []), true)) throw ValidationException::withMessages(['date' => 'The consultant is unavailable on this weekday.']);
            if (\App\Models\ConsultationBlackoutDate::where('consultant_id', $consultantId)->whereDate('date', $date)->exists()) throw ValidationException::withMessages(['date' => 'The consultant is unavailable on this date.']);

            $conflict = Appointment::where('consulter_id', $consultantId)->where('consultation_slot_id', $slotId)->whereDate('date', $date)
                ->where('status', '!=', STATUS_REJECT)->where('payment_status', '!=', PAYMENT_STATUS_NOT_INIT)
                ->when($existing, fn ($query) => $query->where('id', '!=', $existing->id))->exists();
            $pendingHold = Appointment::where('consulter_id', $consultantId)->where('consultation_slot_id', $slotId)->whereDate('date', $date)
                ->where('status', '!=', STATUS_REJECT)->where('payment_status', PAYMENT_STATUS_NOT_INIT)
                ->where('hold_expires_at', '>', now())->when($existing, fn ($query) => $query->where('id', '!=', $existing->id))->exists();
            if ($conflict || $pendingHold) throw ValidationException::withMessages(['consultation_slot_id' => 'That time was just booked. Please choose another available slot.']);

            $oldValues = $existing ? ['date' => $existing->date, 'consultation_slot_id' => $existing->consultation_slot_id] : null;
            $appointment = $existing ?: new Appointment();
            $appointment->fill([
                'user_id' => $studentId, 'consulter_id' => $consultantId, 'consultation_slot_id' => $slotId,
                'date' => $date, 'consultation_type' => $type, 'status' => STATUS_PENDING,
                'payment_status' => $paymentStatus, 'consultation_timezone' => $timezone,
                'created_by' => $existing?->created_by ?: ($actorId ?: $studentId),
                'hold_expires_at' => $paymentStatus === PAYMENT_STATUS_NOT_INIT ? now()->addMinutes(15) : null,
            ]);
            $appointment->save();
            if (\Illuminate\Support\Facades\Schema::hasTable('consultation_booking_events')) {
                DB::table('consultation_booking_events')->insert([
                    'appointment_id' => $appointment->id, 'actor_id' => $actorId ?: $studentId,
                    'event_type' => $existing ? 'rescheduled' : 'booked',
                    'old_values' => $oldValues ? json_encode($oldValues) : null,
                    'new_values' => json_encode(['date' => $date, 'consultation_slot_id' => $slotId]),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            return $appointment;
        });
    }

    public function checkAvailability(int $consultantId, int $slotId, string $date, int $type): void
    {
        $this->ensureMigration();
        $timezone = User::whereKey($consultantId)->value('consultation_timezone') ?: config('app.timezone', 'UTC');
        DB::transaction(function () use ($consultantId, $slotId, $date, $type, $timezone) {
            $consultant = User::whereKey($consultantId)->lockForUpdate()->where('role', USER_ROLE_CONSULTANT)->where('status', STATUS_ACTIVE)->firstOrFail();
            $slot = $consultant->slots()->where('consultation_slots.id', $slotId)->where('consultation_slots.status', STATUS_ACTIVE)->first();
            if (!$slot) throw ValidationException::withMessages(['consultation_slot_id' => 'Choose an active time belonging to this consultant.']);
            $dateValue = Carbon::createFromFormat('!Y-m-d', $date, $timezone);
            if (!$dateValue || $dateValue->toDateString() !== $date || $dateValue->lt(Carbon::today($timezone)) || $dateValue->gt(Carbon::today($timezone)->addYear()) || Carbon::parse($date.' '.$slot->start_time, $timezone)->lessThanOrEqualTo(now($timezone))) {
                throw ValidationException::withMessages(['date' => 'Choose a future date within the next 12 months.']);
            }
            if (!in_array($type, [CONSULTATION_TYPE_PHYSICAL, CONSULTATION_TYPE_VIRTUAL], true)) throw ValidationException::withMessages(['consultation_type' => 'Choose an available consultation type.']);
            if (in_array(Carbon::parse($date, $timezone)->dayOfWeek + 1, array_map('intval', $consultant->day_off ?? []), true)) throw ValidationException::withMessages(['date' => 'The consultant is unavailable on this weekday.']);
            if (\App\Models\ConsultationBlackoutDate::where('consultant_id', $consultantId)->whereDate('date', $date)->exists()) throw ValidationException::withMessages(['date' => 'The consultant is unavailable on this date.']);
            $busy = Appointment::where('consulter_id', $consultantId)->where('consultation_slot_id', $slotId)->whereDate('date', $date)
                ->where('status', '!=', STATUS_REJECT)
                ->where(fn ($q) => $q->whereIn('payment_status', [PAYMENT_STATUS_PENDING, PAYMENT_STATUS_PAID])->orWhere(fn ($h) => $h->where('payment_status', PAYMENT_STATUS_NOT_INIT)->where('hold_expires_at', '>', now())))->exists();
            if ($busy) throw ValidationException::withMessages(['consultation_slot_id' => 'That time is not available. Please choose another slot.']);
        });
    }

    public function cancel(Appointment $appointment, User $student, string $reason): void
    {
        $this->ensureMigration();
        DB::transaction(function () use ($appointment, $student, $reason) {
            $locked = Appointment::whereKey($appointment->id)->lockForUpdate()->firstOrFail();
            if ((int) $locked->user_id !== (int) $student->id || $locked->status === STATUS_REJECT) abort(404);
            $slot = ConsultationSlot::findOrFail($locked->consultation_slot_id);
            $zone = $locked->consultation_timezone ?: config('app.timezone', 'UTC');
            $start = Carbon::parse($locked->date->toDateString().' '.$slot->start_time, $zone);
            if ($start->lessThan(now($zone)->addHours(24))) throw ValidationException::withMessages(['appointment' => 'Online cancellation is available until 24 hours before the appointment. Contact the team for later changes.']);
            $locked->update(['status' => STATUS_REJECT, 'cancellation_reason' => $reason, 'hold_expires_at' => null]);
            DB::table('consultation_booking_events')->insert(['appointment_id' => $locked->id, 'actor_id' => $student->id, 'event_type' => 'cancelled', 'old_values' => json_encode(['status' => $appointment->status]), 'new_values' => json_encode(['status' => STATUS_REJECT]), 'reason' => $reason, 'created_at' => now(), 'updated_at' => now()]);
        });
    }

    private function ensureMigration(): void
    {
        abort_unless(Schema::hasColumn('users', 'consultation_timezone') && Schema::hasColumn('appointments', 'hold_expires_at'), 503, 'Consultation safety migration is not installed on this environment.');
    }
}
