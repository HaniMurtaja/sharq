<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'en' ? 'ltr' : 'rtl' }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="">
    <meta name="keywords" content="">

    <style>
        body,
        html {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .container {
            text-align: center;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .message {
            margin-top: 20px;
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <a href="javascript:history.back()" class="back-button">Back</a>
    <div class="container">
        <img src="{{ asset('system-closed.jpg') }}" alt="System Closed">
        <div class="message">System Closed</div>
    </div>
    
</body>

</html>
