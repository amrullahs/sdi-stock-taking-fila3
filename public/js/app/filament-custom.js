// Custom JavaScript for Filament client-side calculations
function calculateTotal(inputElement) {
    // Find the parent row
    const row = inputElement.closest('tr');
    
    if (!row) return;
    
    // Get input values
    const storageInput = row.querySelector('.storage-input');
    const wipInput = row.querySelector('.wip-input');
    const ngInput = row.querySelector('.ng-input');
    const totalCell = row.querySelector('.total-cell');
    
    if (!storageInput || !wipInput || !ngInput || !totalCell) return;
    
    // Parse values (handle empty/NaN)
    const storageValue = parseFloat(storageInput.value) || 0;
    const wipValue = parseFloat(wipInput.value) || 0;
    const ngValue = parseFloat(ngInput.value) || 0;
    
    // Calculate total
    const total = storageValue + wipValue + ngValue;
    
    // Update total cell
    totalCell.textContent = total.toLocaleString();
    
    // Also update the hidden input if it exists
    const totalInput = row.querySelector('input[name*="total_count"]');
    if (totalInput) {
        totalInput.value = total;
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to existing inputs
    const storageInputs = document.querySelectorAll('.storage-input');
    const wipInputs = document.querySelectorAll('.wip-input');
    const ngInputs = document.querySelectorAll('.ng-input');
    
    storageInputs.forEach(input => {
        input.addEventListener('input', function() {
            calculateTotal(this);
        });
    });
    
    wipInputs.forEach(input => {
        input.addEventListener('input', function() {
            calculateTotal(this);
        });
    });
    
    ngInputs.forEach(input => {
        input.addEventListener('input', function() {
            calculateTotal(this);
        });
    });
});

// Handle dynamically added inputs (for modals)
document.addEventListener('livewire:init', function() {
    Livewire.hook('element.initialized', (el, component) => {
        // Check if this is a modal or table with our inputs
        const storageInputs = el.querySelectorAll('.storage-input');
        const wipInputs = el.querySelectorAll('.wip-input');
        const ngInputs = el.querySelectorAll('.ng-input');
        
        storageInputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateTotal(this);
            });
        });
        
        wipInputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateTotal(this);
            });
        });
        
        ngInputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateTotal(this);
            });
        });
    });
});