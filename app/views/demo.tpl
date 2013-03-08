<html>
<head>
	<title>Hook test</title>
	<link rel="stylesheet" href="/static/css/demo.css" />
</head>
<body>

	{$content}

	<h1>DEMO FORM</h1>
	<form name="test" method="post" action="http://phetiche.local">
		<input type="text" name="test" />
		<input type="submit" name="Run test" />
		<input type="checkbox" name="game[alpha]" value="alpha" />
		<input type="checkbox" name="game[2]" value="2" />
		<input type="checkbox" name="game[betha]" value="betha" />
		<input type="checkbox" name="game[4]" value="4" />

		<input type="radio" name="checkme" />

	</form>

	{$name|powerball}
	{$footer}

</body>