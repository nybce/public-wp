<?php
/**
 * Block Template.
 *
 * @file
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$block_id = 'resource-cards-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$cards = get_field( 'cards' );
if ( empty( $cards ) ) {
	return;
}
?>
<div class="resource-card-wrapper mb-24 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<?php
	foreach ( $cards as $card ) {
		$card_title  = $card['title'];
		$image       = $card['image'];
		$icon        = $card['icon'];
		$description = $card['description'];
		$lnk         = $card['link'];
		$more_info   = $card['more_info'];
		?>
	<a href="<?php echo esc_url( ! empty( $lnk ) ? $lnk['url'] : '#' ); ?>" class="resource-card">
		<?php if ( ! empty( $image ) ) { ?>
			<div class="resource-card-img">
				<?php NYBC_Helpers::picture( $image, '654x367' ); ?>
			</div>
		<?php } ?>
		<div class="border-top"></div>

		<div class="resource-card-desc">
			<div class="resource-card-title">
				<?php if ( ! empty( $icon ) ) { ?>
					<div class="resource-card-icon">
						<img src="<?php echo esc_url( NYBC_IMG_URI ); ?>/picker-icons/<?php echo esc_attr( $icon ); ?>.svg" alt="">
					</div>
				<?php } ?>
				<div class="h5 title fw-800"><?php echo esc_html( $card_title ); ?></div>
			</div>

			<div class="spacer-8"></div>

			<div class="text-lg text-20">
				<?php echo wp_kses_post( $description ); ?>
			</div>

			<div class="spacer-24"></div>

			<?php if ( ! empty( $more_info ) ) { ?>
				<div class="divider"></div>

				<div class="spacer-24"></div>

				<div class="resource-card-info">
					<?php
					foreach ( $more_info as $info ) {
						$info_title       = $info['title'];
						$info_description = $info['description '];
						$info_count       = $info['count'];
						?>
						<div class="resource-card-label">
							<div class="text text-20"><?php echo esc_html( $card_title ); ?></div>
							<?php if ( ! empty( $info_description ) ) { ?>
								<div class="spacer-4"></div>
								<div class="h5 title fw-800"><?php echo esc_html( $info_description ); ?></div>
							<?php } ?>
							<?php if ( ! empty( $info_count ) ) { ?>
								<div class="spacer-4"></div>
								<div class="text fw-500" style="color:#E30513"><?php echo esc_html( $info_count ); ?></div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</a>
	<?php } ?>
</div>

