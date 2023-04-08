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
    <!-- <p>	Verify your email address so we know it's really you—and so we can send you important information about your Muktopaath account.</p> -->
    <p>আপনার ইমেইল আড্রেসটি ভেরিফাই করুন যেন আমরা আপনার মুক্তপাঠ অ্যাকাউন্ট সম্পর্কে গুরুত্বপূর্ণ তথ্য পাঠাতে পারি।</p>
    <a href="{{ $data['link'] }}" target="_blank">অ্যাকাউন্ট ভেরিফাই করুন</a>
    <p>অথবা</p>
    <p>নিচের লিংকে প্রবেশ করুন</p>
    <p style="color:blue">{{ $data['link'] }}</p>

    <footer>
	    <p class="copyright" style="color:#fff;">The Muktopaath Team <a style="margin-left: 10px; color: #fff;" href="https://front.muktopaath.orangebd.com" target="_blank">Muktopaath.com</a></p>
	</footer>
</body>
</html>