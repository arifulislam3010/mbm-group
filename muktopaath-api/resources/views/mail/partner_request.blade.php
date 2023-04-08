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

    <h1>প্রিয়, {{ $name }}!</h1>
    <?php if($partner_type=='institution'){  ?>
    <p> আপনার প্রতিষ্ঠানের {{$name}} আবেদনটি অনুমোদন দেওয়া হয়েছে।</p>
    <?php }else{ ?>
    <p> মুক্তপাঠে আপনার আবেদনকৃত ({{$partner_type}}) রোলটি এই মূহুর্তে এডমিন ডেস্কে অপেক্ষামান। আপনার আবেদনটি গৃহীত হলে ফিরতি মেসেজে জানিয়ে দেয়া হবে। <!-- আপনাকে মুক্তপাঠে {{$partner_type}} রোলে নিযুক্ত করা হয়েছে --></p>
    <?php } ?>
    <br><br>
    
    <a href="http://demo.muktopaath.gov.bd/login" target="_blank">লগইন করার জন্য ক্লিক করুন</a>

    <footer>
	    <p class="copyright" style="color:#fff;">The Muktopaath Team <a style="margin-left: 10px; color: #fff;" href="https://front.muktopaath.orangebd.com" target="_blank">Muktopaath.com</a></p>
	</footer>
</body>
</html>