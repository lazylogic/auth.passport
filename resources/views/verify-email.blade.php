<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
</head>
<body>
    <h2>Email Verification</h2>
    Result: <span id="result"></span>

    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <script>
        $( document ).ready(function() {
            console.log( window.location.search );

            // 페이지가 로딩 되면
            // query string 으로 전달된 parameters 를 인증 API 로 전달 한다.
            // '/api/verify' 은 signature 를 만들 때 사용한 route URL 과 동일 해야 한다
            $.post( '/api/verify' + window.location.search )
            .done( function( response ) {
                console.log( response );
                $("#result").text("Veriry Success");
            })
            .fail( function( error ) {
                console.log( error.responseJSON );
                $("#result").text("Veriry fail");
            })
        });
    </script>
</body>
</html>