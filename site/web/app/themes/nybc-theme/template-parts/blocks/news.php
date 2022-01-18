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

$block_id = 'news-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}
$posts_type = get_field( 'post_type' );

$curr_page = get_query_var( 'paged' );
$curr_page = $curr_page ? $curr_page : 1;
$args      = array(
	'paged'     => $curr_page,
	'post_type' => $posts_type ? $posts_type : 'post',
);

$bydate = '';

if ( isset( $_GET['bydate'] ) && ! empty( $_GET['bydate'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'filter' ) ) {
	$bydate = sanitize_text_field( wp_unslash( $_GET['bydate'] ) );
	$date   = explode( '/', $bydate );
	if ( 1 === count( $date ) ) {
		array_unshift( $date, '01' );
	}
	$start              = "{$date[1]}-{$date[0]}-01";
	$end                = gmdate( 'Y-m-t', strtotime( $start ) );
	$args['date_query'] = array(
		array(
			'after'     => $start,
			'before'    => $end,
			'inclusive' => true,
		),
	);
}
$selected_terms = '';
if ( isset( $_GET['terms'] ) && ! empty( $_GET['terms'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'filter' ) ) {
	$selected_terms = sanitize_text_field( wp_unslash( $_GET['terms'] ) );
	$terms          = explode( ',', $selected_terms );
	// @codingStandardsIgnoreStart
	if ( ! empty( $terms ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'terms'    => $terms,
			),
		);
	}
	// @codingStandardsIgnoreEnd
}
$query = new WP_Query( $args );

?>
<div class="<?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="filters-wrapper mobile-none">
		<div class="select-item-total"><?php esc_html_e( 'Showing', 'nybc' ); ?>
			<span><?php echo esc_html( $query->found_posts ); ?></span> <?php esc_html_e( 'Results', 'nybc' ); ?></div>

		<div class="select-item ml-auto">
			<div class="calendar">
				<div class="date_pick">
					<div class="input-calendar">
						<?php if ( empty( $bydate ) ) { ?>
							<p><?php esc_html_e( 'Filter By Date', 'nybc' ); ?></p>
						<?php } else { ?>
							<p><?php echo esc_html( $bydate ); ?></p>
						<?php } ?>
						<img src="<?php echo esc_url( NYBC_IMG_URI ); ?>/icons/calendar.svg" alt="">
					</div>
					<form>
						<?php wp_nonce_field( 'filter', 'nonce' ); ?>
						<input type="hidden" name="terms" value="<?php echo esc_attr( $selected_terms ); ?>">
						<div class="date_selector">
						<div class="years">
							<a class="picker-left picker-select-arrow"></a>
							<input class="year-selected" maxlength="4" name="bydate" readonly>
							<a class="picker-right picker-select-arrow"></a>
						</div>
						<div class="months">
						</div>
					</div>
					</form>
					<script>
						jQuery(function ($) {
							$('.months').on("click", '.month', function (e) {
								var btn = $(this);
								setTimeout(function (){ btn.closest('form').submit();}, 200);
							});
						});
					</script>
				</div>
			</div>
		</div>
	</div>

	<div class="spacer-24"></div>

	<div class="news-wrapper">
		<?php
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$lnk         = get_the_permalink();
				$image_url   = get_the_post_thumbnail_url( null, '519x292' );
				$block_title = get_the_title();
				$date        = get_the_time( 'M j, Y' );
				$tags        = get_the_tags();

				$image = array(
					'url' => $image_url,
					'alt' => $block_title,
				);

				?>
				<a href="<?php echo esc_url( $lnk ); ?>" class="news-item">
					<?php if ( ! empty( $image_url ) ) { ?>
					<div class="news-img">
						<?php NYBC_Helpers::picture( $image ); ?>
					</div>
					<?php } ?>
					<div class="border-top"></div>

					<div class="news-info">
						<?php
						if ( ! empty( $tags ) ) {
							?>
							<ul class="tags-list">
								<?php foreach ( $tags as $tg ) { ?>
									<li><?php echo esc_html( $tg->name ); ?></li>
								<?php } ?>
							</ul>
						<?php } ?>

						<div class="spacer-16"></div>

						<div class="h5 title fw-800"><?php echo esc_html( $block_title ); ?> </div>

						<div class="spacer-16"></div>

						<div class="date"><?php echo esc_html( $date ); ?></div>
					</div>
				</a>
				<?php
			}
			wp_reset_postdata();
		}
		?>
	</div>
	<?php if ( $query->max_num_pages > 1 ) { ?>
		<div class="spacer-48 spacer-xs-32"></div>
	<?php } ?>

	<?php NYBC_Helpers::pagination( $query->max_num_pages ); ?>

	<div class="spacer-96 spacer-xs-48"></div>
</div>
