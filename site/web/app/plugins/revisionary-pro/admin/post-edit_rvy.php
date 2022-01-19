<?php

/*
 * Post Edit: UI modifications for Classic Editor
 */
class RvyPostEdit {
    function __construct() {
        add_action('admin_head', array($this, 'act_admin_head') );

        // deal with case where another plugin replaced publish metabox
        add_filter('presspermit_preview_post_label', [$this, 'fltPreviewLabel']);
        add_filter('presspermit_preview_post_title', [$this, 'fltPreviewTitle']);

        add_action('post_submitbox_misc_actions', [$this, 'act_post_submit_revisions_links'], 5);

        add_filter('user_has_cap', [$this, 'fltAllowBrowseRevisionsLink'], 50, 3);

        add_filter('revisionary_apply_revision_allowance', [$this, 'fltRevisionAllowance'], 5, 2);
    }

    function act_admin_head() {
        ?>
        <script type="text/javascript">
        /* <![CDATA[ */
        jQuery(document).ready( function($) {
            var rvyNowCaption = "<?php _e( 'Current Time', 'revisionary' );?>";
            $('#publishing-action #publish').show();
        });
        /* ]]> */
        </script>

        <?php
        global $post;

        if (!empty($post) && !rvy_is_supported_post_type($post->post_type)) {
            return;
        }

        wp_enqueue_script( 'rvy_post', RVY_URLPATH . "/admin/post-edit.js", array('jquery'), PUBLISHPRESS_REVISIONS_VERSION, true );

        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';

        $do_pending_revisions = rvy_get_option('pending_revisions');
        $do_scheduled_revisions = rvy_get_option('scheduled_revisions');

        if (('revision' == $post->post_type) || rvy_in_revision_workflow($post)) {
            wp_enqueue_script('rvy_object_edit', RVY_URLPATH . "/admin/rvy_revision-classic-edit{$suffix}.js", ['jquery', 'jquery-form'], PUBLISHPRESS_REVISIONS_VERSION, true);

            $args = \PublishPress\Revisions\PostEditorWorkflowUI::revisionLinkParams(compact('post', 'do_pending_revisions', 'do_scheduled_revisions'));

            $args['deleteCaption'] = __('Delete Permanently', 'revisionary');

            wp_localize_script( 'rvy_object_edit', 'rvyObjEdit', $args );

            if (defined('PUBLISHPRESS_VERSION')) {
                wp_dequeue_script('publishpress-custom_status');
                wp_dequeue_style('publishpress-custom_status');
            }
        } elseif (current_user_can('edit_post', $post->ID)) {
            $status_obj = get_post_status_object($post->post_status);

		    if (('future' != $post->post_status) && (!empty($status_obj->public) || !empty($status_obj->private) || rvy_get_option('pending_revision_unpublished'))) {
                wp_enqueue_script('rvy_object_edit', RVY_URLPATH . "/admin/rvy_post-classic-edit{$suffix}.js", ['jquery', 'jquery-form'], PUBLISHPRESS_REVISIONS_VERSION, true);

                $args = \PublishPress\Revisions\PostEditorWorkflowUI::postLinkParams(compact('post', 'do_pending_revisions', 'do_scheduled_revisions'));
                wp_localize_script( 'rvy_object_edit', 'rvyObjEdit', $args );
            }
        }

        $args = array(
            'nowCaption' => __( 'Current Time', 'revisionary' ),
        );
        wp_localize_script( 'rvy_post', 'rvyPostEdit', $args );
	}

    public function fltPreviewLabel($preview_caption) {
        global $post;

        $type_obj = get_post_type_object($post->post_type);

        if ($type_obj && empty($type_obj->public)) {
            return $preview_caption;
        }

        //if (current_user_can('edit_post', rvy_post_id($post->ID))) {
        //    $preview_caption = ('future-revision' == $post->post_status) ? __('Preview / Publish', 'revisionary') : __('Preview / Approve', 'revisionary');

        //} elseif ($type_obj && !empty($type_obj->public)) {
            $preview_caption = __('Preview');
        //}

        return $preview_caption;
    }

    public function fltPreviewTitle($preview_title) {
        global $post;

        $type_obj = get_post_type_object($post->post_type);

        if ($type_obj && empty($type_obj->public)) {
            return $preview_title;
        }

        if (current_user_can('edit_post', rvy_post_id($post->ID))) {
            $preview_title = __('View / moderate saved revision', 'revisionary');

        } elseif ($type_obj && !empty($type_obj->public)) {
            $preview_title = __('View saved revision', 'revisionary');
        }

        return $preview_title;
    }

    function act_post_submit_revisions_links() {
        global $post;

        // These links do not apply when editing a revision
        if (rvy_in_revision_workflow($post) || !current_user_can('edit_post', $post->ID) || !rvy_is_supported_post_type($post->post_type)) {
            return;
        }

        if (rvy_get_option('scheduled_revisions')) {
	        if ($_revisions = rvy_get_post_revisions($post->ID, 'future-revision', ['orderby' => 'ID', 'order' => 'ASC'])) {
	            ?>
	            <div class="misc-pub-section">
	            <?php
	            printf('%s' . pp_revisions_status_label('future-revision', 'plural') . ': %s', '<span class="dashicons dashicons-clock"></span>&nbsp;', '<b>' . count($_revisions) . '</b>');
	            ?>
	            <a class="hide-if-no-js"
                    href="<?php echo esc_url(admin_url("revision.php?post_id=$post->ID&revision=future-revision")); ?>" target="_revision_diff"><?php _ex('Compare', 'revisions', 'revisionary'); ?></a>
	            </div>
	            <?php
	        }
        }

        if (rvy_get_option('pending_revisions')) {
	        if ($_revisions = rvy_get_post_revisions($post->ID, 'pending-revision', ['orderby' => 'ID', 'order' => 'ASC'])) {
	            ?>
	            <div class="misc-pub-section">
	            <?php
	            printf('%s' . pp_revisions_status_label('pending-revision', 'plural') . ': %s', '<span class="dashicons dashicons-edit"></span>&nbsp;', '<b>' . count($_revisions) . '</b>');
	            ?>
	            <a class="hide-if-no-js"
                    href="<?php echo esc_url(admin_url("revision.php?post_id=$post->ID&revision=pending-revision")); ?>" target="_revision_diff"><?php _ex('Compare', 'revisions', 'revisionary'); ?></a>
	            </div>
	            <?php
	        }
	    }
    }

    function fltAllowBrowseRevisionsLink($wp_blogcaps, $reqd_caps, $args) {
        if (!empty($args[0]) && ('edit_post' == $args[0]) && !empty($args[2])) {
            if ($_post = get_post((int) $args[2])) {
                if ('revision' == $_post->post_type && current_user_can('edit_post', $_post->post_parent)) {
                    if (did_action('post_submitbox_minor_actions')) {
                        if (!did_action('post_submitbox_misc_actions')) {
                            $wp_blogcaps = array_merge($wp_blogcaps, array_fill_keys($reqd_caps, true));
                        } else {
                            remove_filter('user_has_cap', [$this, 'fltAllowBrowseRevisionsLink'], 50, 3);
                        }
                    }
                }
            }

        }

        return $wp_blogcaps;
    }

    function fltRevisionAllowance($allowance, $post_id) {
        // Ensure that revision "edit" link is not suppressed for the Revisions > Browse link
        if (did_action('post_submitbox_minor_actions') && !did_action('post_submitbox_misc_actions')) {
            $allowance = true;
        }

        return $allowance;
    }

}
