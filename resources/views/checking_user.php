<?php
	$email = $_POST["email"];
	if(!empty($email)){
		$link = mysqli_connect("localhost", "root", "") or die("Không thể kết nối được MySQL Database");
		$link->set_charset('utf8');
		mysqli_select_db($link,"tour");

		$sql = "select * from users where email = '".$email."'";
		$result = mysqli_query($link,$sql);
		$dong = mysqli_num_rows($result);
		if($dong) echo "no";
		elseif(! (strstr($email,"@gmail.com") || strstr($email,"@yahoo.com")))
		{
			echo "no";
		}else{ 
			echo "yes";
		}
	}else{
		echo "no";
	}
?>