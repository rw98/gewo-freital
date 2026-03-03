<?php

namespace App\Console\Commands;

use App\Enums\RequestAppointmentStatus;
use App\Models\RequestAppointment;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for appointments happening tomorrow';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tomorrow = now()->addDay()->startOfDay();
        $endOfTomorrow = now()->addDay()->endOfDay();

        $appointments = RequestAppointment::query()
            ->whereIn('status', [
                RequestAppointmentStatus::Pending,
                RequestAppointmentStatus::Confirmed,
            ])
            ->whereHas('timeslot', function ($query) use ($tomorrow, $endOfTomorrow) {
                $query->whereBetween('starts_at', [$tomorrow, $endOfTomorrow]);
            })
            ->with(['listingRequest', 'timeslot.listing'])
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            Notification::route('mail', $appointment->listingRequest->email)
                ->notify(new AppointmentReminderNotification($appointment));

            $count++;
        }

        $this->info("Sent {$count} appointment reminder(s).");

        return Command::SUCCESS;
    }
}
