<div class="p-4">

    @if (count($conferenceRooms))
    <div class="overflow-x-auto">
        <div class="flex space-x-4 min-w-max">
            @foreach ($conferenceRooms as $room)
            <div class="bg-white border rounded-lg shadow p-4 w-64">
                <h3 class="text-md font-semibold text-gray-800 mb-1">{{ $room->name }}</h3>
                <p class="text-xs text-gray-500 mb-3">{{ $room->location }}</p>

                @forelse ($room->bookings as $booking)
                @php
                $statusColor = match ($booking->timeline_status) {
                'Ended' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                'Ongoing' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                'Incoming' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                };

                $eventBg = match ($booking->timeline_status) {
                'Ended' => 'bg-purple-100 border-purple-200 text-purple-800',
                'Ongoing' => 'bg-green-100 border-green-200 text-green-800',
                'Incoming' => 'bg-orange-100 border-orange-200 text-orange-800',
                default => 'bg-gray-100 border-gray-200 text-gray-800',
                };
                @endphp

                <div class="border p-2 mb-2 rounded {{ $eventBg }}">
                    <p class="text-sm font-semibold {{ $statusColor['text'] }}">
                        {{ $booking->title }}
                    </p>
                    <p class="text-xs text-gray-600">
                        {{ \Carbon\Carbon::parse($booking->date)->format('F d, Y') }}<br>
                        {{ \Carbon\Carbon::parse($booking->time_from)->format('g:i A') }} -
                        {{ \Carbon\Carbon::parse($booking->time_to)->format('g:i A') }}
                    </p>
                    <p class="text-xs text-gray-600">{{ $booking->booked_by }}</p>
                    {{-- <span
                        class="inline-block mt-1 {{ $statusColor['text'] }} {{ $statusColor['bg'] }} px-2 py-0.5 text-xs rounded">
                        {{ $booking->timeline_status }}
                    </span> --}}
                </div>
                @empty
                <p class="text-xs text-gray-400 italic">No bookings</p>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="text-center text-gray-400 mt-8">
        <p class="text-sm italic">No conference rooms available.</p>
    </div>
    @endif
</div>