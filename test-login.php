<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OMH Cebu IT | Timetracker</title>
    <style>
        :root {
            --accent: #42bff5;
            --bg-color: #f4f7f6;
            --text-main: #333;
            --text-muted: #666;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            max-width: 800px;
            width: 90%;
            gap: 50px;
            align-items: center;
        }

        /* Brand Section */
        .brand-section {
            width: 300px;
            text-align: center;
            border-right: 1px solid #eee;
            padding-right: 50px;
        }

        .brand-section h1 {
            color: #1a4a7a; /* Mimicking the logo's dark blue */
            margin-bottom: 5px;
            font-size: 2rem;
        }

        .brand-section p {
            color: var(--text-muted);
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .login-logo {
            width: 100%;
        }

        /* Form Section */
        .login-box {
            flex: 1;
        }

        h2 {
            margin-top: 0;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.1s ease;
        }

        button:hover {
            background-color: #35a8db;
        }

        button:active {
            transform: scale(0.98);
        }

        .logo-text {
            font-size: 24px;
            color: #666;
        }

        /* Responsive Fix */
        @media (max-width: 600px) {
            .container {
                flex-direction: column;
                padding: 30px;
                gap: 20px;
            }
            .brand-section {
                border-right: none;
                padding-right: 0;
                border-bottom: 1px solid #eee;
                padding-bottom: 20px;
            }

            .left-logo img{
                position: relative;
                box-sizing: border-box;
                width: 100px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="brand-section">
        <img class="login-logo" src="image/omegalogo.png" alt="">
        <div style="margin-top: 30px;">
            <strong><span class="logo-text">OJT Timetracker</span></strong><br>
            
        </div>
    </div>

    <div class="login-box">
        <h2>Log In</h2>
        <form action="#" method="post">
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" placeholder="name@email.com" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="submit">Sign In</button>
        </form>
    </div>
</div>

</body>
</html>