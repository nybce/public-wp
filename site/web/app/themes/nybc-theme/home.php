<?php

/**
 * The main template file
 *
 * @package NYBC
 */

if (!defined('ABSPATH')) {
	exit;
}

get_header();

global $wp_query;

$query = $wp_query;

$archive_hero = get_field('archive_hero', 'options');
$tagline      = $archive_hero ? $archive_hero['tagline'] : '';
$image        = $archive_hero ? $archive_hero['image'] : '';
$color        = $archive_hero ? $archive_hero['color'] : '';
$button_link  = $archive_hero ? $archive_hero['link'] : '';

$post_title = wp_strip_all_tags(get_the_archive_title());

$image_url = !empty($image) ? $image['sizes']['1915x'] : '';

$archive_zip_search                   = get_field('archive_zip_search', 'options');
$archive_zip_search_title             = $archive_zip_search ? $archive_zip_search['title'] : '';
$archive_zip_search_label             = $archive_zip_search ? $archive_zip_search['input_label'] : '';
$archive_zip_search_lnk               = $archive_zip_search ? $archive_zip_search['link'] : '';
$archive_zip_search_description_title = $archive_zip_search ? $archive_zip_search['description_title'] : '';
$archive_zip_search_description       = $archive_zip_search ? $archive_zip_search['description'] : '';

$archive_cta       = get_field('archive_cta', 'options');
$archive_cta_title = $archive_cta ? $archive_cta['title'] : '';
$archive_cta_body  = $archive_cta ? $archive_cta['body'] : '';
$archive_cta_lnk   = $archive_cta ? $archive_cta['link'] : '';

