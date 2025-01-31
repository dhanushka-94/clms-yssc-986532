@props(['event', 'attendees', 'existingAttendances', 'type'])

<div class="space-y-6">
    <!-- Attendee Type Selector -->
    <div class="p-4 bg-gray-50 rounded-lg">
        <h4 class="text-sm font-medium text-gray-900">Select Attendee Type</h4>
        <div class="flex items-center gap-4 mt-2">
            <a href="{{ request()->fullUrlWithQuery(['type' => 'players']) }}" 
               class="px-3 py-2 text-sm font-medium rounded-md {{ $type === 'players' ? 'bg-indigo-100 text-indigo-700' : 'bg-white text-gray-700' }}">
                Players
            </a>
            <a href="{{ request()->fullUrlWithQuery(['type' => 'staff']) }}" 
               class="px-3 py-2 text-sm font-medium rounded-md {{ $type === 'staff' ? 'bg-indigo-100 text-indigo-700' : 'bg-white text-gray-700' }}">
                Staff
            </a>
            <a href="{{ request()->fullUrlWithQuery(['type' => 'members']) }}" 
               class="px-3 py-2 text-sm font-medium rounded-md {{ $type === 'members' ? 'bg-indigo-100 text-indigo-700' : 'bg-white text-gray-700' }}">
                Members
            </a>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="p-4 bg-gray-50 rounded-lg">
        <h4 class="text-sm font-medium text-gray-900">Bulk Actions</h4>
        <div class="flex items-center gap-4 mt-2">
            <select id="bulkStatus" class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <option value="">Select Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="excused">Excused</option>
            </select>
            <button type="button" onclick="applyBulkStatus()" class="px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Apply to All
            </button>
        </div>
    </div>

    <input type="hidden" name="type" value="{{ $type }}">

    <!-- Attendance Table -->
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Check In</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Check Out</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($attendees as $attendee)
                    @php
                        $attendance = $existingAttendances->firstWhere('attendee_id', $attendee->id);
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            <input type="hidden" name="attendances[{{ $loop->index }}][attendee_id]" value="{{ $attendee->id }}">
                            {{ $attendee->first_name }} {{ $attendee->last_name }}
                            @if(isset($attendee->jersey_number))
                                <span class="ml-1 text-gray-500">#{{ $attendee->jersey_number }}</span>
                            @endif
                            @if(isset($attendee->role))
                                <span class="ml-1 text-gray-500">({{ $attendee->role }})</span>
                            @endif
                            @if(isset($attendee->membership_type))
                                <span class="ml-1 text-gray-500">({{ $attendee->membership_type }})</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <select name="attendances[{{ $loop->index }}][status]" class="attendance-status block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="present" @selected($attendance?->status === 'present')>Present</option>
                                <option value="absent" @selected(!$attendance || $attendance->status === 'absent')>Absent</option>
                                <option value="late" @selected($attendance?->status === 'late')>Late</option>
                                <option value="excused" @selected($attendance?->status === 'excused')>Excused</option>
                            </select>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <input type="time" name="attendances[{{ $loop->index }}][check_in_time]" value="{{ $attendance?->check_in_time?->format('H:i') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <input type="time" name="attendances[{{ $loop->index }}][check_out_time]" value="{{ $attendance?->check_out_time?->format('H:i') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <input type="text" name="attendances[{{ $loop->index }}][remarks]" value="{{ $attendance?->remarks }}" placeholder="Add remarks..." class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-sm text-center text-gray-500">
                            No {{ str($type)->singular() }} found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function applyBulkStatus() {
    const status = document.getElementById('bulkStatus').value;
    if (!status) return;

    const statusSelects = document.querySelectorAll('.attendance-status');
    statusSelects.forEach(select => {
        select.value = status;
    });
}
</script> 