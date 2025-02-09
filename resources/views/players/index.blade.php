<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Players') }}
            </h2>
            <a href="{{ route('players.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                Add New Player
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Search and Filter -->
                    <div class="mb-4 flex justify-between items-center">
                        <form action="{{ route('players.index') }}" method="GET" class="flex items-center space-x-4 flex-1">
                            <div class="flex-1 max-w-sm">
                                <input type="text" 
                                    name="search" 
                                    value="{{ request('search') }}"
                                    placeholder="Search by name, ID, position..." 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <select name="status" 
                                    class="rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="injured" {{ request('status') === 'injured' ? 'selected' : '' }}>Injured</option>
                                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search
                            </button>
                            @if(request()->hasAny(['search', 'status', 'sort', 'direction']))
                                <a href="{{ route('players.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Clear
                                </a>
                            @endif
                            <!-- Hidden sort fields -->
                            <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                            <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
                        </form>
                    </div>

                    <!-- Players Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('players.index', array_merge(request()->except(['sort', 'direction']), [
                                            'sort' => 'id',
                                            'direction' => request('sort') === 'id' && request('direction') === 'asc' ? 'desc' : 'asc'
                                        ])) }}" class="flex items-center group">
                                            ID
                                            <span class="ml-1">{{ request('sort') === 'id' ? (request('direction') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('players.index', array_merge(request()->except(['sort', 'direction']), [
                                            'sort' => 'name',
                                            'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'
                                        ])) }}" class="flex items-center group">
                                            Name
                                            <span class="ml-1">{{ request('sort') === 'name' ? (request('direction') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('players.index', array_merge(request()->except(['sort', 'direction']), [
                                            'sort' => 'position',
                                            'direction' => request('sort') === 'position' && request('direction') === 'asc' ? 'desc' : 'asc'
                                        ])) }}" class="flex items-center group">
                                            Position
                                            <span class="ml-1">{{ request('sort') === 'position' ? (request('direction') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('players.index', array_merge(request()->except(['sort', 'direction']), [
                                            'sort' => 'jersey',
                                            'direction' => request('sort') === 'jersey' && request('direction') === 'asc' ? 'desc' : 'asc'
                                        ])) }}" class="flex items-center group">
                                            Jersey
                                            <span class="ml-1">{{ request('sort') === 'jersey' ? (request('direction') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('players.index', array_merge(request()->except(['sort', 'direction']), [
                                            'sort' => 'status',
                                            'direction' => request('sort') === 'status' && request('direction') === 'asc' ? 'desc' : 'asc'
                                        ])) }}" class="flex items-center group">
                                            Status
                                            <span class="ml-1">{{ request('sort') === 'status' ? (request('direction') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('players.index', array_merge(request()->except(['sort', 'direction']), [
                                            'sort' => 'contract',
                                            'direction' => request('sort') === 'contract' && request('direction') === 'asc' ? 'desc' : 'asc'
                                        ])) }}" class="flex items-center group">
                                            Contract
                                            <span class="ml-1">{{ request('sort') === 'contract' ? (request('direction') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($players as $player)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($player->profile_picture)
                                            <img src="{{ asset('storage/' . $player->profile_picture) }}" 
                                                alt="{{ $player->first_name }}'s Profile" 
                                                class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                                <span class="text-yellow-800 font-medium text-sm">
                                                    {{ strtoupper(substr($player->first_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $player->player_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $player->first_name }} {{ $player->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $player->nic }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $player->position }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $player->jersey_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($player->status === 'active') bg-green-100 text-green-800 
                                            @elseif($player->status === 'injured') bg-red-100 text-red-800
                                            @elseif($player->status === 'suspended') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($player->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            LKR {{ number_format($player->contract_amount, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Until {{ $player->contract_end_date->format('Y-m-d') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('players.show', $player) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">View</a>
                                        <a href="{{ route('players.edit', $player) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <form action="{{ route('players.destroy', $player) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this player?')">
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
                        {{ $players->links() }}
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
                            aValue = a.querySelector('td:nth-child(2)').textContent.trim();
                            bValue = b.querySelector('td:nth-child(2)').textContent.trim();
                            break;
                        case 'name':
                            aValue = a.querySelector('td:nth-child(3)').textContent.trim();
                            bValue = b.querySelector('td:nth-child(3)').textContent.trim();
                            break;
                        case 'position':
                            aValue = a.querySelector('td:nth-child(4)').textContent.trim();
                            bValue = b.querySelector('td:nth-child(4)').textContent.trim();
                            break;
                        case 'jersey':
                            aValue = parseInt(a.querySelector('td:nth-child(5)').textContent.trim()) || 0;
                            bValue = parseInt(b.querySelector('td:nth-child(5)').textContent.trim()) || 0;
                            return currentSort.direction === 'asc' ? aValue - bValue : bValue - aValue;
                        case 'status':
                            aValue = a.querySelector('td:nth-child(6) span').textContent.trim();
                            bValue = b.querySelector('td:nth-child(6) span').textContent.trim();
                            break;
                        case 'contract':
                            // Extract contract amount (removing 'LKR' and commas)
                            aValue = parseFloat(a.querySelector('td:nth-child(7) div:first-child').textContent.replace('LKR', '').replace(/,/g, '').trim()) || 0;
                            bValue = parseFloat(b.querySelector('td:nth-child(7) div:first-child').textContent.replace('LKR', '').replace(/,/g, '').trim()) || 0;
                            return currentSort.direction === 'asc' ? aValue - bValue : bValue - aValue;
                        default:
                            return 0;
                    }

                    if (column !== 'jersey' && column !== 'contract') {
                        return currentSort.direction === 'asc' ? 
                            aValue.localeCompare(bValue) : 
                            bValue.localeCompare(aValue);
                    }
                });

                // Reorder the table
                rows.forEach(row => tbody.appendChild(row));
            }

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();

                tableRows.forEach(row => {
                    const name = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const playerId = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const nic = row.querySelector('td:nth-child(3) div:nth-child(2)').textContent.toLowerCase();
                    const position = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                    const jersey = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                    const status = row.querySelector('td:nth-child(6) span').textContent.toLowerCase().trim();

                    const matchesSearch = name.includes(searchTerm) || 
                                        playerId.includes(searchTerm) || 
                                        nic.includes(searchTerm) || 
                                        position.includes(searchTerm) ||
                                        jersey.includes(searchTerm);
                                        
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