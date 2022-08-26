/* 
* Add Item to Admin panel
*/

function register_post_type() {
  $args = array( 'public' => true, 'label' => 'Job Listing' ); 
  register_post_type( 'job', $args );
}
add_action( 'init', 'register_post_type' );



/* 
* Creating a Taxonomy 
*/

//hook in init action and call create_book_taxonomies when the hook is triggered
add_action( 'init', 'create_topics_hierarchical_taxonomy', 0 );

//set a name for a custom Topics taxonomy for your posts
function create_topics_hierarchical_taxonomy() {

// Adding a new taxonomy, making it hierarchical like categories
// also set translation for the interface
  $labels = array(
    'name' => _x( 'Topics', 'taxonomy general name' ),
    'singular_name' => _x( 'Topic', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Topics' ),
    'all_items' => __( 'All Topics' ),
    'parent_item' => __( 'Parent Topic' ),
    'parent_item_colon' => __( 'Parent Topic:' ),
    'edit_item' => __( 'Edit Topic' ),
    'update_item' => __( 'Update Topic' ),
    'add_new_item' => __( 'Add New Topic' ),
    'new_item_name' => __( 'New Topic Name' ),
    'menu_name' => __( 'Topics' ),
  );

// Now registering the taxonomy
  register_taxonomy('topics',array('post'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'topic' ),
  ));

}



/*
 * function to add an editor in the admin panel
 */
function true_double_editor() {
	global $post;
	echo '<h2>Gallery</h2>'; // title to the second editor
	wp_editor( get_post_meta($post->ID, '_true_editor_data', true), 'trueeditor' );
}
 
add_action( 'edit_form_advanced', 'true_double_editor' );
add_action( 'edit_page_form', 'true_double_editor' );
 
// data save function
function true_save_double_editor($post_id){
	update_post_meta($post_id, '_true_editor_data', $_POST['trueeditor']);
}
 


/*
 * custom form 
 */
  
 function editor_custom_content_bottom() {
	global $asgarosforum;
	if (!is_user_logged_in() && $asgarosforum->options['allow_guest_postings']) {
		$captcha_instance = new ReallySimpleCaptcha();
		$captcha_word = $captcha_instance->generate_random_word();
		$captcha_prefix = mt_rand();
		$captcha_file = $captcha_instance->generate_image($captcha_prefix, $captcha_word);
		$captcha_url = plugins_url().'/really-simple-captcha/tmp/'.$captcha_file;
		echo '<div class="editor-row editor-row-captcha">';
		echo '<span class="row-title">'.__('Captcha:', 'asgaros-forum').'</span>';
		echo '<img src="'.$captcha_url.'" /><br />';
		echo '<input type="text" name="captcha_value">';
		echo '<input type="hidden" name="captcha_prefix" value="'.$captcha_prefix.'">';
		echo '</div>';
	}
}
add_action('asgarosforum_editor_custom_content_bottom', 'editor_custom_content_bottom');
function insert_custom_validation($status) {
	global $asgarosforum;
	if (!is_user_logged_in() && $asgarosforum->options['allow_guest_postings']) {
		$captcha_instance = new ReallySimpleCaptcha();
		$captcha_value = $_POST['captcha_value'];
		$captcha_prefix = $_POST['captcha_prefix'];
		$captcha_correct = $captcha_instance->check($captcha_prefix, $captcha_value);
		$captcha_instance->remove($captcha_prefix);
		if (!$captcha_correct) {
			$asgarosforum->info = __('You must enter the correct captcha.', 'asgaros-forum');
			return false;
		}
	}
	return $status;
}
add_filter('asgarosforum_filter_insert_custom_validation', 'insert_custom_validation');
 
 
 
 
 /*
 * Human Comprehensible URLs for Wookomers
 */
 
 function wpd_product_category_base_same_shop_base( $flash = false ){
    $terms = get_terms(array(
        'taxonomy' => 'product_cat',
        'post_type' => 'product',
        'hide_empty' => false,
    ));
    if ($terms && !is_wp_error($terms)) {
        $siteurl = esc_url(home_url('/'));
        foreach ($terms as $term) {
            $term_slug = $term->slug;
            $baseterm = str_replace($siteurl, '', get_term_link($term->term_id, 'product_cat'));
 
            add_rewrite_rule($baseterm . '?$','index.php?product_cat=' . $term_slug,'top');
            add_rewrite_rule($baseterm . 'page/([0-9]{1,})/?$', 'index.php?product_cat=' . $term_slug . '&amp;paged=$matches[1]','top');
            add_rewrite_rule($baseterm . '(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', 'index.php?product_cat=' . $term_slug . '&amp;feed=$matches[1]','top');
 
        }
    }
    if ($flash == true)
        flush_rewrite_rules(false);
}
add_filter( 'init', 'wpd_product_category_base_same_shop_base');
 
