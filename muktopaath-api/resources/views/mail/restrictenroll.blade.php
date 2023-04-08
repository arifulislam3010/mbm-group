<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {box-sizing: border-box;}

body { 
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.container{
	margin: 0px 70px;
}

.header {
  overflow: hidden;
  background-color: white;
  padding: 15px;
}

.header img {
	width: 100px;
	max-height: 50px;
}

.header a {
  float: left;
  color: black;
  text-align: center;
  padding: 12px;
  text-decoration: none;
  font-size: 18px; 
  line-height: 25px;
  border-radius: 4px;
}

.header a.logo {
  font-size: 25px;
  font-weight: bold;
}

.header a:hover {
  background-color: #ddd;
  color: black;
}

.header a.active {
  background-color: dodgerblue;
  color: white;
}

.header-right {
  float: right;
}

.content {
	padding-left:20px; 
	padding-top: 20px;
	height: auto;
}

footer {
        height: 60px;
        width: 100%;
        background-color: #333333;
        bottom: 0;
    }

    p.copyright {
        position: absolute;
	    width: 89%;
	    color: #fff;
	    line-height: 40px;
	    font-size: 0.7em;
	    text-align: center;
    }

@media screen and (max-width: 500px) {
  .header a {
    float: none;
    display: block;
    text-align: left;
  }
  .header-right {
    float: none;
  }
}
</style>
</head>
<body>
<div class="container">
	<div class="header">
		<img src="https://muktopaath.gov.bd/static/img/logo.a851287.png">
	</div>

	<div class="content">
        <p>{{ $data['course_name'] }} কোর্সটিতে আপনাকে স্বাগতম। কোর্সটি শুরু করার জন্য</p>
        <a href="{{ $data['link'] }}" target="_blank">এখানে ক্লিক করুন</a>
        ।</p>
        <p>{{ $data['link'] }}</p>
        <p>মুক্তপাঠের সাথে থাকার জন্য ধন্যবাদ।</p>
    </div>
	<footer>
	    <p class="copyright" style="color:#fff;">The Muktopaath Team <a style="margin-left: 10px; color: #fff;" href="https://front.muktopaath.orangebd.com" target="_blank">Muktopaath.com</a></p>
	</footer>
</div>
</body>
</html>