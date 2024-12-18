<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <form method="GET" class="flex gap-4">
                            <div>
                                <label for="entry_id" class="block text-sm font-medium text-gray-700">Filter by Entry</label>
                                <select name="entry_id" id="entry_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">All Entries</option>
                                    @foreach(Auth::user()->entries as $userEntry)
                                        <option value="{{ $userEntry->id }}" {{ request('entry_id') == $userEntry->id ? 'selected' : '' }}>
                                            {{ $userEntry->entry_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="self-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                    <th class="px-4 py-2">Date</th>
                                    <th class="px-4 py-2">Entry</th>
                                    <th class="px-4 py-2">User</th>
                                    <th class="px-4 py-2">Transaction</th>
                                    <th class="px-4 py-2">Position</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $transaction)
                                    <tr class="text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $transaction->created_at->format('M j, Y g:ia') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $transaction->entry->entry_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $transaction->entry->user->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-red-600">-{{ $transaction->droppedPlayer->name }}</span>
                                                <span class="text-gray-500">/</span>
                                                <span class="text-green-600">+{{ $transaction->addedPlayer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $transaction->roster_position }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No transactions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

