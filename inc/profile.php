<?php
add_action( 'show_user_profile', 'social_profile_fields' );
add_action( 'edit_user_profile', 'social_profile_fields' );

function social_profile_fields( $user ) { 
?>

   <h3>Social Sites</h3>

   <table class="form-table">

      <tr>
         <th><label for="twitter">Twitter</label></th>

         <td>
            <input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" /><br />
            <span class="description">Your Twitter Username</span>
         </td>
      </tr>

      <tr>
         <th><label for="twitter">Facebook</label></th>

         <td>
            <input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $user->ID ) ); ?>" /><br />
            <span class="description">Your Facebook Profile URL</span>
         </td>
      </tr>
      
      <tr>
         <th><label for="twitter">Linkedin</label></th>

         <td>
            <input type="text" name="linkedin" id="linkedin" value="<?php echo esc_attr( get_the_author_meta( 'linkedin', $user->ID ) ); ?>" /><br />
            <span class="description">Your Linkedin Profile URL</span>
         </td>
      </tr>

   </table>

<?php
}
add_action( 'personal_options_update', 'social_save_profile_fields' );
add_action( 'edit_user_profile_update', 'social_save_profile_fields' );

function social_save_profile_fields( $user_id ) {
   if ( !current_user_can( 'edit_user', $user_id ) )
      return false;

   update_user_meta( $user_id, 'twitter', $_POST['twitter'] );
   update_user_meta( $user_id, 'facebook', $_POST['facebook'] );
   update_user_meta( $user_id, 'linkedin', $_POST['linkedin'] );
}