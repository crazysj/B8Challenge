<?php
require 'Coupons.php';
$cp = new Coupons;
	$c1_items = file_get_contents("tempCartItems.json");
	print("<pre>".print_r($c1_items,true)."</pre>");
	
	
	echo('Start test<br>');
	$code="BOXLOVE";
	$outlet_id=5;
	print("<br>".$code." Outlet Id: ".$outlet_id."<br>");
	$result=$cp->check_coupon_code($c1_items,$code,$outlet_id);
	print_r($result);

	$code="BOX8LOVE";
	$outlet_id=3;
	print("<br><br>".$code." Outlet Id: ".$outlet_id."<br>");
	$result=$cp->check_coupon_code($c1_items,$code,$outlet_id);
	print_r($result);

	
	$code="HELLOBOX8";
	$outlet_id=14;
	print("<br><br>".$code." Outlet Id: ".$outlet_id."<br>");
	$result=$cp->check_coupon_code($c1_items,$code,$outlet_id);
	print_r($result);

	$code="GETCASHBACK";
	$outlet_id=3;
	print("<br><br>".$code." Outlet Id: ".$outlet_id."<br>");
	$result=$cp->check_coupon_code($c1_items,$code,$outlet_id);
	print_r($result);
	
	$code="BOGO";
	$outlet_id=10;
	print("<br><br>".$code." Outlet Id: ".$outlet_id."<br>");
	$result=$cp->check_coupon_code($c1_items,$code,$outlet_id);
	print_r($result);
?>