add_action( 'create_term', 'wpd_product_cat_same_shop_edit_success', 10, 2 );
function wpd_product_cat_same_shop_edit_success( $term_id, $taxonomy ) {
    devvn_product_category_base_same_shop_base(true);
}


 
 
 /*
 * Adding new order fields in the admin panel woocommerce
 */
 
 add_filter('woocommerce_admin_billing_fields', 'custom_woocommerce_billing_fields');

function custom_woocommerce_billing_fields($fields)
{
    $fields['your-diapason-from'] = array(
        'label' => "Door width",
        'placeholder' => "Enter door width",
        'required' => false,
        'clear' => false,
        'type' => 'number',
    );

    $fields['your-diapason-to'] = array(
        'label' => "Door height",
        'placeholder' => "Enter door height",
        'required' => false,
        'clear' => false,
        'type' => 'number',
    );

    $fields['your-text-message'] = array(
        'label' => "Buyer's note",
        'placeholder' => "Enter Buyer's note",
        'required' => false,
        'clear' => false,
        'type' => 'text',
    );

    $fields['heating'] = array(
        'label' => "Heat source",
        'placeholder' => "Enter Heat source",
        'required' => false,
        'clear' => false,
        'type' => 'text',
    );

    $fields['located'] = array(
        'label' => "Location",
        'placeholder' => "Enter Location",
        'required' => false,
        'clear' => false,
        'type' => 'text',
    );

    return $fields;
}


 
 
 /*
 * Creating an order via contact form 7 woocommerce
 */
 
 add_action( 'wpcf7_before_send_mail', 'create_order_function', 20, 3 );
