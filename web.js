async function fetchWeather(cityName = "Mobile,usa") {
    const apiUrl = `http://nirdeshprototype.kesug.com/prototype/weather-api.php?q=${cityName}`;

    try {
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error("City not found. Please try another city.");
        }
        const data = await response.json();
        console.log(data); // Log the entire response to check its structure

        if (data.length === 0 || !data[0]) {
            throw new Error("No weather data available.");
        }

        const weatherData = data[0];  // Assuming the data is in the first object of the array

        // Displaying the weather data
        document.getElementById("city").textContent = weatherData.city || cityName;
        document.getElementById("date").textContent = new Date().toLocaleDateString(undefined, {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
        });
        document.getElementById("main-condition").textContent = weatherData.main_condition || "No condition";
        document.getElementById("condition").textContent = weatherData.description || "No description available";

        if (weatherData.icon_url) {
            document.getElementById("icon").src = weatherData.icon_url;
        } else {
            document.getElementById("icon").src = "default-icon.png"; // Replace with your fallback image
        }

        document.getElementById("temperature").textContent = `${weatherData.temperature} °C`;
        document.getElementById("pressure").textContent = `${weatherData.pressure} hPa`;
        document.getElementById("humidity").textContent = `${weatherData.humidity}%`;
        document.getElementById("wind").textContent = `${weatherData.wind} m/s`;

    } catch (error) {
        alert(error.message);
        console.error("Error fetching weather data:", error);
    }
}
function handleSearch() {
    const cityInput = document.getElementById("city-input").value.trim();
    if (cityInput) {
        fetchWeather(cityInput);
    } else {
        alert("Please enter a city name.");
    }
}
document.getElementById("city-input").addEventListener("keypress", (event) => {
    if (event.key === "Enter") {
        handleSearch();
    }
});
fetchWeather();