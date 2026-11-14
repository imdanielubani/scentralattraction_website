<?php
/**
 * Shown on a policy/FAQ page that has no content yet. Never invents policy text.
 * Editors see a hint; visitors see an invitation to contact the store directly.
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$phone = scentral_option( 'contact_phone' );
$email = scentral_option( 'contact_email' );
?>
<div class="pending">
	<?php if ( current_user_can( 'edit_pages' ) ) : ?>
		<p class="pending__hint"><strong><?php esc_html_e( 'Editor note:', 'scentral' ); ?></strong> <?php esc_html_e( 'This page has no content yet. Add your text in the editor — use H2 headings for sections (they become the table of contents) or Details blocks for questions.', 'scentral' ); ?></p>
	<?php endif; ?>
	<p class="pending__title"><?php esc_html_e( 'This page is being updated.', 'scentral' ); ?></p>
	<p class="muted"><?php esc_html_e( 'For any question in the meantime, our team will be glad to help.', 'scentral' ); ?></p>
	<ul class="pending__contact">
		<?php if ( $phone ) : ?>
			<li><?php echo scentral_icon( 'phone' ); // phpcs:ignore ?><a href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
		<?php endif; ?>
		<?php if ( $email ) : ?>
			<li><?php echo scentral_icon( 'mail' ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
		<?php endif; ?>
	</ul>
</div>
