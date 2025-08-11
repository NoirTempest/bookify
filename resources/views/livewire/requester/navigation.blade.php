<nav class="bg-gray-50 border-b border-gray-200 px-4 py-2 flex gap-6 text-sm font-medium text-gray-600 sticky top-[4rem] z-20">
    <a href="{{ route('requester.calendar') }}"
       class="{{ request()->routeIs('requester.calendar') ? 'text-blue-600 font-semibold' : 'hover:text-blue-600' }}">
        📅 Calendar View
    </a>
    <a href="{{ route('requester.status') }}"
       class="{{ request()->routeIs('requester.status') ? 'text-blue-600 font-semibold' : 'hover:text-blue-600' }}">
        🏢 Conference Status
    </a>
</nav>
