<?php
/**
 * Dark side card (reference "Other ways in"): a title, short service list and the store's
 * verified contact details. Shared by the Contact and Order Tracking templates.
 *
 * Args: title, label (aria-label), perks (array of array{icon,text,url?}).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args ?? array(),
	array(
		'title' => __( 'Other ways in', 'scentral' ),
		'label' => __( 'Other ways to reach us', 'scentral' ),
		'perks' => array(),
	)
);

$phone    = scentral_option( 'contact_phone' );
$email    = scentral_option( 'contact_email' );
$hours    = scentral_option( 'contact_hours' );
$address  = scentral_option( 'contact_address' );
$whatsapp = preg_replace( '/[^0-9]/', '', (string) scentral_option( 'contact_whatsapp' ) );
?>
<aside class="cf__side" data-reveal aria-label="<?php echo esc_attr( $args['label'] ); ?>">
	<strong class="cf__side-title"><?php echo esc_html( $args['title'] ); ?></strong>
	<?php if ( $args['perks'] ) : ?>
		<ul class="cf__perks">
			<?php foreach ( $args['perks'] as $perk ) : ?>
				<li>
					<?php echo scentral_icon( $perk['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<?php if ( ! empty( $perk['url'] ) ) : ?>
						<a href="<?php echo esc_url( $perk['url'] ); ?>"><?php echo esc_html( $perk['text'] ); ?></a>
					<?php else : ?>
						<span><?php echo esc_html( $perk['text'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<dl class="cf__list">
		<?php if ( $email ) : ?>
			<div><dt><?php esc_html_e( 'Email', 'scentral' ); ?></dt><dd><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></dd></div>
		<?php endif; ?>
		<?php if ( $phone ) : ?>
			<div><dt><?php esc_html_e( 'Phone', 'scentral' ); ?></dt><dd><a href="<?php echo esc_attr( scentral_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></dd></div>
		<?php endif; ?>
		<?php if ( $whatsapp ) : ?>
			<div><dt><?php esc_html_e( 'WhatsApp', 'scentral' ); ?></dt><dd><a href="<?php echo esc_url( 'https://wa.me/' . $whatsapp ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Message us', 'scentral' ); ?></a></dd></div>
		<?php endif; ?>
		<?php if ( $hours ) : ?>
			<div><dt><?php esc_html_e( 'Hours', 'scentral' ); ?></dt><dd><?php echo esc_html( $hours ); ?></dd></div>
		<?php endif; ?>
		<?php if ( $address ) : ?>
			<div><dt><?php esc_html_e( 'Store', 'scentral' ); ?></dt><dd><?php echo esc_html( $address ); ?></dd></div>
		<?php endif; ?>
	</dl>
	<?php scentral_social_list( 'social-list cf__social' ); ?>
</aside>
