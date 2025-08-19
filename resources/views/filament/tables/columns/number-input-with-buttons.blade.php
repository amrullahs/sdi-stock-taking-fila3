<div class="flex items-center justify-center space-x-1" x-data="{
    value: {{ $getState() ?? 'null' }},
    isLoading: false,
    async updateValue(newValue) {
        if (newValue < 0) newValue = 0;
        this.isLoading = true;
        
        try {
            const response = await $wire.updateCount('{{ $getRecord()->id }}', '{{ $columnName }}', newValue);
            if (response.success) {
                this.value = newValue;
                $dispatch('notify', {
                    type: 'success',
                    message: response.message || 'Count updated successfully'
                });
            } else {
                $dispatch('notify', {
                    type: 'error', 
                    message: response.message || 'Failed to update count'
                });
            }
        } catch (error) {
            $dispatch('notify', {
                type: 'error',
                message: 'An error occurred while updating'
            });
        } finally {
            this.isLoading = false;
        }
    }
}">
    <!-- Decrease Button -->
    <button 
        type="button"
        class="flex items-center justify-center w-6 h-6 text-xs font-medium text-gray-500 bg-gray-100 border border-gray-300 rounded-l hover:bg-gray-200 focus:ring-2 focus:ring-primary-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600"
        x-bind:disabled="isLoading || (value !== null && value <= 0)"
        x-on:click="updateValue(value - 1)"
    >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
        </svg>
    </button>

    <!-- Input Field -->
    <input 
        type="number" 
        class="w-16 h-6 text-xs text-center border-t border-b border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        x-model="value"
        x-bind:disabled="isLoading"
        x-on:change="updateValue(parseInt($event.target.value) || 0)"
        x-on:keydown.enter="updateValue(parseInt($event.target.value) || 0)"
        placeholder="-"
        min="0"
        step="1"
        x-bind:class="{ 'text-gray-400 italic': value === null }"
    >

    <!-- Increase Button -->
    <button 
        type="button"
        class="flex items-center justify-center w-6 h-6 text-xs font-medium text-gray-500 bg-gray-100 border border-gray-300 rounded-r hover:bg-gray-200 focus:ring-2 focus:ring-primary-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600"
        x-bind:disabled="isLoading"
        x-on:click="updateValue(value + 1)"
    >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
    </button>

    <!-- Loading Indicator -->
    <div x-show="isLoading" class="ml-1">
        <svg class="w-3 h-3 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>