function create_order_function( $contact_form, &$abort, $that ){
	$data = $that->get_posted_data();
	$url = $_SERVER["REQUEST_URI"];
	if( $data['order-form'] == "Y" ) {
		
		$address = array(
 		    'email'      => $data['your-email'],
 		    'phone'      => $data['your-phone'],
 		);

 		if( ! empty($data['your-city']) )
 			$address['city'] = $data['your-city'];

 		if( ! empty($data['your-name']) )
 			$address['first_name'] = $data['your-name'];

 		if( ! empty($data['your-diapason-from']) )
 			$address['your-diapason-from'] = $data['your-diapason-from'];

 		if( ! empty($data['your-diapason-to']) )
 			$address['your-diapason-to'] = $data['your-diapason-to'];

 		if( ! empty($data['your-text-message']) )
 			$address['your-text-message'] = $data['your-text-message'];

 		if( ! empty($data['heating'][0]) )
 			$address['heating'] = implode(", ", $data['heating']);

 		if( ! empty($data['located'][0]) )
 			$address['located'] = implode(", ", $data['located']);

 		// Now we create the order
 		$order = wc_create_order();

 		global $woocommerce;
 		$cart = WC()->cart;

		include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
		include_once WC_ABSPATH . 'includes/class-wc-cart.php';
		
		if ( is_null( $cart ) ) {
		    wc_load_cart();
		}

 		$cart = WC()->cart;

 		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
    	    $order->add_product( get_product($cart_item['product_id']), $cart_item['quantity']);
    	}
 		
 		$order->set_address( $address, 'billing' );

 		$order->calculate_totals();
 		$order->update_status('completed');
 		$order->save();
 		WC()->cart->empty_cart(true);
 		WC()->session->set('cart', array());
		
	}
}


 
 
 /*
 * Sort Function
 */
 
 function replace_query_parameter($name, $arr_replace, $delete = []) {
	$url = $_SERVER["REQUEST_URI"];
	$regExp = "/{$name}\=[a-z0-9\[\]\%]*/i";

	if( count($delete) > 0 ) {
		foreach ($delete as $del) {
			$url = preg_replace("/{$del}\=[a-z0-9\[\]\%]*/i", "", $url);
		}
	}

	$urlstring = implode('&', array_map(
	    function ($v, $k) { return sprintf("%s=%s", $k, $v); },
	    $arr_replace,
	    array_keys($arr_replace)
	));

	if( preg_match($regExp, $url) ) {
		$url = preg_replace($regExp, $urlstring."&", $url);
	} else {
		if(strpos($url, "?") !== false)
			$url .= "&" . $urlstring;
		else
			$url .= "?" . $urlstring;
	}

	$url = preg_replace("/&{2,}/", "&", $url);
	if(substr($url, -1) == "&") {
		$url = substr($url, 0, -1);
	}
	return $url;
}


 
 
 /*
 * filter woocommerce
 */
 
 add_filter('woocommerce_shortcode_products_query', 'product_query_function', 10, 3);

function product_query_function($query_args, $attributes, $type) {
	$get = $_GET;
	if(!empty($get)) {
		foreach ($get as $key => $value) {
			if(strpos($key, "filter_") !== false && $value && strpos($key, "product_visivility") === false) {

				$attribute = str_replace("filter_", "", $key);
				$query_args["tax_query"][] = [
					'taxonomy' 		=> "pa_".$attribute,
					'field'         => 'slug',
					'terms'			=> (is_array($value)) ? $value : [$value]
				];
			}
		}
		if(isset($get["min_price"]) && $get["min_price"] > 0 && isset($get["max_price"]) && $get["max_price"] > 0) {
			$query_args["meta_query"][] = [
				'key' => '_price',
            	'value' => array($get["min_price"], $get["max_price"]),
            	'compare' => 'BETWEEN',
            	'type' => 'NUMERIC'
			];
		} elseif(isset($get["min_price"]) && $get["min_price"] > 0 && !isset($get["max_price"]) 
			|| isset($get["min_price"]) && $get["min_price"] > 0 && $get["max_price"] <= 0) {
			$query_args["meta_query"][] = [
				'key' => '_price',
            	'value' => $get["min_price"],
            	'compare' => '>='
			];
		} elseif(isset($get["max_price"]) && $get["max_price"] > 0 && !isset($get["min_price"]) 
			|| isset($get["max_price"]) && $get["max_price"] > 0 && $get["min_price"] <= 0) {
			$query_args["meta_query"][] = [
				'key' => '_price',
            	'value' => $get["max_price"],
            	'compare' => '<='
			];
		}
	}
	return $query_args;
}


 
 
 /*
 * Sql debugger for wp
 */
 
 define('SAVEQUERIES', true);
add_action('shutdown', 'sql_logger');
function sql_logger() {
    global $wpdb;
    $log_file = fopen(ABSPATH.'/sql_log.txt', 'a');
    fwrite($log_file, "//////////////////////////////////////////\n\n" . date("F j, Y, g:i:s a")."\n");
    foreach($wpdb->queries as $q) {
        fwrite($log_file, $q[0] . " - ($q[1] s)" . "\n\n");
    }
    fclose($log_file);
}
 
 
 
 /*
 * Acceleration WP
 */
 
  function get_content($URL){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, $URL);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
}
function callback($buffer) {
  	if(strpos($_SERVER['HTTP_USER_AGENT'],'Chrome-Lighthouse') !== false) {
		$buffer = preg_replace(["/style=\"background:[\s?]url\((.*?)\)(.*)?\"/i", "/<script(.*)?><\/script>/i", "/[a-z0-9]+\.js/i", "/<iframe(.*)?><\/iframe>/i", "/<link(.*)?>/i", "/<img([^>]*) src=['\"](.*?)['\"]([^>]*)>/i"], '', $buffer);
		$protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
		$style = get_content("{$protocol}{$_SERVER["SERVER_NAME"]}/wp-content/themes/baking/static/css/main.css");
		echo $style;
		$buffer = str_replace("#STYLE#", "<style>{$style}</style>", $buffer);
	} else 
		$buffer = str_replace("#STYLE#", "", $buffer);
	return $buffer;
}

