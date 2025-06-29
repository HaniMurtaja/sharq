<!DOCTYPE html>
<html lang="en">
<style>
    /* Global Styles */
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f9f9f9;
    }

    /* Container */
    .container {
        text-align: center;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 400px;
    }

    /* Logos */
    .logos {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .logo {
        height: 80px;
        margin: 0 10px;
    }

    /* Message Box */
    .message-box {
        margin-top: 20px;
    }

    #status-title {
        font-size: 24px;
        color: #333;
    }


    #status-message {
        font-size: 16px;
        color: #555;
    }

    /* Countdown */
    .countdown {
        margin-top: 20px;
        font-size: 18px;
        color: #007bff;
    }

    /* Success Message */
    .success {
        color: #28a745;
    }

    /* Error Message */
    .error {
        color: #dc3545;
    }
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodics Connection Status</title>

</head>
<body>
<div class="container">
    <div class="logos">
        <img src="{{ asset('new/src/assets/images/logo-2.png') }}" style="width: 100%;height: 150px" alt="Logo" class="logo">
        <img src="//upload.wikimedia.org/wikipedia/commons/e/ef/Foodics_logo.jpg" style="width: 150px;height: 150px" alt="Foodics Logo" class="logo">
    </div>
    <div class="message-box">
        <h1 id="status-title" class="{{ $message['status'] }}">{{ $message['status'] === 'success' ? 'Success!' : 'Error!' }}</h1>
        <p id="status-message" class="{{ $message['status'] }}">{{ $message['message'] }}</p>
    </div>
    <div class="countdown">
        Redirecting in <span id="countdown">5</span> seconds...
    </div>
</div>
<script>
    // Countdown Timer
    let countdown = 5; // Number of seconds before redirect
    const countdownElement = document.getElementById("countdown");

    const interval = setInterval(() => {
        countdown -= 1;
        countdownElement.textContent = countdown;

        if (countdown === 0) {
            clearInterval(interval);
            // Redirect to another page (change the URL as needed)
            window.location.href = "{{route('show-login')}}";
        }
    }, 1000); // Update every second
</script>
</body>
</html>
