<!-- Vehicle Allocation -->
<div id="inlineVehicleForm" style="display: none;" class="bg-white border-bottom shadow-sm py-4">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Vehicle Allocation</h5>
        </div>

        <form wire:submit.prevent="allocateVehicle">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <select id="vehicleSelect" wire:model="form.asset_detail_id" class="form-select">
                        <option value="">Select Vehicle</option>
                        @foreach ($vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->asset_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <input type="text" id="brand" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Model</label>
                    <input type="text" id="model" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. of Seats</label>
                    <input type="text" id="seats" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Plate Number</label>
                    <input type="text" id="plate" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Color</label>
                    <input type="text" id="color" class="form-control" readonly>
                </div>

                <div class="col-12">
                    <hr>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Driver</label>
                    <select wire:model="form.driver_id" class="form-select">
                        <option value="">Select Driver</option>
                        @foreach ($drivers as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Odometer Start</label>
                    <input type="number" wire:model="form.odometer_start" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Odometer End</label>
                    <input type="number" wire:model="form.odometer_end" class="form-control">
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn" style="background-color: #172736; color: white;"
                    onclick="hideInlineVehicleForm()">Cancel</button>
                <button type="submit" class="btn" style="background-color: #172736; color: white;">Allocate</button>
            </div>
        </form>
    </div>
    
</div>
