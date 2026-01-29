document.addEventListener("DOMContentLoaded", () => {
    const maxQty = 100; // Maximum quantity
    const minQty = 1; // Minimum quantity

    // Find all counters on the page
    document.querySelectorAll(".counter").forEach(counter => {
        const decrementBtn = counter.querySelector("button[id^='decrement']");
        const incrementBtn = counter.querySelector("button[id^='increment']");
        const numberInput = counter.querySelector("input[id^='numberInput']");

        // Add event listener for the increment button
        incrementBtn.addEventListener("click", () => {
            let currentValue = parseInt(numberInput.value);
            if (currentValue < maxQty) {
                numberInput.value = currentValue + 1;
                decrementBtn.disabled = false; // Enable decrement button
            }
            if (parseInt(numberInput.value) === maxQty) {
                incrementBtn.disabled = true; // Disable increment button
            }
        });

        // Add event listener for the decrement button
        decrementBtn.addEventListener("click", () => {
            let currentValue = parseInt(numberInput.value);
            if (currentValue > minQty) {
                numberInput.value = currentValue - 1;
                incrementBtn.disabled = false; // Enable increment button
            }
            if (parseInt(numberInput.value) === minQty) {
                decrementBtn.disabled = true; // Disable decrement button
            }
        });
    });
});
