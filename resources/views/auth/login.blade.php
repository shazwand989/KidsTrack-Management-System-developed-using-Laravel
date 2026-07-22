<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidsTrack | Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #FFF8F1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 950px;
            background: white;
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 15px 35px rgba(0,0,0,.15);
        }

        .left {
            width: 40%;
            background: linear-gradient(135deg, #F28C28, #ffb347);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
        }

        .left h1 {
            font-size: 38px;
            margin-bottom: 15px;
        }

        .left h3 {
            margin-bottom: 15px;
        }

        .left p {
            line-height: 26px;
        }

        .emoji {
            font-size: 90px;
            margin-bottom: 20px;
        }

        .right {
            width: 60%;
            padding: 40px 50px;
        }

        .right h2 {
            color: #5A2E0C;
            margin-bottom: 8px;
        }

        .right .subtitle {
            color: #777;
            margin-bottom: 25px;
        }

        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .role-btn {
            flex: 1;
            padding: 12px 8px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            color: #555;
        }

        .role-btn:hover {
            border-color: #F28C28;
            background: #FFF8F1;
        }

        .role-btn.active {
            border-color: #F28C28;
            background: #F28C28;
            color: white;
        }

        .role-btn .icon {
            font-size: 24px;
            display: block;
            margin-bottom: 4px;
        }

        #role_input {
            display: none;
        }

        label {
            display: block;
            margin-bottom: 8px;
            margin-top: 15px;
            color: #5A2E0C;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            transition: .3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border: 1px solid #F28C28;
        }

        button[type="submit"] {
            width: 100%;
            margin-top: 25px;
            padding: 15px;
            background: #F28C28;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: .3s;
        }

        button[type="submit"]:hover {
            background: #e67d14;
        }

        .remember {
            display: flex;
            align-items: center;
            margin-top: 15px;
            gap: 8px;
        }

        .remember input[type="checkbox"] {
            width: auto;
        }

        .error {
            background: #ffdede;
            color: red;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }

        .footer a {
            color: #F28C28;
            text-decoration: none;
            font-weight: bold;
        }

        @media(max-width:768px) {
            .container {
                width: 95%;
                flex-direction: column;
            }
            .left, .right {
                width: 100%;
            }
            .role-selector {
                flex-wrap: wrap;
            }
            .role-btn {
                flex: 1 1 30%;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="left">
        <div class="emoji">🧸</div>
        <h1>KidsTrack</h1>
        <h3>Parent Portal</h3>
        <p>
            Safe • Smart • Simple
            <br><br>
            Monitor your child's attendance,
            notifications and nursery information
            anytime, anywhere.
        </p>
    </div>

    <div class="right">

        <h2>Welcome Back <i class="fas fa-hand-wave"></i></h2>
        <p class="subtitle">Please select your role and login to continue.</p>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="hidden" name="role" id="role_input" value="parent">

            <div class="role-selector">
                <button type="button" class="role-btn active" data-role="parent">
                    <span class="icon">👨‍👩‍👦</span>
                    Parent
                </button>
                <button type="button" class="role-btn" data-role="guardian">
                    <span class="icon"><i class="fas fa-shield-alt"></i></span>
                    Guardian
                </button>
                <button type="button" class="role-btn" data-role="admin">
                    <span class="icon">👑</span>
                    Admin
                </button>
            </div>

            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                value="{{ old('email') }}"
                required
                autofocus
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <div class="remember">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin:0;">Remember Me</label>
            </div>

            <button type="submit" id="loginBtn">
                Login as Parent
            </button>

        </form>

        <div class="footer">
            Need help?
            <br>
            Contact Nursery Administrator.
        </div>

    </div>

</div>

@if(session('redirect_after_login'))
    <input type="hidden" name="redirect" value="{{ session('redirect_after_login') }}">
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleButtons = document.querySelectorAll('.role-btn');
        const roleInput = document.getElementById('role_input');
        const loginBtn = document.getElementById('loginBtn');

        roleButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                roleButtons.forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                const role = this.dataset.role;
                roleInput.value = role;
                if (role === 'admin') {
                    loginBtn.textContent = 'Login as Admin';
                } else if (role === 'guardian') {
                    loginBtn.textContent = 'Login as Guardian';
                } else {
                    loginBtn.textContent = 'Login as Parent';
                }
            });
        });
    });
</script>

</body>
</html>