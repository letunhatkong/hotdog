<?php foreach ( $addon['options'] as $key => $option ) :
	$addon_key     = 'addon-' . sanitize_title( $addon['field-name'] );
	$option_key    = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
	$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ][ $option_key ] ) ? $_POST[ $addon_key ][ $option_key ] : '';
	$price = apply_filters( 'woocommerce_product_addons_option_price',
		$option['price'] > 0 ? '(' . wc_price( get_product_addon_price_for_display( $option['price'] ) ) . ')' : '',
		$option,
		$key,
		'input_multiplier'
	);
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
		<?php if ( ! empty( $option['label'] ) ) : ?>
			<label><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?></label>
		<?php endif; ?>
		<input type="number" step="" class="input-text addon addon-input_multiplier" data-raw-price="<?php echo esc_attr( $option['price'] ); ?>" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" name="<?php echo $addon_key ?>[<?php echo $option_key; ?>]" value="<?php echo ( esc_attr( $current_value ) == '' ? $option['min'] : esc_attr( $current_value ) ); ?>" <?php if ( ! empty( $option['min'] ) || $option['min'] === '0' ) echo 'min="' . $option['min'] .'"'; ?> <?php if ( ! empty( $option['max'] ) ) echo 'max="' . $option['max'] .'"'; ?> />
		<span class="addon-alert"><?php _e( 'This must be a number!', 'woocommerce-product-addons' ); ?></span>
	</p>

<?php endforeach; ?>
