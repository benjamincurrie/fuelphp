<!doctype html>

<html>

<head>
	<title>Welcome to Fuel 2.0</title>
</head>

<body>

<?php echo $body; ?>

<?php $input = _app()->active_request()->input; ?>
<p>
	<strong>Method: </strong> <?php echo $input->method(); ?><br />
	<strong>URI: </strong> <?php echo $input->uri(); ?><br />
	<strong>Query string: </strong> <?php echo json_encode($input->query_string()); ?><br />
</p>

</body>

</html>