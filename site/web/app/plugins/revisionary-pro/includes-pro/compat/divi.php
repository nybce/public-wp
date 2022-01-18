<?php
if( basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) )
	die( 'This page cannot be called directly.' );

/**
 * @package     PublishPress\Revisions\RevisionaryDivi
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (c) 2021 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */
class RevisionaryDivi
{		
	function __construct($revisionary) {
		add_action('wp_print_scripts', [$this, 'submissionRedirect']);

		add_filter('et_fb_get_asset_helpers', [$this, 'fltDiviAssetHelpers'], 11, 2);

		add_action('et_save_post', [$this, 'actDiviUpdatePost']);

		// prevent Divi reload
		if (!defined('REVISIONARY_LEGACY_DIVI_REDIRECT')) {
			add_action('init', function() {
				remove_action( 'template_redirect', 'et_fb_auto_activate_builder' );
			});
		}

		add_action('revisionary_queue_row_actions', [$this, 'actRevisionQueueRowActions'], 10, 2);

		add_filter('revisionary_create_revision_redirect', [$this, 'fltCreateRevisionRedirect'], 10, 2);

		add_filter('revisionary_admin_bar_absolute', [$this, 'fltAdminBarAbsolute']);

		add_filter('page_link', [$this, 'fltPermalink'], 10, 2);
		add_filter('post_type_link', [$this, 'fltPermalink'], 10, 2);

		add_action( 'admin_bar_menu', [$this, 'et_fb_add_admin_bar_link'], 1 );

		/*
		if (\PublishPress\Revisions\Utils::isRESTurl() 
		|| (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) && empty($_REQUEST['et_fb']) && empty($_REQUEST['et_pb_use_builder']) && \PublishPress\Revisions\Utils::isBlockEditorActive())
		) {
			return;
		}
		*/
	}

	function et_fb_add_admin_bar_link() {
		global $wp_the_query;

		if (!empty($wp_the_query->queried_object) && ($wp_the_query->queried_object->ID != $wp_the_query->query_vars['p']) && rvy_in_revision_workflow($wp_the_query->query_vars['p'])) {
			$wp_the_query->queried_object = get_post($wp_the_query->query_vars['p']);
		}
	}

	function fltAdminBarAbsolute($absolute) {
		return false;
	}

	function fltPermalink($url, $post) {
		static $busy;

		if (!empty($busy)) { //} || empty($_REQUEST['et_fb'])) {
			return $url;
		}

		$busy = true;

		if (rvy_in_revision_workflow($post)) {
			$url = rvy_preview_url($post);
		}

		$busy = false;

		return $url;
	}

	function fltCreateRevisionRedirect($url, $post_id) {
        if (!empty($_REQUEST['front'])) {
			$url = add_query_arg(['et_fb' => 1, 'PageSpeed' => 'off'], rvy_preview_url($post_id));
        }

        return $url;
    }

