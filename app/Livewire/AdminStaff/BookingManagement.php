<?php

namespace App\Livewire\AdminStaff;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;
use App\Models\AssetDetail;
use App\Models\Driver;
use App\Models\VehicleDriverAssignment;

class BookingManagement extends Component
{
    use WithPagination;

    public $selectedBooking = null;
    public $vehicles = [];
    public $drivers = [];

    public $form = [
        'asset_detail_id' => '',
        'brand' => '',
        'model' => '',
        'number_of_seats' => '',
        'plate_number' => '',
        'color' => '',
        'driver_id' => '',
        'odometer_start' => '',
        'odometer_end' => '',
    ];

    protected $listeners = ['viewBookingDetails'];

    public function mount()
    {
        $this->vehicles = AssetDetail::where('asset_type_id', 2)->get();
        $this->drivers = Driver::where('is_active', true)->get();
    }

    public function viewBookingDetails($bookingId)
    {
        $this->resetForm();

        $this->selectedBooking = Booking::with([
            'user.department',
            'user.branch',
            'assetType',
            'assetDetail',
            'vehicleAssignment.driver',
            'vehicleAssignment.assetDetail',
            'bookedGuests',
        ])->findOrFail($bookingId);

        $this->dispatch('open-details-modal');
    }

    public function updatedFormAssetDetailId($value)
    {
        $vehicle = AssetDetail::find($value);
        if ($vehicle) {
            $this->form['brand'] = $vehicle->brand;
            $this->form['model'] = $vehicle->model;
            $this->form['number_of_seats'] = $vehicle->number_of_seats;
            $this->form['plate_number'] = $vehicle->plate_number;
            $this->form['color'] = $vehicle->color;
        } else {
            $this->resetVehicleFields();
        }
    }

    public function allocateVehicle()
    {
        $this->validate([
            'form.asset_detail_id' => 'required|exists:asset_details,id',
            'form.driver_id' => 'required|exists:drivers,id',
            'form.odometer_start' => 'required|numeric',
            'form.odometer_end' => 'required|numeric',
        ]);

        try {
            if ($this->form['odometer_end'] < $this->form['odometer_start']) {
                $this->dispatch('allocation-status', ['status' => 'invalid-odometer']);
                return;
            }

            $existing = VehicleDriverAssignment::where('booking_id', $this->selectedBooking->id)->first();

            if ($existing) {
                // Update existing assignment
                $existing->update([
                    'asset_detail_id' => $this->form['asset_detail_id'],
                    'driver_id' => $this->form['driver_id'],
                    'assigned_by' => auth()->id(),
                    'assigned_date' => now(),
                    'odometer_start' => $this->form['odometer_start'],
                    'odometer_end' => $this->form['odometer_end'],
                ]);

                $this->dispatch('allocation-status', ['status' => 'updated']);
            } else {
                // Create new assignment
                VehicleDriverAssignment::create([
                    'booking_id' => $this->selectedBooking->id,
                    'asset_detail_id' => $this->form['asset_detail_id'],
                    'driver_id' => $this->form['driver_id'],
                    'assigned_by' => auth()->id(),
                    'assigned_date' => now(),
                    'odometer_start' => $this->form['odometer_start'],
                    'odometer_end' => $this->form['odometer_end'],
                ]);

                $this->dispatch('allocation-status', ['status' => 'success']);
            }

            $this->selectedBooking = $this->selectedBooking->fresh([
                'vehicleAssignment.driver',
                'vehicleAssignment.assetDetail',
            ]);

            $this->dispatch('close-allocate-modal');
            $this->resetForm();

        } catch (\Exception $ex) {
            \Log::error('🚨 Allocation error', [
                'message' => $ex->getMessage(),
                'booking_id' => $this->selectedBooking->id ?? null,
                'form' => $this->form,
            ]);

            $this->dispatch('allocation-status', ['status' => 'error']);
        }
    }



    private function resetVehicleFields()
    {
        $this->form['brand'] = '';
        $this->form['model'] = '';
        $this->form['number_of_seats'] = '';
        $this->form['plate_number'] = '';
        $this->form['color'] = '';
    }

    private function resetForm()
    {
        $this->form = [
            'asset_detail_id' => '',
            'brand' => '',
            'model' => '',
            'number_of_seats' => '',
            'plate_number' => '',
            'color' => '',
            'driver_id' => '',
            'odometer_start' => '',
            'odometer_end' => '',
        ];
    }

    public function render()
    {
        $bookings = Booking::with([
            'user.department',
            'user.branch',
            'assetType',
        ])
            ->where('asset_type_id', 2)
            ->where('status', 'approved')
            ->latest()
            ->paginate(10);

        return view('livewire.admin-staff.booking-management', [
            'bookings' => $bookings,
        ])->layout('layouts.adminstaff');
    }
}
