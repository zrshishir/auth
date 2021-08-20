<!DOCTYPE html>
<html>
<head>
    <title>kings</title>
</head>
<body>
<h1>{{ $details['title'] }}</h1>
@if($details['code'] == 0)
    <a href="{{ $details['body'] }}">Please click here to signup.</a>
@else
    <p>{{ $details['code'] }}</p>
@endif
<p>Thank you</p>
</body>
</html>