$bydate = '';
if (isset($_GET['bydate']) && !empty($_GET['bydate']) && isset($_GET['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'filter')) {
	$bydate = sanitize_text_field(wp_unslash($_GET['bydate']));
}

$selected_terms = '';
if (isset($_GET['terms']) && !empty($_GET['terms']) && isset($_GET['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'filter')) {
	$selected_terms = sanitize_text_field(wp_unslash($_GET['terms']));
}
?>
<main>

	<div class="section banner">

		<div class="banner-inner type-2">
			<div class="banner-wrapper">
				<div class="container container-lg">
					<div class="row justify-content-center">
						<div class="col-lg-10">
							<div class="banner-content">

								<div class="h5 title fw-800 light"><?php echo esc_html($tagline); ?></div>

								<div class="spacer-24"></div>

								<h1 class="h1 title fw-800 light"><?php echo single_post_title(); ?></h1>

								<?php if (!empty($button_link)) { ?>
								<div class="spacer-24"></div>
								<a class="btn btn-small btn-primary" target="<?php echo esc_attr($button_link['target']); ?>"
									href="<?php echo esc_url($button_link['url']); ?>"><?php echo esc_html($button_link['title']); ?></a>
								<?php } ?>

							</div>
						</div>
					</div>
				</div>
			</div>

			<?php if (!empty($image_url)) { ?>
			<div class="opacity-1"></div>
			<div class="bg rellax" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
			<?php } elseif (!empty($color)) { ?>
			<div class="bg" style="background-color: <?php echo esc_attr($color); ?>"></div>
			<?php } ?>
		</div>
		<div class="spacer-120 spacer-xs-64"></div>

	</div>

	<div class="section " id="two-column-block-block_61cc79611b2ac">
		<div class="decor-news" data-rellax-speed="-1"
			style="background-image: url('<?php echo esc_url(NYBC_IMG_URI); ?>/wave.svg');"></div>
		<div class="container container-lg">
			<div class="row">
				<div class=" col-lg-4 order-lg-0 order-2">
					<?php NYBC_Helpers::sidebar_nav(); ?>
					<div class="promo-wrapper">
						<div class="promo-item">
							<form
								action="<?php echo esc_url(!empty($archive_zip_search_lnk) ? $archive_zip_search_lnk['url'] : ''); ?>"
								target="_blank">
								<div class="h5 title fw-800"><?php echo esc_html($archive_zip_search_title); ?></div>
								<div class="spacer-16"></div>
								<div class="text text-20"><?php echo esc_html($archive_zip_search_label); ?></div>
								<div class="spacer-16"></div>
								<input type="text" name="zipcode" class="input" required placeholder="">
								<div class="spacer-16"></div>
								<button type="submit"
									class="btn btn-primary"><?php echo esc_html(!empty($archive_zip_search_lnk) ? $archive_zip_search_lnk['title'] : ''); ?></button>
							</form>
						</div>
						<?php if (!empty($archive_zip_search_description_title) || !empty($archive_zip_search_description)) { ?>
						<div class="spacer-48"></div>
						<div class="promo-item">
							<div class="h5 title fw-800"><?php echo esc_html($archive_zip_search_description_title); ?></div>
							<div class="spacer-16"></div>
							<div class="text text-20"><?php echo esc_html($archive_zip_search_description); ?></div>
						</div>
						<?php } ?>
					</div>
					<div class="spacer-24"></div>
					<div class="promo-cta">
						<div class="h4 title fw-800 light"><?php echo esc_html($archive_cta_title); ?></div>
						<div class="spacer-8"></div>
						<div class="text-lg text-30"><?php echo esc_html($archive_cta_body); ?></div>
						<?php if (!empty($archive_cta_lnk)) { ?>
						<div class="spacer-24"></div>
						<a class="btn btn-small btn-secondary" target="<?php echo esc_attr($archive_cta_lnk['target']); ?>"
							href="<?php echo esc_url($archive_cta_lnk['url']); ?>"><?php echo esc_html($archive_cta_lnk['title']); ?></a>
						<?php } ?>
					</div>
				</div>
				<div class=" col-lg-8">
					<?php NYBC_Helpers::sidebar_nav(true); ?>
					<div class="filters-wrapper mobile-none">
						<div class="select-item-total"><?php esc_html_e('Showing', 'nybc'); ?>
							<span><?php echo esc_html($query->found_posts); ?></span> <?php esc_html_e('Results', 'nybc'); ?>
						</div>

						<div class="select-item ml-auto">
							<div class="calendar">
								<div class="date_pick">
									<div class="input-calendar" tabindex="0">
										<?php if (empty($bydate)) { ?>
										<p><?php esc_html_e('Filter By Date', 'nybc'); ?></p>
										<?php } else { ?>
										<p><?php echo esc_html($bydate); ?></p>
										<?php } ?>
										<img src="<?php echo esc_url(NYBC_IMG_URI); ?>/icons/calendar.svg" alt="">
									</div>
									<form>
										<?php wp_nonce_field('filter', 'nonce'); ?>
										<input type="hidden" name="terms" value="<?php echo esc_attr($selected_terms); ?>">
										<div class="date_selector">
											<div class="years">
												<a class="picker-left picker-select-arrow" tabindex="0"></a>
												<input class="year-selected" maxlength="4" name="bydate" readonly>
												<a class="picker-right picker-select-arrow" tabindex="0"></a>
											</div>
											<div class="months">
											</div>
										</div>
									</form>
									<script>
									jQuery(function($) {
										$('.months').on("click", '.month', function(e) {
											var btn = $(this);
											setTimeout(function() {
												btn.closest('form').submit();
											}, 200);
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
						if ($query->have_posts()) {
							while ($query->have_posts()) {
								$query->the_post();
								$lnk         = get_the_permalink();
								$image_url   = get_the_post_thumbnail_url(null, '519x292');
								$block_title = get_the_title();
								$date        = get_the_time('M j, Y');
								$tags        = get_the_tags();

								$image = array(
									'url' => $image_url,
									'alt' => $block_title,
								);

						?>
						<a href="<?php echo esc_url($lnk); ?>" class="news-item">
							<?php if (!empty($image_url)) { ?>
							<div class="news-img">
								<?php NYBC_Helpers::picture($image); ?>
							</div>
							<?php } ?>
							<div class="border-top"></div>

							<div class="news-info">
								<?php
										if (!empty($tags)) {
										?>
								<ul class="tags-list">
									<?php foreach ($tags as $tg) { ?>
									<li><?php echo esc_html($tg->name); ?></li>
									<?php } ?>
								</ul>
								<?php } ?>

								<div class="spacer-16"></div>

								<div class="h5 title fw-800"><?php echo esc_html($block_title); ?> </div>

								<div class="spacer-16"></div>

								<div class="date"><?php echo esc_html($date); ?></div>
							</div>
						</a>
						<?php
							}
						}
						?>
					</div>
					<?php if ($query->max_num_pages > 1) { ?>
					<div class="spacer-48 spacer-xs-32"></div>
					<?php } ?>

					<?php NYBC_Helpers::pagination($query->max_num_pages); ?>



				</div>

			</div>
		</div>
		<div class="spacer-96 spacer-xs-48"></div>
	</div>
	<div class="spacer-120"></div>

</main>

<?php
get_footer();