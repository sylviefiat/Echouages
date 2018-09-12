<?php defined('_JEXEC') or die(); ?>

<form action="<?php echo $pg_data['system']['paymentURL'] ?>"  method="post" id="paymentForm">
<p align="center">
	<input type="hidden" name="cmd" value="<?php echo $pg_data['system']['cmd'] ?>" />
	<input type="hidden" name="business" value="<?php echo $pg_data['vendor']['merchant'] ?>" />
	<input type="hidden" name="return" value="<?php echo $pg_data['system']['success'] ?>" />
	<input type="hidden" name="cancel_return" value="<?php echo $pg_data['system']['cancel'] ?>" />
	<input type="hidden" name="notify_url" value="<?php echo $pg_data['system']['postback'] ?>" />
	<input type="hidden" name="custom" value="<?php echo $pg_data['order']['id'] ?>" />

	<input type="hidden" name="item_number" value="<?php echo $pg_data['order']['id'] ?>" />
	<input type="hidden" name="item_name" value="<?php echo $pg_data['order']['title']  ?>" />
	<input type="hidden" name="currency_code" value="<?php echo $pg_data['order']['currency'] ?>" />

	<input type="hidden" name="amount" value="<?php echo $pg_data['order']['subtotal'] ?>" />
	<input type="hidden" name="tax" value="<?php echo $pg_data['order']['tax'] ?>" />

	<input type="hidden" name="first_name" value="<?php echo $pg_data['client']['name'] ?>" />
	<input type="hidden" name="last_name" value="<?php echo $pg_data['client']['lastname'] ?>" />

	<input type="hidden" name="address_override" value="0">
	<input type="hidden" name="address1" value="<?php echo $pg_data['client']['address'] ?>">
	<input type="hidden" name="address2" value="">
	<input type="hidden" name="city" value="<?php echo $pg_data['client']['city'] ?>">
	<input type="hidden" name="state" value="<?php echo $pg_data['client']['state'] ?>">
	<input type="hidden" name="zip" value="<?php echo $pg_data['client']['zip'] ?>">
	<input type="hidden" name="country" value="<?php echo $pg_data['client']['country'] ?>">

	<?php /* Remove the following line if PayPal doing POST to your site causes a problem */ ?>
	<input type="hidden" name="rm" value="2">

	<input type="hidden" name="no_note" value="1" />
	<input type="hidden" name="no_shipping" value="1" />
	
	<?php if(!empty($pg_data['system']['cbt'])){ ?>
		<input type="hidden" name="cbt" value="<?php echo $pg_data['system']['cbt'] ?>" />
	<?php } ?>
	
	<?php if(!empty($pg_data['system']['cpp_header_image'])){ ?>
		<input type="hidden" name="cpp_header_image" value="<?php echo $pg_data['system']['cpp_header_image']?>" />
	<?php } ?>
	
	<?php if(!empty($pg_data['system']['cpp_headerback_color'])){ ?>
		<input type="hidden" name="cpp_headerback_color" value="<?php echo $pg_data['system']['cpp_headerback_color']?>" />
	<?php } ?>
	
	<?php if(!empty($pg_data['system']['cpp_headerborder_color'])){ ?>
		<input type="hidden" name="cpp_headerborder_color" value="<?php echo $pg_data['system']['cpp_headerborder_color']?>" />
	<?php } ?>

	<input type="image" style="width: 100px; height: auto;" src="http://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" id="paypalsubmit" />
	<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</p>
</form>