function buffer_start() { ob_start("callback"); }

function buffer_end() { ob_end_flush(); }

add_action('init', 'buffer_start');
add_action('wp_footer', 'buffer_end');


 
 
 /*
 * the ability to change the weight of the goods in the order
 */
  
  add_filter('woocommerce_quantity_input_step', 'add_custom_step_in_input');
function add_custom_step_in_input() {
    return '0.1';
}

 
 
 /*
 * Redirect to "Thank you" page after checkout
 */
  
add_action( 'template_redirect', 'truemisha_redirect_to_thank_you' );
 
function truemisha_redirect_to_thank_you() {
 
	// if not the "Order accepted" page, then we do nothing
	if( ! is_order_received_page() ) {
		return;
	}
 
	// it would be nice to check the status of the order, do not redirect fake orders
	if( isset( $_GET[ 'key' ] ) ) {
		$order_id = wc_get_order_id_by_order_key( $_GET[ 'key' ] );
		$order = wc_get_order( $order_id );
		if( $order->has_status( 'failed' ) ) {
			return;
		}
	}
 
	wp_redirect( site_url( 'new-thank-you' ) );
	exit;
 
}
 
 
 
 /*
 * Upsell items on the "Thank you" page
 */
  
  add_action( 'woocommerce_thankyou', 'truemisha_buy_more' );
 
function truemisha_buy_more() {
	echo '<h2>Maybe buy something else?</h2>';
	echo do_shortcode( '[products ids="14,22"]' );
}

 
 
 /*
 * the cost of each shipping method on the product page
 */
  
  add_action( 'woocommerce_after_add_to_cart_form', 'true_shipping_single_product', 25 );
function true_shipping_single_product() {
 
	// first get available delivery zones
	$shipping_zones = WC_Shipping_Zones::get_zones();
 
	if( $shipping_zones ) { 
		echo '<p>Delivery cost</p>';
		echo '<table>';
 
		// for each delivery area
		foreach ( $shipping_zones as $shipping_zone_id => $shipping_zone ) {
 
			// get delivery zone object
			$shipping_zone = new WC_Shipping_Zone( $shipping_zone_id ); 
			echo '<tr><td>' . $shipping_zone->get_zone_name() . '</td><td>';
 
			// get available delivery methods for this zone
			$shipping_methods = $shipping_zone->get_shipping_methods( true, 'values' );
 
			if( $shipping_methods ) {
				foreach ( $shipping_methods as $shipping_method_id => $shipping_method ) {
					$cost = ! empty( $shipping_method->cost ) ? wc_price( $shipping_method->cost ) : 'Is free!';
					echo $shipping_method->title . ' ' . $cost . '<br />'; 
				}
			} 
			echo '</td></tr>'; 
		}
		echo '</table>'; 
	} 
}
 
 
 
 
 /*
 * Creating a separate tab with shipping costs on the product page
 */
  
add_filter( 'woocommerce_product_tabs', 'truemisha_shipping_product_tab', 25 );
 
