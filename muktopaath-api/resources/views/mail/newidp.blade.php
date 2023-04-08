<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="header">
		<img src="https://muktopaath.gov.bd/static/img/logo.a851287.png">
	</div>

    <h1>প্রিয়, {{ $data['name'] }}!</h1>
    <p> আপনার আইডি {{ $data['to'] }} এবং পাসওয়ার্ড</p>
    <h4>{{ $data['password'] }}</h4>
    <a href="http://muktopaath.gov.bd/login" target="_blank">লগইন করার জন্য ক্লিক করুন</a>
    <p>অথবা, নিচের ইউআরএল এ প্রবেশ করুন</p>
    <br>
    <i>http://muktopaath.gov.bd/login</i>

    <footer>
	    <p class="copyright" style="color:#fff;">The Muktopaath Team <a style="margin-left: 10px; color: #fff;" href="https://front.muktopaath.orangebd.com" target="_blank">Muktopaath.com</a></p>
	</footer>
</body>
</html>