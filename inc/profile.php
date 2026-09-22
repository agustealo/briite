<?php
/**
 * Historical Briite social-profile compatibility callbacks.
 *
 * Briite no longer registers user-profile fields or writes user metadata.
 * Existing twitter, facebook, and linkedin user-meta rows are intentionally
 * left untouched so upgrades do not destroy site data. The callback names are
 * retained as inert compatibility shims for child themes that test for them.
 *
 * @package kriate
 */

/**
 * Historical social-profile renderer retained as an inert compatibility shim.
 *
 * @param WP_User $user User being edited.
 * @return void
 */
function social_profile_fields( $user ) {
	unset( $user );
}

/**
 * Historical social-profile save callback retained as an inert compatibility shim.
 *
 * @param int $user_id User ID being updated.
 * @return void
 */
function social_save_profile_fields( $user_id ) {
	unset( $user_id );
}
