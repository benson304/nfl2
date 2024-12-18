<div
    x-data="{
        showDropdown: false,
        showDialog: false,
        dialogType: '',
        dialogMessage: '',
        init() {
            this.$wire.on('showDialog', (data) => {
                this.dialogType = data[0].type;
                this.dialogMessage = data[0].message;
                this.showDialog = true;
            });
        }
    }"
    class="relative"
>
    <button
        @click="showDropdown = !showDropdown"
        class="px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
        Change Player
    </button>

    <!-- Dialog Modal -->
    <div x-show="showDialog"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true"
         style="display: flex; items: center; justify-content: center;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full justify-center p-4 text-center items-center">
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                     @click.away="showDialog = false">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             :class="dialogType === 'error' ? 'bg-red-100' : 'bg-green-100'">
                            <template x-if="dialogType === 'error'">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </template>
                            <template x-if="dialogType === 'success'">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </template>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" x-text="dialogType === 'error' ? 'Error' : 'Success'"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="dialogMessage"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto"
                                :class="dialogType === 'error' ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500'"
                                @click="showDialog = false">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Player Selection Dropdown -->
    <div
        x-cloak
    x-show="showDropdown"
    @click.away="showDropdown = false"
    class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg z-[60] border"
    style="transform: translateX(-50%);">
        <div class="p-4">
            <form wire:submit="changePlayer">
                <select
                    wire:model="selectedPlayerId"
                    class="w-full rounded-md border-gray-300 text-sm mb-4"
                    required
                >
                    <option value="">Select a player</option>
                    @foreach($players as $player)
                        <option value="{{ $player->id }}">
                            {{ $player->name }} ({{ $player->team->name }})
                        </option>
                    @endforeach
                </select>

                <div class="flex justify-end space-x-2">
                    <button
                        type="button"
                        @click="showDropdown = false"
                        class="px-3 py-1 text-sm text-gray-600 bg-gray-100 rounded hover:bg-gray-200">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                        Confirm Change
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
