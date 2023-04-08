<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @if($type=='newpw')

    <h1>Hi, {{ $name }}!</h1>
    <p> Your id is {{$email}} and password is {{$pw}}</p>
       <a href="http://demo.muktopaath.gov.bd/login" target="_blank">click to login</a>
    <br><br>
    @elseif($type=='approval')

    <h1>Hi, {{ $name }}!</h1>
    <p> your institution request  is approved</p>
       <a href="http://demo.muktopaath.gov.bd/login" target="_blank">click to login</a>
    <br><br>
    @elseif($type=='newrole')

    <h1>Hi, {{ $name }}!</h1>
    <p> You are assigned {{$role}} role for {{$institution}} in muktopaath </p>
       <a href="http://demo.muktopaath.gov.bd/login" target="_blank">click to login</a>
    <br><br>
    @elseif($type=='partner_request')

    <p> You have requested for a new partner request as {{$institution_name}} in muktopaath </p>
       <a href="http://demo.muktopaath.gov.bd/login" target="_blank">click to login</a>
    <br><br>
    @else
    <h1>Hi, {{ $name }}!</h1>
    <p> Verify your email address so we know it's really you—and so we can send you important information about your Muktopaath account.</p>
    <br><br>
    @endif

</body>
</html>