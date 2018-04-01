<?php

class CouponTypes
{   	
	private $cart_items=array();
	private $type;
	private $value;
	private $cashback_value;
	private $minimum_delivery_amount_after_discount;
	private $maximum_discount;	
	private $output = array(
			       'valid'=>false,
				    'message'=>"",
					 'discount'=>0.0,
					   'cashback'=>0.0				      		
	);
    
	private function calculate_cart_amount()
	{
		$cart_amount=0;
		foreach($this->cart_items as $items)
		{   foreach($items as $item)
		    {
				$cart_amount+=($item->quantity * $item->unit_cost);
			}
		}
		//echo($cart_amount);
		return $cart_amount;
	}
	
	private function calculate_bogo_discount($total_items)
	{
		$price_map=array();//price=>quantity
		foreach($this->cart_items as $items)
		{ 
			foreach($items as $item)
			{
			$price=$item->unit_cost;
			$quantity=$item->quantity;	
			if(isset($price_map[$price]))
			{
				$price_map[$price]+=$quantity;
			}
			else
			{
				//array_push($price,$quantity);
				$price_map[$price]=$quantity;
			}
			}	
		}
		ksort($price_map);//sort by price
		$bogo_discount=0;
		$total_bogo_item=0;
		foreach($price_map as $p=>$q)
		{
			if(($total_bogo_item<$total_items/2) && $bogo_discount<$this->maximum_discount)
			{
				if($total_bogo_item+$q <=  $total_items/2)
				{
					$bogo_discount+=($p*$q);
					$total_bogo_item+=$q;
				}
				else
				{
					$bogo_discount+=($p*(($total_items/2)-$total_bogo_item));
					$total_bogo_item+=(($total_items/2)-$total_bogo_item);
				}		
			}	
		}
			
		if($bogo_discount > $this->maximum_discount)
		{
			$bogo_discount=$this->maximum_discount;
		}
		
		return $bogo_discount;
	}

	
	private function check_minimum_delivery_amount($cart_amount)
	{
		if($cart_amount >= $this->minimum_delivery_amount_after_discount )
		{
			$this->output['valid']=true;
			$this->output['message'].=" Coupon Applied";
			return true;	
		}
		else
		{
			$this->output['valid']=false;
			$this->output['message'].="With this coupon cart amount must be minimum ".$this->minimum_delivery_amount_after_discount." after applying coupon!";
			return false;
		}		
	}
	
	private function percentage()
	{
		$cart_amount=$this->calculate_cart_amount();
		$discount=$cart_amount*($this->value/100); // expected discount
			//$actual_discount=$expected_discount;
		if($discount > $this->maximum_discount)
		{
			$discount=$this->maximum_discount;
			$this->output['message']="Maximum Discount is ".$this->maximum_discount.". ";
		}		
		//check minimum_delivery_amount_after_discount
		$cart_amount-=$discount;
		$this->check_minimum_delivery_amount($cart_amount);
		//returning discount & cashback values even when cart amount is less than minimum delivery amount 
		$this->output['discount']=$discount;
		$this->output['cashback']=$this->cashback_value; //zero
		
		return json_encode($this->output);
	}
	
	private function discount()
	{
		$cart_amount=$this->calculate_cart_amount();
		if($this->value > $this->maximum_discount)
		{
			$this->value=$this->maximum_discount;
			$this->output['message']="Maximum Discount is ".$maximum_discount." ";
		}		
		if($this->value > $cart_amount)
		{
			$this->value=$cart_amount;
		}
		/*else
		{
			$cart_amount-=$this->value;
		}*/
		 $this->check_minimum_delivery_amount($cart_amount);
	     $this->output['discount']=$this->value;
		 $this->output['cashback']=$this->cashback_value;  
		return json_encode($this->output);
	}
	
	private function discount_cashback()
	{
		//assuming maximum_discount will be compared to value only and cashback does not depend on maximum_discount
		return($this->discount());
		// no check on cashback amount because no such condition is mentioned in the problem statement
	}
	
	private function bogo()
	{
		$cart_amount=$this->calculate_cart_amount();
		$total_items=0;
		foreach($this->cart_items as $items)
		{
			foreach($items as $item)
			{
			$total_items+=($item->quantity);
			}
		}
		if($total_items%2 !=0)
		{
			$this->output['message']="Total items in your cart must be multiple of 2 to apply this coupon.";
			return json_encode($this->output);
		}
		
		$bogo_discount=$this->calculate_bogo_discount($total_items);
		
		$cart_amount-=$bogo_discount;
		$this->check_minimum_delivery_amount($cart_amount);
		$this->output['discount']=$bogo_discount;
		$this->output['cashback']=$this->cashback_value;  
		return json_encode($this->output);	
	}
	
	public function apply_code($cart_items,$type,$value,$cashback_value,$maximum_discount,$minimum_delivery_amount_after_discount)
	{
		$this->cart_items=json_decode($cart_items);
	   	$this->value=$value;
		$this->cashback_value=$cashback_value;
		$this->maximum_discount=$maximum_discount;
		$this->minimum_delivery_amount_after_discount=$minimum_delivery_amount_after_discount;
		
		switch($type)
		{
			case "Percentage":
			return($this->percentage());
			break;
			
			case "Discount":
			return($this->discount());
			break;
			
			case "Discount&Cashback":
			return($this->discount_cashback());
			break;
			
			case "Bogo":
			return($this->bogo());
			break;
			
			default:
			return json_encode($this->output);	
			break;
		}
	}
	
}

?>