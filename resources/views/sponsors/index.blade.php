<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sponsors') }}
            </h2>
            <a href="{{ route('sponsors.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                Add New Sponsor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Search and Filter -->
                    <div class="mb-4 flex justify-between items-center">
                        <div class="flex-1 max-w-sm">
                            <input type="text" id="search" placeholder="Search sponsors..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        </div>
                        <div class="ml-4">
                            <select id="status-filter" class="rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sponsors Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-yellow-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-yellow-100" data-sort="id">
                                        Sponsor ID
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-yellow-100" data-sort="company">
                                        Company
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-yellow-100" data-sort="contact">
                                        Contact
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-yellow-100" data-sort="status">
                                        Status
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-yellow-100" data-sort="contract">
                                        Contract
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sponsors as $sponsor)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sponsor->sponsor_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($sponsor->profile_picture)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $sponsor->profile_picture) }}" alt="{{ $sponsor->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                                        <span class="text-yellow-800 font-medium text-sm">
                                                            {{ strtoupper(substr($sponsor->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $sponsor->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $sponsor->contact_person }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $sponsor->email }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $sponsor->phone }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($sponsor->status === 'active') bg-green-100 text-green-800 
                                            @elseif($sponsor->status === 'inactive') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($sponsor->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            LKR {{ number_format($sponsor->sponsorship_amount, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Until {{ $sponsor->sponsorship_end_date ? $sponsor->sponsorship_end_date->format('Y-m-d') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('sponsors.show', $sponsor) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">View</a>
                                        <a href="{{ route('sponsors.edit', $sponsor) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <form action="{{ route('sponsors.destroy', $sponsor) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this sponsor?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $sponsors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const statusFilter = document.getElementById('status-filter');
            const tableRows = document.querySelectorAll('tbody tr');
            const sortableHeaders = document.querySelectorAll('th[data-sort]');
            let currentSort = { column: null, direction: 'asc' };

            // Sorting function
            function sortTable(column) {
                const tbody = document.querySelector('tbody');
                const rows = Array.from(tableRows);
                const sortIcons = document.querySelectorAll('.sort-icon');

                // Reset all sort icons
                sortIcons.forEach(icon => icon.textContent = '↕');

                // Update sort direction
                if (currentSort.column === column) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = column;
                    currentSort.direction = 'asc';
                }

                // Update sort icon for current column
                const currentHeader = document.querySelector(`th[data-sort="${column}"]`);
                const currentIcon = currentHeader.querySelector('.sort-icon');
                currentIcon.textContent = currentSort.direction === 'asc' ? '↑' : '↓';

                // Sort the rows
                rows.sort((a, b) => {
                    let aValue, bValue;

                    switch(column) {
                        case 'id':
                            aValue = a.querySelector('td:nth-child(1)').textContent.trim();
                            bValue = b.querySelector('td:nth-child(1)').textContent.trim();
                            break;
                        case 'company':
                            aValue = a.querySelector('td:nth-child(2) .text-gray-900').textContent.trim();
                            bValue = b.querySelector('td:nth-child(2) .text-gray-900').textContent.trim();
                            break;
                        case 'contact':
                            aValue = a.querySelector('td:nth-child(3)').textContent.trim();
                            bValue = b.querySelector('td:nth-child(3)').textContent.trim();
                            break;
                        case 'status':
                            aValue = a.querySelector('td:nth-child(4) span').textContent.trim();
                            bValue = b.querySelector('td:nth-child(4) span').textContent.trim();
                            break;
                        case 'contract':
                            // Extract contract amount (removing 'LKR' and commas)
                            aValue = parseFloat(a.querySelector('td:nth-child(5) div:first-child').textContent.replace('LKR', '').replace(/,/g, '').trim()) || 0;
                            bValue = parseFloat(b.querySelector('td:nth-child(5) div:first-child').textContent.replace('LKR', '').replace(/,/g, '').trim()) || 0;
                            return currentSort.direction === 'asc' ? aValue - bValue : bValue - aValue;
                        default:
                            return 0;
                    }

                    if (column !== 'contract') {
                        return currentSort.direction === 'asc' ? 
                            aValue.localeCompare(bValue) : 
                            bValue.localeCompare(aValue);
                    }
                });

                // Reorder the table
                rows.forEach(row => tbody.appendChild(row));
            }

            // Filter function
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();

                tableRows.forEach(row => {
                    const name = row.querySelector('td:nth-child(2) .text-gray-900').textContent.toLowerCase();
                    const sponsorId = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    const contact = row.querySelector('td:nth-child(2) .text-gray-500').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(3) div:first-child').textContent.toLowerCase();
                    const phone = row.querySelector('td:nth-child(3) div:last-child').textContent.toLowerCase();
                    const status = row.querySelector('td:nth-child(4) span').textContent.toLowerCase().trim();

                    const matchesSearch = name.includes(searchTerm) || 
                                       sponsorId.includes(searchTerm) || 
                                       contact.includes(searchTerm) || 
                                       email.includes(searchTerm) || 
                                       phone.includes(searchTerm);
                                       
                    const matchesStatus = statusValue === '' || status === statusValue;

                    row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
                });
            }

            // Add event listeners
            searchInput.addEventListener('input', filterTable);
            statusFilter.addEventListener('change', filterTable);
            sortableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.getAttribute('data-sort');
                    sortTable(column);
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 