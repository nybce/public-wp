<?php
class RevisionaryWPMLTM {
    function __construct() {
        // WPML Translation Management currently handles this as long as Mimic API Actions is enabled
        //add_action('revision_applied', [$this, 'actRevisionApplied']);

        if (is_admin()) {
            if (!empty($_REQUEST['rvy_wpml_sync_needs_update'])) {
                $this->transManageSyncNeedsUpdate();
            }

            if (isset($_REQUEST['needs_update_sync_done'])) {
                add_action('all_admin_notices', [$this, 'confirmationNotice']);
            }
        }
    }

	/*
    public function actRevisionApplied($post_id) {
		global $wpdb;

		$translation_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE trid = %d",
				$post_id
			)
		);

		foreach($translation_ids as $translation_id) {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}icl_translation_status SET needs_update = 1 WHERE translation_id = %d",
					$translation_id
				)
			);
		}
    }
	*/

    public function transManageSyncNeedsUpdate() {
        global $wpdb;

        /*
        - select both translation_id and element_id
        
        - element_id is the post ID of translations. For each of those, set the needs_update flag in translation_status only if the translation post has a post_modified_gmt value older than the source post's post_modified_gmt value
        */

        $new_flagged = 0;

        if (!$flagged_posts = get_option('_revisionary_wpml_flagged_posts')) {
            $flagged_posts = [];
        }

        if (!$flagged_post_ids = get_option('_revisionary_wpml_flagged_post_ids')) {
            $flagged_post_ids = [];
        }

        foreach(get_post_types(['public' => true]) as $post_type) {
            $translations = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT t.translation_id, t.element_id AS translated_post_id, t.trid as source_post_id FROM {$wpdb->prefix}icl_translations t "
                    . " INNER JOIN $wpdb->posts AS source_p ON source_p.ID = t.trid"
                    . " INNER JOIN $wpdb->posts AS trans_p ON trans_p.ID = t.element_id"
                    . " INNER JOIN {$wpdb->prefix}icl_translation_status st ON st.translation_id = t.translation_id "
                    . " WHERE t.element_type = %d AND source_p.post_status NOT IN ('pending-revision', 'future-revision') AND st.needs_update != 1 AND source_p.post_date_gmt > trans_p.post_date_gmt",
                    "post_{$post_type}"
                )
            );

            foreach($translations as $translation) {
                $new_flagged++;
                $flagged_posts[$translation->translation_id] = true;
                $flagged_post_ids[$translation->source_post_id] = true;

                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}icl_translation_status SET needs_update = 1 WHERE translation_id = %d",
                        $translation->translation_id
                    )
                );
            }
        }

        if (!empty($new_flagged)) {
            update_option('_revisionary_wpml_flagged_posts', $flagged_posts);
            update_option('_revisionary_wpml_flagged_post_ids', $flagged_post_ids);
        }

        wp_redirect(admin_url("admin.php?page=revisionary-settings&needs_update_sync_done=$new_flagged"));
    }

    function confirmationNotice() {
        $num_flagged = (int) $_REQUEST['needs_update_sync_done'];

        if (!empty($_REQUEST['needs_update_sync_done'])) {
            $msg = sprintf(_n( 'WPML Translation Management: %s translation flagged.', 'WPML Translation Management: %s translations flagged.', $num_flagged, 'revisionary' ), $num_flagged);
        } else {
            $msg = __('WPML Translation Management: Flags already synchronized', 'revisionary');
        }

        echo '<div class="notice"><p>' . $msg . '</p></div>';
    }

}
