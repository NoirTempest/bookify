<!-- Make sure Alpine.js is loaded -->
<link rel="stylesheet" href="{{ asset('css/tabs.css') }}">
<div x-data="{ activeTab: 'calendar' }" class="flex min-h-screen bg-gray-100 pt-16">

    <!-- Sidebar -->
    <aside class="w-[15vw] bg-white border-r border-gray-200 fixed top-0 bottom-0 left-0 z-40">
        @include('livewire.requester.sidebar')
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col ml-[15vw]">
        <!-- Page Content -->
        <livewire:requester.notification.notification-header />
        <main class="flex-1 p-6 overflow-auto">
            <!-- Notification Header -->

            <!-- Navigation Tabs + Filter Dropdown -->
            <!-- Navigation Tabs + Filter Dropdown -->
            <div class="mb-6">
                
                <!-- Tabs Row -->
                <div class="flex justify-start gap-4 mb-4">
                    <nav class="tab-buttons flex gap-4">
                        <a href="#" @click.prevent="activeTab = 'calendar'"
                            :class="activeTab === 'calendar' ? 'tab-button active' : 'tab-button'">
                            View Calendar
                        </a>
                        <a href="#" @click.prevent="activeTab = 'conference'"
                            :class="activeTab === 'conference' ? 'tab-button active' : 'tab-button'">
                            Conference Room
                        </a>
                    </nav>
                </div>

                <!-- Filter Centered -->
                <div class="d-flex flex-column align-items-center text-center" style="margin-top: -70px;">
                    Filter by Asset or Driver Name
                    </label>

                    <select id="assetFilter" class="w-64 rounded-md border-gray-300 shadow-sm text-center"
                        onchange="filterCalendarByAsset()">
                        <option value="">All</option>

                        @php
                        use App\Models\Booking;
                        use App\Models\Driver;

                        $assetNames = Booking::whereNotNull('asset_name')->distinct()->pluck('asset_name')->sort();
                        $driverNames = Driver::where('is_active', true)->pluck('name')->sort();
                        @endphp

                        <optgroup label="Assets">
                            @foreach ($assetNames as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </optgroup>

                        <optgroup label="Drivers">
                            @foreach ($driverNames as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>


            <!-- View Calendar Tab -->
            <div x-show="activeTab === 'calendar'" x-cloak>
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Your Booking Calendar</h2>
                    </div>



                    <!-- 📅 Calendar -->
                    <div class="calendar-container">
                        @livewire('requester.calendar', ['compactMode' => true])
                    </div>
                </div>
            </div>

            <!-- Conference Room Tab -->
            <div x-show="activeTab === 'conference'" x-cloak>
                @livewire('requester.conference-room')
            </div>

        </main>
    </div>
</div>