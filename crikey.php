<?php
/*
 Plugin Name: WP E-Commerce Region Based Shipping Australia States
 Description: Region Based Shipping Options For WP E-Commerce
 Author: Dijitul Developments
 Author URI: http://www.dijituldevelopments.co.uk/
 Version: 0.1.2
*/

class crikey {

	var $internal_name;
	var $name;
	var $is_external;

	function crikey () {

		// An internal reference to the method - must be unique!
		$this->internal_name = "crikey";
		
		// $this->name is how the method will appear to end users
		$this->name = "Australian Delivery";

		// Set to FALSE - doesn't really do anything :)
		$this->is_external = FALSE;

		return true;
	}
	
	/* You must always supply this */
	function getName() {
		return $this->name;
	}
	
	/* You must always supply this */
	function getInternalName() {
		return $this->internal_name;
	}
	
	
	/* Use this function to return HTML for setting any configuration options for your shipping method
	 * This will appear in the WP E-Commerce admin area under Products > Settings > Shipping
         *
	 * Whatever you output here will be wrapped inside the right <form> tags, and also
	 * a <table> </table> block */

	function getForm() {

		$shipping = get_option('crikey_options');
		
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		Australian Capital Territory:<br/>';
		$output .= '		<input type="text" name="shipping[ACT]" value="'.htmlentities($shipping['ACT']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		New South Wales:<br/>';
		$output .= '		<input type="text" name="shipping[NSW]" value="'.htmlentities($shipping['NSW']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		Northern Territory:<br/>';
		$output .= '		<input type="text" name="shipping[NT]" value="'.htmlentities($shipping['NT']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		Queensland:<br/>';
		$output .= '		<input type="text" name="shipping[QLD]" value="'.htmlentities($shipping['QLD']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';		
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		South Australia:<br/>';
		$output .= '		<input type="text" name="shipping[SA]" value="'.htmlentities($shipping['SA']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		Tasmania:<br/>';
		$output .= '		<input type="text" name="shipping[TAS]" value="'.htmlentities($shipping['TAS']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		Victoria:<br/>';
		$output .= '		<input type="text" name="shipping[VIC]" value="'.htmlentities($shipping['VIC']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';		
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		Western Australia:<br/>';
		$output .= '		<input type="text" name="shipping[WA]" value="'.htmlentities($shipping['WA']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';				
		$output .= '<tr>';
		$output .= '	<td>';
		$output .= '		International:<br/>';
		$output .= '		<input type="text" name="shipping[international]" value="'.htmlentities($shipping['international']).'"><br/>';
		$output .= '	</td>';
		$output .= '</tr>';	

		return $output;
	}
	


	/* Use this function to store the settings submitted by the form above
	 * Submitted form data is in $_POST */

	function submit_form() {

		if($_POST['shipping'] != null) {

			$shipping = (array)get_option('crikey_options');
			$submitted_shipping = (array)$_POST['shipping'];

			update_option('crikey_options',array_merge($shipping, $submitted_shipping));

		}

		return true;

	}
	
	/* If there is a per-item shipping charge that applies irrespective of the chosen shipping method
         * then it should be calculated and returned here. The value returned from this function is used
         * as-is on the product pages. It is also included in the final cart & checkout figure along
         * with the results from GetQuote (below) */

	function get_item_shipping(&$cart_item) {

		global $wpdb;

		// If we're calculating a price based on a product, and that the store has shipping enabled

		$product_id = $cart_item->product_id;
		$quantity = $cart_item->quantity;
		$weight = $cart_item->weight;
		$unit_price = $cart_item->unit_price;

    		if (is_numeric($product_id) && (get_option('do_not_use_shipping') != 1)) {
			$region = $_SESSION['wpsc_selected_region'];
			$country_code = $_SESSION['wpsc_delivery_country'];

			// Get product information
      			$product_list = $wpdb->get_row("SELECT *
			                                  FROM `".WPSC_TABLE_PRODUCT_LIST."`
				                         WHERE `id`='{$product_id}'
			                                 LIMIT 1",ARRAY_A);
			/*
       			// If the item has shipping enabled
      			if($product_list['no_shipping'] == 0) {

        			if($country_code == get_option('base_country')) {

					// Pick up the price from "Local Shipping Fee" on the product form
          				$additional_shipping = $product_list['pnp'];

				} else {

					// Pick up the price from "International Shipping Fee" on the product form
          				$additional_shipping = $product_list['international_pnp'];

				}          

        			$shipping = $quantity * $additional_shipping;

			} else {
			*/
					//djb31st edited the rest out as we don't appear to use postage on the site
        			//if the item does not have shipping
        			$shipping = 0;

			//}

		} else {

      			//if the item is invalid or store is set not to use shipping
			$shipping = 0;

		}

    		return $shipping;	
	}
	


	/* This function returns an Array of possible shipping choices, and associated costs.
         * This is for the cart in general, per item charges (As returned from get_item_shipping (above))
         * will be added on as well. */

	function getQuote() {

		global $wpdb, $wpsc_cart;

		// This code is let here to show how you can access delivery address info
		// We don't use it for this skeleton shipping method

		if (isset($_POST['country'])) {

			$country = $_POST['country'];
			$_SESSION['wpsc_delivery_country'] = $country;

		} else {

			$country = $_SESSION['wpsc_delivery_country'];

		}
		
		//get county region for europe
		$region = $wpdb->get_row("SELECT continent
									  FROM `".WPSC_TABLE_CURRENCY_LIST."`
								 WHERE `isocode`='{$country}'
									 LIMIT 1",ARRAY_A);
		
		
		// Retrieve the options set by submit_form() above
		$crikey_rates = get_option('crikey_options');
	
		if($_SESSION['wpsc_delivery_region'])
			$region_sel = $_SESSION['wpsc_delivery_region'];
		elseif($_SESSION['wpsc_selected_region'])
			$region_sel = $_SESSION['wpsc_selected_region'];
	
		$region = $region['continent'];		
		
			if($region_sel=="1000")
				return array ("Australian Capital Territory" => (float) $crikey_rates['ACT']);
			elseif($region_sel=="1001")
				return array ("New South Wales" => (float) $crikey_rates['NSW']);
			elseif($region_sel=="1002")
				return array ("Northern Territory" => (float) $crikey_rates['NT']);		
			elseif($region_sel=="1003")			
				return array ("Queensland" => (float) $crikey_rates['QLD']);		
			elseif($region_sel=="1004")			
				return array ("South Australia" => (float) $crikey_rates['SA']);		
			elseif($region_sel=="1005")			
				return array ("Tasmania" => (float) $crikey_rates['TAS']);		
			elseif($region_sel=="1006")			
				return array ("Victoria" => (float) $crikey_rates['VIC']);		
			elseif($region_sel=="1007")			
				return array ("Western Australia" => (float) $crikey_rates['WA']);
		else
		{
			if($crikey_rates['international']!=0)
				return array ("International" => (float) $crikey_rates['international']);
		}
	}
} 

function crikey_add($wpsc_shipping_modules) {

	global $crikey;
	$crikey = new crikey();

	$wpsc_shipping_modules[$crikey->getInternalName()] = $crikey;

	return $wpsc_shipping_modules;
}
	
function crikey_install() {

   global $wpdb;

//update australia to have regions
// wp_wpsc_currency_list
//UPDATE  `wp_wpsc_currency_list` SET  `has_regions` =  '1' WHERE  `wp_wpsc_currency_list`.`country` ="Australia";
$wpdb->query("UPDATE  `". $wpdb->prefix ."wpsc_currency_list` SET  `has_regions` =  '1' WHERE  `". $wpdb->prefix ."wpsc_currency_list`.`country` ='Australia'");

//add regions
$wpdb->query("INSERT INTO  `". $wpdb->prefix ."wpsc_region_tax` (`id` ,`country_id` ,`name` ,`code` ,`tax`)
VALUES (
'1000' ,  '137',  'Australian Capital Territory',  'AC',  '0'
), (
'1001' ,  '137',  'New South Wales',  'NS',  '0'
)
, (
'1002' ,  '137',  'Northern Territory',  'NT',  '0'
)
, (
'1003' ,  '137',  'Queensland',  'QL',  '0'
)
, (
'1004' ,  '137',  'South Australia',  'SA',  '0'
)
, (
'1005' ,  '137',  'Tasmania',  'TM',  '0'
)
, (
'1006' ,  '137',  'Victoria',  'VI',  '0'
)
, (
'1007' ,  '137',  'Western Australia',  'WA',  '0'
)
;");

}
add_filter('wpsc_shipping_modules', 'crikey_add');
register_activation_hook(__FILE__,'crikey_install');

?>
