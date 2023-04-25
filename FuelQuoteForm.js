document.addEventListener("DOMContentLoaded", function() {
    const getQuoteBtn = document.getElementById("getQuoteBtn");
    const gallonsRequested = document.getElementById("gallonsRequested");
    const fuelType = document.getElementById("fuelType");
    const deliveryDate = document.getElementById("deliveryDate");
    const suggestedPrice = document.getElementById("suggestedPrice");
    const totalAmount = document.getElementById("totalAmount");

    getQuoteBtn.addEventListener("click", function() {
        if (gallonsRequested.value && fuelType.value && deliveryDate.value) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "main.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    suggestedPrice.value = response.pricePerGallon.toFixed(2);
                    totalAmount.value = response.totalAmount.toFixed(2);
                }
            };

            xhr.send(`gallonsRequested=${gallonsRequested.value}&fuelType=${fuelType.value}&deliveryDate=${deliveryDate.value}`);
        }
    });
});
