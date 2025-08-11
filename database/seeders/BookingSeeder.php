<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\AssetType;
use App\Models\AssetDetail;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user, asset type, and asset detail
        $user = User::first();
        $assetType = AssetType::first();
        $assetDetail = AssetDetail::first();

        if (!$user || !$assetType || !$assetDetail) {
            $this->command->info('Please ensure you have at least one user, asset type, and asset detail before running this seeder.');
            return;
        }

        // Create sample bookings for the current month
        $currentDate = Carbon::now();
        $bookings = [
            [
                'asset_type_id' => $assetType->id,
                'asset_detail_id' => $assetDetail->id,
                'user_id' => $user->id,
                'purpose' => 'Team Meeting',
                'no_of_seats' => 10,
                'destination' => 'Conference Room A',
                'scheduled_date' => $currentDate->copy()->addDays(2),
                'time_from' => '09:00',
                'time_to' => '11:00',
                'notes' => 'Weekly team standup meeting',
                'status' => 'approved',
            ],
            [
                'asset_type_id' => $assetType->id,
                'asset_detail_id' => $assetDetail->id,
                'user_id' => $user->id,
                'purpose' => 'Client Presentation',
                'no_of_seats' => 15,
                'destination' => 'Main Hall',
                'scheduled_date' => $currentDate->copy()->addDays(5),
                'time_from' => '14:00',
                'time_to' => '16:00',
                'notes' => 'Important client presentation for Q4 project',
                'status' => 'pending',
            ],
            [
                'asset_type_id' => $assetType->id,
                'asset_detail_id' => $assetDetail->id,
                'user_id' => $user->id,
                'purpose' => 'Training Session',
                'no_of_seats' => 25,
                'destination' => 'Training Room B',
                'scheduled_date' => $currentDate->copy()->addDays(8),
                'time_from' => '10:00',
                'time_to' => '12:00',
                'notes' => 'New employee orientation training',
                'status' => 'approved',
            ],
            [
                'asset_type_id' => $assetType->id,
                'asset_detail_id' => $assetDetail->id,
                'user_id' => $user->id,
                'purpose' => 'Project Review',
                'no_of_seats' => 8,
                'destination' => 'Meeting Room C',
                'scheduled_date' => $currentDate->copy()->addDays(12),
                'time_from' => '13:00',
                'time_to' => '15:00',
                'notes' => 'Monthly project review and planning',
                'status' => 'rejected',
            ],
            [
                'asset_type_id' => $assetType->id,
                'asset_detail_id' => $assetDetail->id,
                'user_id' => $user->id,
                'purpose' => 'Workshop',
                'no_of_seats' => 20,
                'destination' => 'Workshop Room',
                'scheduled_date' => $currentDate->copy()->addDays(15),
                'time_from' => '09:30',
                'time_to' => '11:30',
                'notes' => 'Hands-on technical workshop',
                'status' => 'approved',
            ],
            [
                'asset_type_id' => $assetType->id,
                'asset_detail_id' => $assetDetail->id,
                'user_id' => $user->id,
                'purpose' => 'Board Meeting',
                'no_of_seats' => 12,
                'destination' => 'Boardroom',
                'scheduled_date' => $currentDate->copy()->addDays(18),
                'time_from' => '16:00',
                'time_to' => '18:00',
                'notes' => 'Quarterly board meeting',
                'status' => 'pending',
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }

        $this->command->info('Sample bookings created successfully!');
    }
}