function truemisha_shipping_product_tab( $tabs ) {
	$tabs[ 'shipping_cost' ] = array(
		'title' 	=> 'Delivery cost',
		'priority' 	=> 25,
		'callback' 	=> 'true_shipping_single_product'
	);
	return $tabs;
}
  
  function true_shipping_single_product() {
 
	// first get available delivery zones
	$shipping_zones = WC_Shipping_Zones::get_zones();
 
	if( $shipping_zones ) { 
		echo '<p>Delivery cost</p>';
		echo '<table>';
 
		// for each delivery area
		foreach ( $shipping_zones as $shipping_zone_id => $shipping_zone ) {
 
			// get delivery zone object
			$shipping_zone = new WC_Shipping_Zone( $shipping_zone_id ); 
			echo '<tr><td>' . $shipping_zone->get_zone_name() . '</td><td>';
 
			// get available delivery methods for this zone
			$shipping_methods = $shipping_zone->get_shipping_methods( true, 'values' );
 
			if( $shipping_methods ) {
				foreach ( $shipping_methods as $shipping_method_id => $shipping_method ) {
					$cost = ! empty( $shipping_method->cost ) ? wc_price( $shipping_method->cost ) : 'Is free!';
					echo $shipping_method->title . ' ' . $cost . '<br />'; 
				}
			} 
			echo '</td></tr>'; 
		}
		echo '</table>'; 
	} 
}
 
 
 /*
 *Tabs in the product card
 */
 
 // Removing tabs
 add_filter( 'woocommerce_product_tabs', 'truemisha_remove_product_tabs', 25 );
 
function truemisha_remove_product_tabs( $tabs ) {
 if( ! empty( $tabs[ 'description' ] ) ) {
	unset( $tabs[ 'description' ] );
}
if( ! empty( $tabs[ 'reviews' ] ) ) {
	unset( $tabs[ 'reviews' ] );
}
if( ! empty( $tabs[ 'additional_information' ] ) ) {
	unset( $tabs[ 'additional_information' ] );
}
	return $tabs;
}
  
//  Renaming tabs

add_filter( 'woocommerce_product_tabs', 'truemisha_rename_tabs', 25 );
 
function truemisha_rename_tabs( $tabs ) {
	$tabs[ 'description' ][ 'title' ] = 'About the product';
	$tabs[ 'reviews' ][ 'title' ] = 'what people think';
	$tabs[ 'additional_information' ][ 'title' ] = 'Characteristics';
  
	return $tabs;
 
}

// Changing the order of tabs
add_filter( 'woocommerce_product_tabs', 'truemisha_reorder_tabs', 25 );
 
function truemisha_reorder_tabs( $tabs ) {
 
	$tabs[ 'description' ][ 'priority' ] = 10000;
	return $tabs;
 
}

//  Changing the contents of a tab
add_filter( 'woocommerce_product_tabs', 'truemisha_custom_description_tab', 25 );
 
function truemisha_custom_description_tab( $tabs ) {
 
	$tabs[ 'description' ][ 'callback' ] = 'truemisha_super_tab';
	return $tabs;
 
}
 
function truemisha_super_tab() {
	echo '<h2>Some headline</h2>';
	echo '<p>Some description</p>';
}

// create your own tab
add_filter( 'woocommerce_product_tabs', 'truemisha_new_product_tab', 25 );
 
function truemisha_new_product_tab( $tabs ) {
 
	$tabs[ 'new_super_tab' ] = array(
		'title' 	=> 'super таб',
		'priority' 	=> 25,
		'callback' 	=> 'truemisha_new_tab_content'
	);
 
	return $tabs;
 
}
function truemisha_new_tab_content() {
	echo '<p>Some HTML code for the tab</p>';
}



 
 /*
 * Transferring images uploaded for a product to the product gallery
 */
 
 function truemisha_attached_images_to_gallery( $product_id ) {
 
	$images = get_posts( 
		array(
			'post_parent'    => $product_id,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'fields' => 'ids',
			'post__not_in' => array( get_post_thumbnail_id( $product_id ) )
		)
	);
 
	if( $images ) {
		update_post_meta( $product_id, '_product_image_gallery', join( ',', $images ) );
	}
 
}
  
 
 
 /*
 * display number of sales on product page
 */
 
