<?php

class RevisionaryPro {
    function __construct() {
        add_filter('default_options_rvy', [$this, 'fltDefaultOptions']);
        add_filter('options_sitewide_rvy', [$this, 'fltDefaultOptionScope']);
        add_filter('wp_revisions_to_keep', [$this, 'fltMaybeSkipRevisionCreation'], 10, 2);

        add_filter('revisionary_main_post_statuses', [$this, 'fltMainPostStatuses'], 5, 2);
        add_filter('revisionary_preview_compare_view_caption', [$this, 'fltPreviewCompareViewCaption'], 10, 2);
        add_filter('revisionary_preview_view_caption', [$this, 'fltPreviewCompareViewCaption'], 10, 2);

        add_action('revisionary_front_init', [$this, 'loadACFtaxonomyPreviewFilters']);
    }

    function fltDefaultOptions($options) {
        $options['pending_revision_unpublished'] = 0;
        $options['prevent_rest_revisions'] = 0;
        return $options;
    }

    function fltDefaultOptionScope($options) {
        $options['pending_revision_unpublished'] = true;
        $options['prevent_rest_revisions'] = true;
        return $options;
    }

    function fltMaybeSkipRevisionCreation($num, $post) {	
		if (class_exists('ACF') && rvy_get_option('prevent_rest_revisions')) {	
			$arr_url = parse_url(get_option('siteurl'));
			
			if ($arr_url && isset($arr_url['path'])) {
				if (0 === strpos($_SERVER['REQUEST_URI'], $arr_url['path'] . '/wp-json/wp/')) {
					$num = 0;
				}
			}
		}

		return $num;
    }
    
    // @todo: Are these ACF filters still needed with Revisions 3.0 submission mechanism?
    
    function loadACFtaxonomyPreviewFilters() {
        // Some ACF implementations cause the current revision (post_status = 'inherit') to be loaded as queried object prior to taxonomy field value retrieval
		// However, don't force revision_id elsewhere because main post / current revision ID seems to be required for some other template rendering. 
		add_filter("acf/load_value", [$this, 'fltACFenablePostFilter'], 1);
		add_filter("acf/load_value", [$this, 'fltACFdisablePostFilter'], 9999);
    }

    public function fltACFenablePostFilter($val) {
		add_filter("acf/decode_post_id", [$this, 'fltACFdecodePostID'], 10, 2);
		return $val;
	}

	public function fltACFdisablePostFilter($val) {
		remove_filter("acf/decode_post_id", [$this, 'fltACFdecodePostID'], 10, 2);
		return $val;
	}

    public function fltACFdecodePostID($args, $post_id) {
        if ($args["type"] != "option") {
            $args['id'] = rvy_detect_post_id();
        }

        return $args;
    }

    function fltPreviewCompareViewCaption($caption, $revision) {
        $status_obj = get_post_status_object(get_post_field('post_status', rvy_post_id($revision->ID)));
        
        if ($status_obj && (empty($status_obj->public) && empty($status_obj->private))) {
            $caption = __("%sCompare%s%sView Current Draft%s", 'revisionary');
        }

        $caption = str_replace( ' ', '&nbsp;', $caption);

        return $caption;
    }

    function fltPreviewViewCaption($caption, $revision) {

        $status_obj = get_post_status_object(get_post_field('post_status', rvy_post_id($revision->ID)));
        
        if ($status_obj && (empty($status_obj->public) && empty($status_obj->private))) {
            $caption = __("%sView Current Draft%s", 'revisionary');
        }

        $caption = str_replace( ' ', '&nbsp;', $caption);

        return $caption;
    }

    function fltMainPostStatuses($statuses, $return = 'object') {
        if (rvy_get_option('pending_revision_unpublished')) {
            $statuses = get_post_stati( ['internal' => false], $return );
        }

        return $statuses;
    }
}
