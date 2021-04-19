<?php
/**
 * Plugin Name: 		WooCommerce Prefix Suffix
 * Description: 		Tämä lisäosa lisää mahdollisuuden lisätä tuotteen hinnalle etuliitteen ja päätteen.
 * Version: 			1.0.0
 * Requires at least: 	5.2
 * Requires PHP: 		7.2
 * Author: 				Toni Manninen
 * Text Domain: 		wc-prefix-suffix
 * Domain Path:       	/languages
 *
 * Licence: GPL v3 or later
 * https://www.gnu.org/licenses/gpl-3.0.html
 *
 *
 *
 * WooCommerce Prefix Suffix is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 
 * WooCommerce Prefix Suffix is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with WP Prefix Suffix. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 
 

 /**
 * Luodaan ja näytetään custom tekstikenttä tuotesivulla
 * @since 1.0.0
 */
function wcps_create_custom_field() {
	$args = array(
	'id' => 'custom_prefix_for_price',
	'label' => __( 'Etuliite hinnalle', 'wc-prefix-suffix' ),
	'class' => 'prefix-custom-field',
	'desc_tip' => true,
	'description' => __( 'Tämä teksti näkyy ennen hintaa tuote ja kategoria sivuilla.', 'wc-prefix-suffix' ),
	);
	woocommerce_wp_text_input( $args );
	
	$args = array(
	'id' => 'custom_suffix_for_price',
	'label' => __( 'Pääte hinnalle', 'wc-prefix-suffix' ),
	'class' => 'suffix-custom-field',
	'desc_tip' => true,
	'description' => __( 'Tämä teksti näkyy hinnan jälkeen tuote ja kategoria sivuilla.', 'wc-prefix-suffix' ),
	);
	woocommerce_wp_text_input( $args );
}

add_action( 'woocommerce_product_options_general_product_data', 'wcps_create_custom_field' );

/**
 * Tallennetaan kentän tieto tietokantaan.
 * @since 1.0.0
 */
function wcps_save_custom_field( $post_id ) {
 $product = wc_get_product( $post_id );
 $prefix = isset( $_POST['custom_prefix_for_price'] ) ? $_POST['custom_prefix_for_price'] : '';
 $suffix = isset( $_POST['custom_suffix_for_price']) ? $_POST['custom_suffix_for_price'] : '';
 $product->update_meta_data( 'custom_prefix_for_price', sanitize_text_field( $prefix ) );
 $product->update_meta_data( 'custom_suffix_for_price', sanitize_text_field( $suffix ) ); 
 $product->save();
}
add_action( 'woocommerce_process_product_meta', 'wcps_save_custom_field' );

/**
 * Lisätään etuliite ja pääte hintaan mukaan ja palautetaan hinta.
 * @since 1.0.0
 */
add_filter( 'woocommerce_get_price_html', 'wcps_add_price_suffix', 99, 4 );
function wcps_add_price_suffix( $price, $product ){
	
	global $post;
 // Tarkistetaan onko tuotteella olemassa etuliitettä tai päätettä
	 $product = wc_get_product( $post->ID );
	 $prefix = $product->get_meta( 'custom_prefix_for_price' );
	 $suffix = $product->get_meta( 'custom_suffix_for_price' );
	 if( $prefix || $suffix )  {
		$price = $prefix . ' ' . $price . ' ' . $suffix;
		return $price; // Palautetaan "Etuliite hinta pääte"
	}
	else {
		return $price; // Mikäli etuliitettä tai päätettä ei ole, palautetaan pelkkä hinta.
	}
}