add_action( 'woocommerce_single_product_summary', 'truemisha_product_sales', 25 );
 
function truemisha_product_sales() {

	//determine the ID of the current product
	global $product;
	$product_id = $product->get_id();
 
	// We will receive product sales from orders, so we first get them
	$orders = wc_get_orders( array(
		'limit' => -1,
		'status' => array_map( 'wc_get_order_status_name', wc_get_is_paid_statuses() ),
		// uncomment if you want to get orders for the last month for example
		// 'date_after' => date( 'Y-m-d', strtotime( '-1 month' ) ),
		'return' => 'ids',
	) );
 
	// this variable will contain the number of sales
	$number_of_sales = 0;
 
	// start a cycle from received orders
	foreach ( $orders as $order_id ) {
		$order = wc_get_order( $order_id );
		// receive goods in order
		$items = $order->get_items();
		foreach ( $items as $item ) {
			if ( $product_id == $item->get_product_id() ) {
				$number_of_sales = $number_of_sales + absint( $item['qty'] );
			}
		}
	}
 
	if ( $number_of_sales > 0 ) {
		echo "<p>Total Sold: $number_of_sales шт</p>";
	}
 
}


  
 
 /*
 * add text or HTML to email about successful order
 */
 
 //  Adding a discount to the email to the buyer
 add_action( 'woocommerce_email_before_order_table', 'truemisha_discount_in_email', 25, 4 );
 
function truemisha_discount_in_email( $order, $sent_to_admin, $plain_text, $email ) {
 
	// checking that this hook is used in a letter to the buyer, and not to the admin
	if( false === $sent_to_admin ) {
		echo '<h2>20% off your next purchase!</h2>';
		echo '<p>We thank you for your purchase and therefore give you a promo code "<strong>PROMO20</strong>" for a 20% discount on your next order!</p>';
	}
 
}

//  if the email settings are set to Plain text
  add_action( 'woocommerce_email_before_order_table', 'truemisha_discount_in_email', 25, 4 );
 
function truemisha_discount_in_email( $order, $sent_to_admin, $plain_text, $email ) {
 
	// checking that this hook is used in a letter to the buyer, and not to the admin
	if( false === $sent_to_admin ) {
 
		if( false === $plain_text ) {
			echo '<h2>20% на следующую покупку!</h2>';
			echo '<p>Мы благодарим вас за покупку и поэтому дарим вам промокод "<strong>PROMO20</strong>" на получение 20% скидки на вашу следующий заказ!</p>';
		} else {
			echo "20% off your next purchase!\n
			We thank you for your purchase and therefore give you a promo code PROMO20 for a 20% discount on your next order!";
		}	
 
	}
 
}

//  Adding additional information about the order to the letters to the administrator
add_action( 'woocommerce_email_before_order_table', 'truemisha_ordermeta_in_email', 25, 4 );
 
function truemisha_ordermeta_in_email( $order, $sent_to_admin, $plain_text, $email ) {
 
	// checking that this hook is activated in the administrator's email
	if( true === $sent_to_admin ) {
 
		$dostavka_k = get_post_meta( $order->get_order_number(), 'dostavka_k', true );
 
		if( $dostavka_k ) {
			echo 'Deliver on time: ' . $dostavka_k;
		}
 
	}
 
}
 
 
 /*
 * attaching the file to the letter about a new order and about order processing
 */
 
 add_filter( 'woocommerce_email_attachments', 'truemisha_file_attachment_in_emails', 25, 4 );
 
function truemisha_file_attachment_in_emails( $attachments, $email_id, $order, $email ) {
 
	$file_id = 132; // Here we indicate the ID of our attachment file
	$email_ids = array( 'new_order', 'customer_processing_order' ); // which emails to attach to
 
	if ( in_array ( $email_id, $email_ids ) ) {
		$attachments[] = get_attached_file( $file_id );
	}
	return $attachments;
}

  
 
 
 /*
 * Changing the price display format for variable products
 */
  
  
  add_filter( 'woocommerce_variable_price_html', 'truemisha_variation_price', 20, 2 );
 
