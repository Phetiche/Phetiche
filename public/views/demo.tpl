<html>
<head>
	<title>Hook test</title>
	<link rel="stylesheet" href="/static/css/demo.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>

</head>
<body>

	{$content}

	<h1>DEMO FORM</h1>
	<form name="test" method="post" action="http://phetiche.local">
		<input type="text" name="test" />
		<input type="file" name="file" id="file" />
		<input type="submit" name="Run test" />
		<input type="checkbox" name="game[alpha]" value="alpha" />
		<input type="checkbox" name="game[2]" value="2" />
		<input type="checkbox" name="game[betha]" value="betha" />
		<input type="checkbox" name="game[4]" value="4" />

		<input type="radio" name="checkme" />

	</form>

	{$name|powerball}
	{$footer}

	<script>

		(function() {

			var max_size = 100; // IN Kb

			$("#file").change(function() {

				if (!this.files) {
					alert("This browser doesn't seem to support the files property.");
				} else {

					var size = this.files[0].size / 1024; // In Kb
					if (size > max_size) {
						alert('File is too large');
					}

				}

			});
		})();

	</script>


</body>
