<!doctype html>

<html>

<head>
	<title>Welcome to Fuel 2.0</title>
</head>

<body>

<?php echo $body; ?>

<p>
	<strong>Method: </strong> <?php echo Input::method(); ?><br />
	<strong>URI: </strong> <?php echo Input::uri(); ?><br />
	<strong>Query string: </strong> <?php echo json_encode(Input::get()); ?><br />
</p>

</body>

</html>