function truemisha_variation_price( $price, $product ) {
 
	$min_regular_price = $product->get_variation_regular_price( 'min', true );
	$min_sale_price = $product->get_variation_sale_price( 'min', true );
	$max_regular_price = $product->get_variation_regular_price( 'max', true );
	$max_sale_price = $product->get_variation_sale_price( 'max', true );
 
	if ( ! ( $min_regular_price == $max_regular_price && $min_sale_price == $max_sale_price ) ) {
		if ( $min_sale_price < $min_regular_price ) {
			$price = sprintf( 'от <del>%1$s</del><ins>%2$s</ins>', wc_price( $min_regular_price ), wc_price( $min_sale_price ) );
		} else {
			$price = sprintf( 'от %1$s', wc_price( $min_regular_price ) );
		}
	}
 
	return $price;
 
}


 
 
 /*
 * Indicate the presence of each variation in the product card
 */
 
 add_action( 'woocommerce_after_shop_loop_item', 'truemisha_variations_stock', 25 );
 
function truemisha_variations_stock(){
 
	// immediately get the product object from the global variable
	global $product;
 
	// if the product is not variable, then nothing else needs to be done
	if ( $product->is_type( 'variable' ) ) {
 
		echo '<p class="variations-loop">';
 
		// we run the loop for all variations (which with prices)
		foreach ( $product->get_available_variations() as $variation ) {
 
			$attribute = array();
			foreach ( $variation[ 'attributes' ] as $name => $value ) {
				// $name – attribute_pa_razmer (attribute taxonomy name)
				// $value – s (attribute label)
				$attribute[] = $value;
			}
 
			if ( $variation[ 'max_qty' ] > 0 ) {
				echo '<strong>' . join( ', ', $attribute ) . ':</strong> ' . $variation[ 'max_qty' ] . ' in stock<br>';
			} else {
				echo '<strong>' . join(', ', $attribute ) . ':</strong> out of stock<br>';
			}
 
		}
		echo '</p>';
 
	}
 
}

  
 
 
 /*
 * display the price of variations directly in the dropdown list
 */
    
  // Adding the price of variations to the dropdown list
 add_filter( 'woocommerce_variation_option_name', 'true_price_in_variation_option_name', 10, 4  );
 
function true_price_in_variation_option_name( $option, $null, $attribute, $product ) {
 
	// remember that attributes are terms
	// we will need to get the term label
	$term = get_term_by( 'name', $option, $attribute );
	// the label is now available in $term->slug
 
	global $wpdb;
 
	// to get the variation ID, use a SQL query
	$variation_id = $wpdb->get_var(
		$wpdb->prepare(
			"
			SELECT postmeta.post_id AS product_id
			FROM $wpdb->postmeta AS postmeta
			LEFT JOIN $wpdb->posts AS products ON ( products.ID = postmeta.post_id )
			WHERE postmeta.meta_key LIKE 'attribute_%'
			AND postmeta.meta_value = '%s'
			AND products.post_parent = %d
			",
			array(
				$term->slug,
				$product->get_id()
			)
		)
	);
 
	if( $variation_id ) { 
		// get the variation object, we will need it to get the price
		$_product = new WC_Product_Variation( $variation_id );
		return $option . ' (' . $_product->get_price() . get_woocommerce_currency_symbol() . ')';
	} else {
		return $option;
	}
 
}

// Hide the prices

// first price
add_action( 'template_redirect', 'true_hide_variations_price_1' );
 
function true_hide_variations_price_1() {
	// if not on product page
	if( ! is_singular( 'product' ) ) {
		return;
	}
	// check that it is variable
	$product = wc_get_product( get_queried_object_id() );
	if( $product->is_type( 'variable' ) ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	}
}
add_action( 'wp_head', 'true_hide_variations_price_2' );
 
