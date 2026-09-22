<?php
/**
 * Additional Briite social profile fields.
 *
 * Existing meta keys are intentionally preserved for backwards compatibility.
 *
 * @package kriate
 */

add_action( 'show_user_profile', 'social_profile_fields' );
add_action( 'edit_user_profile', 'social_profile_fields' );

/**
 * Render the existing social profile fields.
 *
 * @param WP_User $user User being edited.
 * @return void
 */
function social_profile_fields( $user ) {
	?>
	<h3><?php esc_html_e( 'Social Sites', 'kriate' ); ?></h3>

	<?php wp_nonce_field( 'kriate_save_social_profile', 'kriate_social_profile_nonce' ); ?>

	<table class="form-table" role="presentation">
		<tr>
			<th><label for="twitter"><?php esc_html_e( 'Twitter', 'kriate' ); ?></label></th>
			<td>
				<input type="text" class="regular-text" name="twitter" id="twitter" value="<?php echo esc_attr( get_user_meta( $user->ID, 'twitter', true ) ); ?>">
				<p class="description"><?php esc_html_e( 'Your Twitter username', 'kriate' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="facebook"><?php esc_html_e( 'Facebook', 'kriate' ); ?></label></th>
			<td>
				<input type="url" class="regular-text" name="facebook" id="facebook" value="<?php echo esc_attr( get_user_meta( $user->ID, 'facebook', true ) ); ?>">
				<p class="description"><?php esc_html_e( 'Your Facebook profile URL', 'kriate' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="linkedin"><?php esc_html_e( 'LinkedIn', 'kriate' ); ?></label></th>
			<td>
				<input type="url" class="regular-text" name="linkedin" id="linkedin" value="<?php echo esc_attr( get_user_meta( $user->ID, 'linkedin', true ) ); ?>">
				<p class="description"><?php esc_html_e( 'Your LinkedIn profile URL', 'kriate' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'personal_options_update', 'social_save_profile_fields' );
add_action( 'edit_user_profile_update', 'social_save_profile_fields' );

/**
 * Save social profile fields securely while retaining historical meta keys.
 *
 * @param int $user_id User ID being updated.
 * @return void
 */
function social_save_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	if ( ! isset( $_POST['kriate_social_profile_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['kriate_social_profile_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'kriate_save_social_profile' ) ) {
		return;
	}

	$fields = array(
		'twitter'  => 'sanitize_text_field',
		'facebook' => 'esc_url_raw',
		'linkedin' => 'esc_url_raw',
	);

	foreach ( $fields as $meta_key => $sanitize_callback ) {
		if ( ! isset( $_POST[ $meta_key ] ) ) {
			continue;
		}

		$value = call_user_func( $sanitize_callback, wp_unslash( $_POST[ $meta_key ] ) );

		if ( '' === $value ) {
			delete_user_meta( $user_id, $meta_key );
			continue;
		}

		update_user_meta( $user_id, $meta_key, $value );
	}
}
