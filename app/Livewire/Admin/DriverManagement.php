<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Driver;

class DriverManagement extends Component
{
    use WithPagination;

    public $name, $is_active = true, $driverId;
    public $editMode = false;
    public $showForm = false;
    public $perPage = 10;

    protected $rules = [
        'name' => 'required|string|max:255',
        'is_active' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        Driver::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Driver added successfully.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        $this->driverId = $driver->id;
        $this->name = $driver->name;
        $this->is_active = $driver->is_active;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        Driver::where('id', $this->driverId)->update([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Driver updated successfully.');
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            Driver::findOrFail($id)->delete();
            $this->dispatch('notify', type: 'success', message: 'Driver deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->dispatch('notify', type: 'error', message: 'Cannot delete Driver because they have related bookings.');
        }
    }


    public function resetForm()
    {
        $this->reset(['name', 'is_active', 'driverId', 'editMode', 'showForm']);
    }

    public function render()
    {
        $drivers = Driver::paginate($this->perPage);

        return view('livewire.admin.driver-management', compact('drivers'));
    }
}
