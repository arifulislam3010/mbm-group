<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>muktopaath</title>
</head>
<body>
    <div class="header">
		<img src="https://muktopaath.gov.bd/static/img/logo.a851287.png">
	</div>

    <h2>প্রিয়, {{ $data['name'] }}!</h2>
    <p>	আপনার অ্যাকাউন্ট থেকে পাসওয়ার্ড ভুলে যাওয়া সংক্রান্ত একটি অনুরোধ পেয়েছি</p>
    <p>আপনার পাসওয়ার্ড রিসেট করতে নিম্নলিখিত কোডটি লিখুন</p>
    <h3>{{ $data['otp'] }}</h3>

    <footer>
	    <p class="copyright" style="color:#fff;">The Muktopaath Team <a style="margin-left: 10px; color: #fff;" href="https://front.muktopaath.orangebd.com" target="_blank">Muktopaath.com</a></p>
	</footer>
</body>
</html>