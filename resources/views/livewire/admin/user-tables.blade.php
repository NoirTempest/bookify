<div class="space-y-10">
    @foreach ([
        'admins' => 'Admins',
        'approvers' => 'Approvers',
        'users' => 'Regular Users',
        'drivers' => 'Drivers',
        'adminStaffs' => 'Admin Staff',
    ] as $group => $label)

        <div>
            <h2 class="text-xl font-bold mb-2">{{ $label }}</h2>
            <table class="w-full table-auto border">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-2">Name</th>
                        <th class="p-2">Email</th>
                        <th class="p-2">Department</th>
                        <th class="p-2">Branch</th>
                        <th class="p-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($$group as $user)
                        <tr class="border-t">
                            <td class="p-2">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td class="p-2">{{ $user->email }}</td>
                            <td class="p-2">{{ $user->department->name ?? '-' }}</td>
                            <td class="p-2">{{ $user->branch->name ?? '-' }}</td>
                            <td class="p-2">
                                <span class="px-2 py-1 rounded text-white text-sm {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-2">
                {{ $$group->links() }}
            </div>
        </div>

    @endforeach
</div>
