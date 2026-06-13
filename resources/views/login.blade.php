<!DOCTYPE html>
<html>
<head>
<title>Login Page</title>
<style>
body { font-family: Arial; background:#f2f2f2; }
.box { width:300px; margin:100px auto; padding:20px;
background:white; border-radius:8px; box-shadow:0 0 10px #ccc;
}
input { width:100%; padding:8px; margin:5px 0; }
button { width:100%; padding:8px; background:#3490dc;
color:white; border:none; }
.error { color:red; }
.link { text-align:center; margin-top:10px; }
.link a { color:#3490dc; text-decoration:none; }
</style>
</head>
<body>
<div class="box">
<h2>Login</h2>
@if(session('error'))
<div class="error">{{ session('error') }}</div>
@endif
<form method="POST" action="/login">
 @csrf
 <input type="email" name="email" placeholder="Email"
value="{{ old('email') }}">
 <input type="password" name="password"
placeholder="Password">
 <button type="submit">Login</button>
</form>
<div class="link">
 <a href="/register">Don't have an account? Register
here</a>
</div>
</div>
</body>
</html>