	function fltDiviAssetHelpers($content, $post_type) {
		if (!empty($_REQUEST['et_post_id'])) {
			$revision_id = (int) $_REQUEST['et_post_id'];

			if (rvy_in_revision_workflow($revision_id)) {
				$revision = get_post($revision_id);

				switch ($revision->post_mime_type) {
					case 'draft-revision':
						if (current_user_can("set_revision_pending-revision", $revision_id)) {
							$content = str_replace('publishButtonText":"' . __('Publish') . '"', 'publishButtonText":"' . pp_revisions_status_label('pending-revision', 'submit_short') . '"', $content);
							$content = str_replace('"publish":"' . __('Publish') . '"', '"publish":"' . pp_revisions_status_label('pending-revision', 'submit_short') . '"', $content);
						} else {
							$content = str_replace('publishButtonText":"' . __('Publish') . '"', 'publishButtonText":"' . pp_revisions_status_label('pending-revision', 'update') . '"', $content);
							$content = str_replace('"publish":"' . __('Publish') . '"', '"publish":"' . pp_revisions_status_label('pending-revision', 'update') . '"', $content);
						}

						break;

					case 'pending-revision':
						if (current_user_can("edit_post", rvy_post_id($revision_id))) {
							$content = str_replace('publishButtonText":"' . __('Publish') . '"', 'publishButtonText":"' . __('Approve', 'revisionary') . '"', $content);
							$content = str_replace('"publish":"' . __('Publish') . '"', '"publish":"' . __('Approve', 'revisionary') . '"', $content);
						} else {
							$content = str_replace('publishButtonText":"' . __('Publish') . '"', 'publishButtonText":"' . pp_revisions_status_label('pending-revision', 'update') . '"', $content);
							$content = str_replace('"publish":"' . __('Publish') . '"', '"publish":"' . pp_revisions_status_label('pending-revision', 'update') . '"', $content);
						}

						$content = str_replace('publishButtonText":"' . __('Submit') . '"', 'publishButtonText":"' . pp_revisions_status_label('pending-revision', 'update') . '"', $content);
						$content = str_replace('"publish":"' . __('Submit') . '"', '"publish":"' . pp_revisions_status_label('pending-revision', 'update') . '"', $content);
						
						break;

					default:
						$content = str_replace('publishButtonText":"' . __('Publish') . '"', 'publishButtonText":"' . pp_revisions_label('update_revision') . '"', $content);
						$content = str_replace('"publish":"' . __('Publish') . '"', '"publish":"' . pp_revisions_label('update_revision') . '"', $content);
				}
			}
		}

		return $content;
	}

	function submissionRedirect() {
		if ($post_id = rvy_detect_post_id()) {
            if ($revision_status = rvy_in_revision_workflow($post_id)) {
				/* Redirect to Revisions preview screen after revision status change */
				?>

				<script type="text/javascript">
				/* <![CDATA[ */

				var rvyIntDetectStatusChange = setInterval(function() {
						var elems = document.getElementsByClassName("et-fb-icon--check");

						if (elems.length) {
							if (elems[0].parentNode.classList.contains("et-fb-button--publish")) {
								clearInterval(rvyIntDetectStatusChange);

								setTimeout(function() {
									window.location = '<?php echo add_query_arg('base_post', rvy_post_id($post_id), rvy_preview_url($post_id));?>';
								}, 200);
							}
						}
				}, 500);

				/* ]]> */
				</script>

				<style>
				body.et-fb div.rvy_view_revision a.rvy_preview_linkspan {display: none;}
				</style>

				<?php

			}
		}
	}

	function actDiviUpdatePost($post_id) {
		if (empty($_REQUEST['action']) || ('et_fb_ajax_save' != $_REQUEST['action'])
		|| empty($_REQUEST['options']) || empty($_REQUEST['options']['status'])
		|| (!in_array($_REQUEST['options']['status'], ['pending', 'publish']))
		) {
			return;
		}

		$post = get_post($post_id);
		
		if (!rvy_in_revision_workflow($post)) {
			return;
		}

		// note: capabilities are validated downstream
		switch ($post->post_mime_type) {
			case 'draft-revision' :
				require_once(dirname(REVISIONARY_FILE).'/admin/revision-action_rvy.php');	
				rvy_revision_submit($post_id);
				break;

			case 'pending-revision' :
				require_once( dirname(REVISIONARY_FILE).'/admin/revision-action_rvy.php');	
				rvy_revision_approve($post_id);
				break;
		}
	}
	
	function actRevisionQueueRowActions($actions, $post) {
        $actions['divi'] = sprintf(
            '<a href="%1$s" class="" title="%2$s" aria-label="%2$s">%3$s</a>',
            add_query_arg(['et_fb' => 1, 'PageSpeed' => 'off'], get_permalink($post->ID)),
            __('Divi', 'revisionary'),
            __('Divi', 'revisionary')
        );

        return $actions;
    }
} // end RevisionaryDivi class
