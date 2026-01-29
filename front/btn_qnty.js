// Select elements
const decrementBtn = document.getElementById('decrement');
const incrementBtn = document.getElementById('increment');
const numberInput = document.getElementById('numberInput');

// Max and Min limits
const MAX_VALUE = 100;
const MIN_VALUE = 1;

// Increment function
incrementBtn.addEventListener('click', () => {
    let currentValue = parseInt(numberInput.value, 10);

    if (currentValue < MAX_VALUE) {
        numberInput.value = currentValue + 1;
    }

    // Enable/disable buttons based on value
    toggleButtons();
});

// Decrement function
decrementBtn.addEventListener('click', () => {
    let currentValue = parseInt(numberInput.value, 10);

    if (currentValue > MIN_VALUE) {
        numberInput.value = currentValue - 1;
    }

    // Enable/disable buttons based on value
    toggleButtons();
});

// Function to toggle button states
function toggleButtons() {
    const value = parseInt(numberInput.value, 10);

    // Disable decrement button if value is at the minimum
    decrementBtn.disabled = value <= MIN_VALUE;

    // Disable increment button if value is at the maximum
    incrementBtn.disabled = value >= MAX_VALUE;
}