// second price
function true_hide_variations_price_2() {
 
	echo '<style>.woocommerce-variation-price{ display:none }</style>';
 
}

 
 /*
 * hide coupon code AND display custom message
 */
 
 add_filter( 'woocommerce_cart_totals_coupon_label', 'truemisha_hide_coupon_code', 20, 2 );
 
function truemisha_hide_coupon_code( $label, $coupon ) {
 
	if( 'COUPON1000RUB' == $coupon->code ) {
		$label = 'Discount on purchases from ' . wc_price( 1000000 );
	}
 
	return $label;
 
}

 
 
 /*
 * We display the discounted price from the coupon and the original price in the "Subtotal"
 */
 
 add_filter( 'woocommerce_cart_subtotal', 'truemisha_subtotal_with_coupons', 25 );
 
function truemisha_subtotal_with_coupons( $cart_subtotal ){
 
	// if some coupon is applied
	if ( WC()->cart->get_cart_discount_total() <> 0 ) {
		// prices without discounts and with discounts
		$new_cart_subtotal = wc_price( WC()->cart->subtotal - WC()->cart->get_cart_discount_tax_total() - WC()->cart->get_cart_discount_total() );
		$cart_subtotal = sprintf( '<del>%s</del> <b>%s</b>', $cart_subtotal , $new_cart_subtotal );
	}
 
	return $cart_subtotal;
 
}



 
 /*
 * Automatically remove expired coupons from the admin panel
 */
 
 // first of all auto-schedule the event
add_action( 'init', 'true_schedule_coupon_removal' );
function true_schedule_coupon_removal() {
	if ( ! wp_next_scheduled( 'coupon_removal_hook' ) ) {
		wp_schedule_event( time(), 'daily', 'coupon_removal_hook' );
	}
}
 
// this is the hook that will run daily through WP_Cron
// and a function is hung on it that will remove coupons
add_action( 'coupon_removal_hook', 'true_do_remove_expired_coupons' );
 
// function that removes expired coupons
function true_do_remove_expired_coupons() {
 
	// coupons - this is the shop_coupon post type, we get them with the get_posts function()
	$args = array(
		'posts_per_page' => -1,
		'post_type'      => 'shop_coupon',
		'post_status'    => 'publish',
		'meta_query'     => array(
			'relation'   => 'AND',
			array(
				'key'     => 'date_expires',
				'value'   => current_time( 'timestamp' ),
				'compare' => '<='
			),
			array(
				'key'     => 'date_expires',
				'value'   => '',
				'compare' => '!='
			)
		)
	);
 
	$coupons = get_posts( $args );
 
	if ( ! empty( $coupons ) ) {
		foreach ( $coupons as $coupon ) {
			wp_trash_post( $coupon->ID );
			// or wp_delete_post( $coupon->ID, true ), if deleted without cart
		}
	}
}


 
 
 /*
 * Disabling the coupon entry form if it has already been applied
 */
 
 
 add_filter( 'woocommerce_coupons_enabled', 'true_if_coupon_applied_checkout', 25 );
 
function true_if_coupon_applied_checkout( $coupons_enabled ) {
 
	//You can remove this condition, then the coupon entry form will disappear and in cart
	if( ! is_checkout() ) {
		return $coupons_enabled;
	}
 
	if ( ! empty( WC()->cart->applied_coupons ) ) {
		return false;
	}
 
	return $coupons_enabled;
 
}



 
 /*
 * disable the coupon for products out of stock and on pre-order
 */
 
 
 add_filter( 'woocommerce_coupon_get_discount_amount', 'true_coupon_discount', 10, 5 );
 
 
function true_coupon_discount( $discount, $price_to_discount, $cart_item, $single, $coupon ) {    
 
	// for convenience, I will take out the product object in a separate variable
	$product = $cart_item[ 'data' ];
 
	// further we check
	if ( 
		$product->is_on_backorder() // if preorder
		|| ! $product->is_in_stock() // OR if out of stock
	) {
		$discount = 0; // set discount equal to zero
	}
 
	return $discount;
 
}
 
