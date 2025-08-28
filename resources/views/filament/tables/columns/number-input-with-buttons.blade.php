@php
    $state = $getState();
    $isStateBased = $isStateBased();
    $record = $getRecord();
    $columnName = $viewData['columnName'] ?? 'count';
@endphp

<div
    x-data="{
        state: @js($state),
        isLoading: false,

        async updateValue(newValue) {
            if (newValue < 0) newValue = 0;

            if (this.state == newValue) return;

            this.isLoading = true;

            try {
                const response = await $wire.updateCount(@js($record->getKey()), @js($columnName), newValue);

                if (response?.success) {
                    this.state = newValue;

                    $dispatch('notify', {
                        type: 'success',
                        message: response.message || 'Updated successfully'
                    });
                } else {
                    throw new Error(response?.message || 'Update failed');
                }
            } catch (error) {
                console.error('Update error:', error);

                $dispatch('notify', {
                    type: 'error',
                    message: error.message || 'An error occurred while updating'
                });

                // Reset to original value on error
                $el.querySelector('input').value = this.state;
            } finally {
                this.isLoading = false;
            }
        }
    }"
    class="fi-ta-text-input-column relative"
>
    <input
        type="number"
        x-model="state"
        x-bind:disabled="isLoading"
        x-on:blur="updateValue(parseInt($event.target.value) || 0)"
        x-on:keydown.enter="updateValue(parseInt($event.target.value) || 0)"
        placeholder="0"
        min="0"
        step="1"
        class="fi-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6"
        x-bind:class="{
            'text-center': true,
            'opacity-50 cursor-not-allowed': isLoading
        }"
    />

    <!-- Loading indicator -->
    <div
        x-show="isLoading"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-900/80"
    >
        <svg class="h-4 w-4 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>
