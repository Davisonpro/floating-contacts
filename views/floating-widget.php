<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://davisonpro.dev/floating-contacts
 * @since      2.0.0
 *
 * @package    DavisonPro\FloatingContacts
 */

use DavisonPro\FloatingContacts\FloatingContacts;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$options        = get_option( 'floating_contacts_options', array() );
$position_class = 'floating-contacts--' . ( $options['position'] ?? 'bottom-right' );
$bg_color       = esc_attr( $options['bg_color'] ?? '#1e88e5' );

// Check if at least one contact method is enabled.
$has_contacts = (
	( ! empty( $options['phone_enabled'] ) && ! empty( $options['phone_number'] ) ) ||
	( ! empty( $options['email_enabled'] ) && ! empty( $options['email_address'] ) ) ||
	( ! empty( $options['whatsapp_enabled'] ) && ! empty( $options['whatsapp_number'] ) ) ||
	! empty( $options['custom_links'] )
);

// If no contacts are set, don't display anything.
if ( ! $has_contacts ) {
	return;
}

$bg_color    = esc_attr( $options['bg_color'] ?? '#1e88e5' );
$hover_color = esc_attr( FloatingContacts::adjust_brightness( $bg_color, 20 ) );
?>

<div class="FloatingContacts <?php echo esc_attr( $position_class ); ?>" 
	style="--fc-bg-color: <?php echo $bg_color; ?>; --fc-hover-color: <?php echo $hover_color; ?>;" 
	aria-label="<?php esc_attr_e( 'Floating Contact Buttons', 'floating-contacts' ); ?>">

	<button type="button" class="FloatingContacts__button" aria-expanded="false" aria-controls="floating-contacts-list">
		<span class="FloatingContacts__button-close">
			<span data-icon="dots"><i></i><i></i><i></i></span>
		</span>
		<span class="FloatingContacts__button-icons" data-icons-number="<?php echo count( array_filter( array( $options['phone_enabled'], $options['email_enabled'], $options['whatsapp_enabled'] ) ) ) + count( $options['custom_links'] ?? array() ); ?>">
			<span data-icon="dots"><i></i><i></i><i></i></span>
			<?php if ( ! empty( $options['phone_enabled'] ) ) : ?>
				<span data-icon="phone"></span>
			<?php endif; ?>
			<?php if ( ! empty( $options['email_enabled'] ) ) : ?>
				<span data-icon="email"></span>
			<?php endif; ?>
			<?php if ( ! empty( $options['whatsapp_enabled'] ) ) : ?>
				<span data-icon="whatsapp"></span>
			<?php endif; ?>
			<?php
			if ( ! empty( $options['custom_links'] ) ) {
				foreach ( $options['custom_links'] as $link ) {
					echo '<span data-icon="' . esc_attr( $link['icon'] ) . '"></span>';
				}
			}
			?>
		</span>
	</button>
	
	<div id="floating-contacts-list" class="FloatingContacts__list" aria-label="<?php esc_attr_e( 'Contact Options', 'floating-contacts' ); ?>">
		<?php if ( ! empty( $options['phone_enabled'] ) && ! empty( $options['phone_number'] ) ) : ?>
			<a href="tel:<?php echo esc_attr( $options['phone_number'] ); ?>" class="FloatingContacts__list-item" data-icon="phone" rel="nofollow">
				<?php echo esc_html( $options['phone_number'] ); ?>
			</a>
		<?php endif; ?>

		<?php if ( ! empty( $options['email_enabled'] ) && ! empty( $options['email_address'] ) ) : ?>
			<a href="mailto:<?php echo esc_attr( $options['email_address'] ); ?>" class="FloatingContacts__list-item" data-icon="email" rel="nofollow">
				<?php esc_html_e( 'Email', 'floating-contacts' ); ?>
			</a>
		<?php endif; ?>

		<?php
		if ( ! empty( $options['whatsapp_enabled'] ) && ! empty( $options['whatsapp_number'] ) ) :
			$whatsapp_url = 'https://api.whatsapp.com/send/?phone=' . preg_replace( '/[^0-9]/', '', $options['whatsapp_number'] );
			if ( ! empty( $options['whatsapp_message'] ) ) {
				$whatsapp_url .= '&text=' . urlencode( $options['whatsapp_message'] );
			}
			$whatsapp_url .= '&app_absent=0';
			?>
			<a href="<?php echo esc_url( $whatsapp_url ); ?>" class="FloatingContacts__list-item" data-icon="whatsapp" rel="nofollow noopener" target="_blank">
				<?php esc_html_e( 'WhatsApp', 'floating-contacts' ); ?>
			</a>
		<?php endif; ?>

		<?php
		if ( ! empty( $options['custom_links'] ) ) {
			foreach ( $options['custom_links'] as $link ) {
		        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<a href="' . esc_url( $link['url'] ) . '" class="FloatingContacts__list-item" data-icon="' . esc_attr( $link['icon'] ) . '" rel="nofollow noopener" target="_blank">' . esc_html( $link['label'] ) . '</a>';
			}
		}
		?>
	</div>
</div>
