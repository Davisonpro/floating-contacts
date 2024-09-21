<?php
/**
 * Provide a refined admin area view for the plugin
 *
 * @link       https://davisonpro.dev/floating-contacts
 * @since      2.0.0
 *
 * @package    DavisonPro\FloatingContacts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$options      = get_option( 'floating_contacts_options', array() );
$custom_links = $options['custom_links'] ?? array();
?>

<div class="wrap floating-contacts-admin">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<?php settings_errors( 'floating_contacts_messages' ); ?>

	<form method="post" action="options.php" id="floating-contacts-settings-form">
		<?php
		settings_fields( 'floating_contacts_options' );
		do_settings_sections( 'floating_contacts' );
		?>
		
		<div class="fc-card">
			<h2 class="fc-card-title"><?php esc_html_e( 'Contact Methods', 'floating-contacts' ); ?></h2>
			
			<div class="fc-field-group">
				<label class="fc-checkbox-label" for="floating_contacts_phone_enabled">
					<input type="checkbox" id="floating_contacts_phone_enabled" name="floating_contacts_options[phone_enabled]" <?php checked( $options['phone_enabled'] ?? false ); ?> class="fc-checkbox">
					<span class="fc-checkbox-text"><?php esc_html_e( 'Enable Phone', 'floating-contacts' ); ?></span>
				</label>
				<input type="tel" id="floating_contacts_phone_number" name="floating_contacts_options[phone_number]" value="<?php echo esc_attr( $options['phone_number'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Phone Number', 'floating-contacts' ); ?>" class="fc-input">
			</div>

			<div class="fc-field-group">
				<label class="fc-checkbox-label" for="floating_contacts_email_enabled">
					<input type="checkbox" id="floating_contacts_email_enabled" name="floating_contacts_options[email_enabled]" <?php checked( $options['email_enabled'] ?? false ); ?> class="fc-checkbox">
					<span class="fc-checkbox-text"><?php esc_html_e( 'Enable Email', 'floating-contacts' ); ?></span>
				</label>
				<input type="email" id="floating_contacts_email_address" name="floating_contacts_options[email_address]" value="<?php echo esc_attr( $options['email_address'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Email Address', 'floating-contacts' ); ?>" class="fc-input">
			</div>

			<div class="fc-field-group">
				<label class="fc-checkbox-label" for="floating_contacts_whatsapp_enabled">
					<input type="checkbox" id="floating_contacts_whatsapp_enabled" name="floating_contacts_options[whatsapp_enabled]" <?php checked( $options['whatsapp_enabled'] ?? false ); ?> class="fc-checkbox">
					<span class="fc-checkbox-text"><?php esc_html_e( 'Enable WhatsApp', 'floating-contacts' ); ?></span>
				</label>
				<input type="text" id="floating_contacts_whatsapp_number" name="floating_contacts_options[whatsapp_number]" value="<?php echo esc_attr( $options['whatsapp_number'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'WhatsApp Number', 'floating-contacts' ); ?>" class="fc-input">
				<textarea id="floating_contacts_whatsapp_message" name="floating_contacts_options[whatsapp_message]" placeholder="<?php esc_attr_e( 'WhatsApp Message', 'floating-contacts' ); ?>" class="fc-textarea"><?php echo esc_textarea( $options['whatsapp_message'] ?? '' ); ?></textarea>
			</div>
		</div>

		<div class="fc-card">
			<h2 class="fc-card-title"><?php esc_html_e( 'Custom Links', 'floating-contacts' ); ?></h2>
			
			<div id="floating-contacts-custom-links">
				<?php
				if ( ! empty( $custom_links ) ) {
					foreach ( $custom_links as $index => $link ) {
						$this->render_custom_link_fields( $index, $link );
					}
				} else {
					$this->render_custom_link_fields( 0 );
				}
				?>
			</div>
			
			<button type="button" class="fc-button fc-button-secondary" id="add-custom-link"><?php esc_html_e( 'Add Custom Link', 'floating-contacts' ); ?></button>
		</div>

		<div class="fc-card">
			<h2 class="fc-card-title"><?php esc_html_e( 'Appearance', 'floating-contacts' ); ?></h2>
			
			<div class="fc-field-group">
				<label class="fc-label" for="floating_contacts_bg_color"><?php esc_html_e( 'Background Color', 'floating-contacts' ); ?></label>
				<input type="text" id="floating_contacts_bg_color" name="floating_contacts_options[bg_color]" value="<?php echo esc_attr( $options['bg_color'] ?? '#0073aa' ); ?>" class="floating-contacts-color-field">
			</div>
			<div class="fc-field-group">
				<label class="fc-label" for="floating_contacts_position"><?php esc_html_e( 'Position', 'floating-contacts' ); ?></label>
				<select id="floating_contacts_position" name="floating_contacts_options[position]" class="fc-select">
					<option value="bottom-right" <?php selected( $options['position'] ?? 'bottom-right', 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'floating-contacts' ); ?></option>
					<option value="bottom-left" <?php selected( $options['position'] ?? 'bottom-right', 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'floating-contacts' ); ?></option>
				</select>
			</div>
		</div>

		<?php submit_button( __( 'Save Settings', 'floating-contacts' ), 'fc-button fc-button-primary', 'submit', true, array( 'id' => 'fc-submit' ) ); ?>
	</form>
</div>

<script type="text/html" id="tmpl-floating-contacts-custom-link">
	<?php $this->render_custom_link_fields( '{{data.index}}' ); ?>
</script>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.floating-contacts-color-field').wpColorPicker({
			defaultColor: '#0073aa',
		});

		var customLinkIndex = <?php echo count( $custom_links ); ?>;

		$('#add-custom-link').on('click', function() {
			var template = wp.template('floating-contacts-custom-link');
			$('#floating-contacts-custom-links').append(template({ index: customLinkIndex }));
			customLinkIndex++;
		});

		$(document).on('click', '.remove-custom-link', function() {
			$(this).closest('.fc-custom-link-item').remove();
		});
	});
</script>
