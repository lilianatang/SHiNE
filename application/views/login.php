
<html>

	<head>

		<meta charset="UTF-8">
		
		<title>SHiNE Login </title>
		
		<!-- Link to External Style Sheet Located in the css folder -->
		<link rel="stylesheet"  href="<?php echo  base_url('style/login-page.css'); ?>" type="text/css">

	</head>

<body>

	<!-- SHiNE Logo Image -->
	<img id = "logo" src="<?php echo  base_url('media/shine-logo.png'); ?>"  alt="SHiNE Portal">

	<!-- Login Form -->
	<?php echo form_open('login/check_users'); ?>

		<label id = "id-label" > User ID: </label>
		<br>

		<input id = "id-input" type = "text" name="username" required> </input>
		<br>
		<label id = "password-label"> Password: </label>
		<br>
		<input id = "password-input" type = "password" name="password" required> </input>
		<br>
		<input type="submit" value="Submit" name="Submit"> </input>

		<p> 

			<?php 
			if ($error == true) {
				echo "Invalid login credentials.";
			} 
			?> 
		</p>
		<br>
	</form>
	
</body>
</html>
