<?php
require 'CouponTypes.php';
class Coupons
{
	private $response = array(
			       'valid'=>false,
				    'message'=>"",
					 'discount'=>0,
					   'cashback'=>0				      		
	);
	
	private $coupons;
	private $total_coupons=0;
	private  $item_types=0;
	private $cart_items;
	
	private $coupon_type;
	private $coupon_value;
	private $cashback_value;
	private $minimum_delivery_amount_after_discount;
	private $maximum_discount;
	
	public function __construct()
	{
		$content=file_get_contents("coupon_codes.json");
		$this->coupons=json_decode($content,true);
		$this->total_coupons=count($this->coupons['coupon_codes']);
		//$this->coupons['coupon_codes'][0]['type']
		//echo ' Inside Contructor ';
	}
	
	private function validate_code($coupon_code,$outlet_id)
	{	
		for($i=0;$i<$this->total_coupons;$i++)
		{		
			//print('<br>code '.$this->coupons['coupon_codes'][$i]['code']."<br>");
			if((!strcmp($this->coupons['coupon_codes'][$i]['code'],$coupon_code)))
			{   
				if($this->coupons['coupon_codes'][$i]['active']== true)  //ignoring date because we already have active flag
				{
					$this->coupon_type=$this->coupons['coupon_codes'][$i]['type'];
					$this->coupon_value=$this->coupons['coupon_codes'][$i]['value'];
					$this->cashback_value=$this->coupons['coupon_codes'][$i]['cashback_value'];
					$this->maximum_discount=$this->coupons['coupon_codes'][$i]['maximum_discount'];
					$this->minimum_delivery_amount_after_discount=$this->coupons['coupon_codes'][$i]['minimum_delivery_amount_after_discount'];			
					
					if(sizeof($this->coupons['coupon_codes'][$i]['applicable_outlet_ids']) == 0)
					{
						return true;
					}	
					elseif(in_array($outlet_id,$this->coupons['coupon_codes'][$i]['applicable_outlet_ids']))
					{
					  return true;	
					}
					$this->response['message']="This coupon code is not valid on this Outlet";
					return false;
				}
				$this->response['message']="This coupon code is no longer valid";
				return false;	
			}
			/*else
			{
				$this->response['message']="Invalid Coupon Code!";
				return false;
			}*/	
		}
			$this->response['message']="Invalid Coupon Code!";
			return false;
	}
	
	public function check_coupon_code($cart_items,$coupon_code,$outlet_id)
	{		
		$this->cart_items=json_decode($cart_items,true);
		//print_r($this->cart_items);
		//print($coupon_code.'  '.$outlet_id.' ');
		$this->item_types=count($this->cart_items['cart_items']);
		//print_r();
		$coupon_code=strtoupper($coupon_code);	 //firsT -> FIRST
		//print('<br>code '.$coupon_code."<br>");
		if($this->item_types == 0)
		{
			$this->response['message']="Your cart is empty!";
			return json_encode($this->response);
		}
		
		if(!isset($coupon_code) || trim($coupon_code)==='') //neither empty nor only white space
		{
			$this->response['message']="No Coupon Code Applied!";
			return json_encode($this->response);	
		}
		
		if(!($this->validate_code($coupon_code,$outlet_id)))
		{
			return(json_encode($this->response));
		}
		
		//if a valid coupon then calculate discount
		$ct = new CouponTypes;
		$result=$ct->apply_code($cart_items,$this->coupon_type,$this->coupon_value,$this->cashback_value,
											$this->maximum_discount,$this->minimum_delivery_amount_after_discount);
		return($result);
		
	}


}

?>