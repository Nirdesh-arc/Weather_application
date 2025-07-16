async function fetchWeather(cityName = "Talladega") {
    // Your API key
    const apiKey = 'd3e6fc7229c1690c12cd591a05a0f350';
    const apiUrl = `http://localhost/Prototype2/ser.php?q=${cityName}`;
  
  
    try {
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error("City not found. Please try again.");
        }
        const data = await response.json();
  
  
        // Update UI elements
        const cityWithCountry = `${data.name}, ${data.sys.country}`; // Add country code
        document.getElementById("location").textContent = cityWithCountry;
        document.getElementById("Day-date").textContent = new Date().toLocaleDateString(undefined, {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        });
      
        document.getElementById("weather-condition").textContent = data.weather[0].main;
        document.getElementById("main-weather").textContent = data.weather[0].description;
        document.getElementById("weather-icon").src = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;
        document.getElementById("temperature").textContent = `Temperature: ${data.main.temp.toFixed(1)} °C`;
        document.getElementById("pressure").textContent = `Pressure: ${data.main.pressure} hPa`;
        document.getElementById("humidity").textContent = `Humidity: ${data.main.humidity} %`;
        document.getElementById("wind-speed").textContent = `Wind Speed: ${data.wind.speed.toFixed(1)} m/s, ${data.wind.deg}°`;
  
  
    } catch (error) {
        alert(error.message);
        console.error("Error fetching weather data:", error);
    }
   }
  
  
   function getweather() {
    const cityInput = document.getElementById("city-input").value.trim();
    if (cityInput) {
        fetchWeather(cityInput);
    } else {
        alert("Please enter a city name.");
    }
   }
  
  
   // Fetch default city's weather when the page loads
   document.addEventListener("DOMContentLoaded", () => {
    fetchWeather(); // Default city is "Talladega"
   });