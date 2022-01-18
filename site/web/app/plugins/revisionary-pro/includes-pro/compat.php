<?php

class RevisionaryCompat {
    private $saved_meta_keys = [];
    private $rest_buffer_controller = [];
    private $rest_method = '';
    private $rest_params = false;

    function __construct() {
        if (defined('FL_BUILDER_VERSION')) {
            add_action('rvy_init', function($revisionary) {
                require_once(dirname(__FILE__).'/compat/beaver-builder.php');
                new RevisionaryBeaverBuilder($revisionary);
            });
        }

        if (defined('ET_BUILDER_PLUGIN_VERSION') || (false !== stripos(get_template(), 'divi'))) {
            add_action('rvy_init', function($revisionary) {
                global $current_user;

                if ((!defined('REST_REQUEST') || ! REST_REQUEST) && !empty($current_user->ID)) {
					require_once(dirname(__FILE__).'/compat/divi.php');
                	new RevisionaryDivi($revisionary);
                }
            });
        }

        if (defined('ELEMENTOR_VERSION') && !defined('RVY_DISABLE_ELEMENTOR_INTEGRATION')) {
            require_once(dirname(__FILE__).'/compat/elementor.php');
            new RevisionaryElementor();
        }

        // WPML
        if ( defined('ICL_SITEPRESS_VERSION') ) {
            require_once(RVY_ABSPATH . '/includes-pro/compat/wpml.php');
        }

        // ACF: ensure custom fields are stored to archive after pending / scheduled revision publication
        add_action('revision_applied', [$this, 'actRevisionApplied'], 20, 2);

		// todo: move to admin file
        add_filter('revisionary_diff_ui', [$this, 'flt_revision_diff_ui'], 10, 4);

        add_filter('revisionary_compare_meta_fields', [$this, 'flt_compare_meta_fields']);

        // Pro
        if (class_exists('ACFE')) {
            add_action('wp_loaded', [$this, 'addACFEsupport']);
        }
		
		add_action('revisionary_copy_postmeta', [$this, 'actPodsCopyPostmeta'], 10, 3);
    }

    /* --- Pro: support ACF Extended single_meta --- */
    function addACFEsupport() {
        // Pro: support ACF Extended single_meta
        if (function_exists('acf_get_setting') && acf_get_setting('acfe/modules/single_meta') && function_exists('acf_get_metadata')) {
            add_filter('revisionary_compare_meta_from', [$this, 'fltACFEcompareFrom'], 10, 2);
            add_filter('revisionary_compare_meta_to', [$this, 'fltACFEcompareTo'], 10, 2);
            add_filter('revisionary_compare_extra_fields', [$this, 'fltACFEadjustExtraFields'], 10, 2);
        }
    }

    function fltACFEcompareFrom($from_meta, $post_id) {
        $acf_from = acf_get_metadata($post_id, 'acf');
        return (is_array($acf_from)) ? array_merge($from_meta, $acf_from) : $from_meta;
    }

    function fltACFEcompareTo($to_meta, $post_id) {
        $acf_to = acf_get_metadata($post_id, 'acf');
        return (is_array($acf_to)) ? array_merge($to_meta, $acf_to) : $to_meta;
    }

    function fltACFEadjustExtraFields($extra_fields, $post_id) {
        unset($meta['acf']);
        unset($meta['_acf']);
        $acf_to = acf_get_metadata($post_id, 'acf');

        return array_merge($extra_fields, array_fill_keys(array_keys($acf_to), true));
    }

    function flt_revision_diff_ui($return, $compare_from, $compare_to, $args) {
        if (!is_array($args)) {
            return $return;
        }
        
        $to_meta = (isset($args['to_meta'])) ? apply_filters('revisionary_compare_meta_to', $args['to_meta'], $compare_to->ID) : [];
        $meta_fields = (isset($args['meta_fields'])) ? $args['meta_fields'] : [];
        $native_fields = (isset($args['native_fields'])) ? $args['native_fields'] : [];
        $strip_tags = (isset($args['strip_tags'])) ? $args['strip_tags'] : [];

        // Display other scalar meta fields
        $from_meta = ($compare_from) ? apply_filters('revisionary_compare_meta_from', get_post_meta($compare_from->ID), $compare_from->ID) : [];

        $extra_fields = $to_meta; //($compare_from) ? array_merge($from_meta, $to_meta) : $to_meta;

        $extra_fields = array_diff_key($extra_fields, $native_fields, $meta_fields, revisionary_unrevisioned_postmeta());
        $extra_fields = apply_filters('revisionary_compare_extra_fields', array_fill_keys(array_keys($extra_fields), true), $compare_to->ID);

        $key_captions = apply_filters('revisionary_meta_key_captions', ['_yoast_wpseo_' => 'Yoast SEO ', '_thumbnail_id' => __('Featured Image', 'revisionary'), ''], $compare_to);
        $caption_keys = array_keys($key_captions);
        $caption_values = array_values($key_captions);

        ksort($extra_fields);

        foreach($extra_fields as $field => $name) {
            if ($skip_meta_prefixes = apply_filters('revisionary_unrevisioned_prefixes', [], $compare_to)) {
                foreach($skip_meta_prefixes as $prefix) {
                    if (0 === strpos($field, $prefix)) {
                        continue 2;
                    }
                }
            }

            $content_to = (isset($to_meta[$field])) ? $to_meta[$field] : '';
		    $content_to = maybe_unserialize($content_to);

		    // ===== TO META =====
            if (is_array($content_to)) {
                $any_nonscalar = false;
                foreach($content_to as $k => $subval) {
				  $subval = maybe_unserialize($subval);
				  
				  if (is_array($subval) ) {
					$any_sub_nonscalar = false;
					foreach($subval as $_subval) {
						if (!is_scalar($_subval)) {						
							$any_sub_nonscalar = true;
							break;
						}
					}
					
					if (!$any_sub_nonscalar) {
						if (count($content_to) > 1 ) {
							$subval = '(' . implode(', ', $subval) . ')';
						} else {
							$subval = implode(', ', $subval);
						}
					}
				  }
					
                    if (!is_scalar($subval)) {
                        $any_nonscalar = true;
                        break;
                    }
					
				  $content_to[$k] = $subval;
                }

                if (!$any_nonscalar) {
                    $content_to = implode(', ', $content_to);
                }
            }

            if (!is_scalar($content_to)) {
                continue;
            }
		   // =======================

		   // ===== FROM META =====
            if ($compare_from) {
                $content_from = (isset($from_meta[$field])) ? $from_meta[$field] : '';
            } else {
                $content_from = '';
            }

            if (is_array($content_from)) {
                $any_nonscalar = false;
                foreach($content_from as $k => $subval) {
				  $subval = maybe_unserialize($subval);
				  
				  if (is_array($subval) ) {
					$any_sub_nonscalar = false;
					foreach($subval as $_subval) {
						if (!is_scalar($_subval)) {						
							$any_sub_nonscalar = true;
							break;
						}
					}
					
					if (!$any_sub_nonscalar) {
						if (count($content_from) > 1 ) {
							$subval = '(' . implode(', ', $subval) . ')';
						} else {
							$subval = implode(', ', $subval);
						}
					}
				  }
					
                    if (!is_scalar($subval)) {
                        $any_nonscalar = true;
                        break;
                    }
					
				  $content_from[$k] = $subval;
                }

                if (!$any_nonscalar) {
                    $content_from = implode(', ', $content_from);
                }
            }

            if (!is_scalar($content_from)) {
                continue;
            }
		   // =======================

            $args = array(
                'show_split_view' => true,
            );

            $args = apply_filters( 'revision_text_diff_options', $args, $field, $compare_from, $compare_to );

            if ($strip_tags) {
                $content_from = strip_tags($content_from);
                $content_to = strip_tags($content_to);
            }

            if ('_thumbnail_id' == $name) {
                $content_from = ($content_from) ? "$content_from (" . wp_get_attachment_image_url($content_from, 'full') . ')' : '';
                $content_to = ($content_to) ? "$content_to (" . wp_get_attachment_image_url($content_to, 'full') . ')' : '';
            }

            if ($name !== true) {
                // field label applied by filter
                $field_name = $name;
            } else {
            $field_name = str_replace($caption_keys, $caption_values, $field);

                if ($field_name == $field) {
                    $field_name = trim(ucwords(str_replace('_', ' ', $field)));
                }
            }

            if ($diff = wp_text_diff( $content_from, $content_to, $args )) {
                $return[] = array(
                    'id'   => $field,
                    'name' => $field_name,
                    'diff' => $diff,
                );
            }
        }

        return $return;
    }

    function actRevisionApplied($post_id, $revision) {
        if (!function_exists('acf_save_post_revision')) {
            return;
        }

        if ($_post = get_post($post_id)) {
            if (!rvy_in_revision_workflow($_post) && ('inherit' != $_post->post_status)) {
                acf_save_post_revision($post_id, $revision->ID);
            }
        }
    }

    function flt_compare_meta_fields($meta_fields) {
        $meta_fields['_requested_slug'] = __('Requested Slug', 'revisionary');
        
        if (defined('FL_BUILDER_VERSION') && defined('REVISIONARY_BEAVER_BUILDER_DIFF')) {
            $meta_fields['_fl_builder_data'] = __('Beaver Builder Data', 'revisionary');
            $meta_fields['_fl_builder_data_settings'] = __('Beaver Builder Settings', 'revisionary');
        }
    
        if (defined('PUBLISHPRESS_MULTIPLE_AUTHORS_VERSION')) {
            $meta_fields['ppma_authors_name'] = __('Author(s)', 'revisionary');
        }

        return $meta_fields;
    }

    public function actPodsCopyPostmeta($from_post, $to_post_id, $args) {
        global $wpdb;

        // Also copy Pods relationship fields
		if (defined('PODS_VERSION')) {
			$pods_table = "{$wpdb->prefix}podsrel";
	
			$qry = $wpdb->prepare(
				"SELECT * FROM $pods_table WHERE item_id = %d",
				$from_post->ID
			);
	
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM $pods_table WHERE item_id = %d",
					$from_post->ID
				)
			);
	
			foreach($results as $row) {
				$rel_data = array_diff_key(
					(array) $row,
					array_fill_keys(['id', 'pod_id', 'field_id', 'item_id'], true)
				);
	
				$rel_data = array_map('intval', $rel_data);
	
				$match_data = [
					'pod_id' => (int) $row->pod_id,
					'field_id' => (int) $row->field_id,
					'item_id' => (int) $to_post_id
				];
	
				if ($rel_id = (int) $wpdb->get_var(
						$wpdb->prepare(
							"SELECT id FROM $pods_table WHERE pod_id = %d AND field_id = %d AND item_id = %d",
							$match_data['pod_id'],
							$match_data['field_id'],
							$match_data['item_id']
						)
					)
				) {
					$wpdb->update(
						$pods_table,
						$rel_data,
						['id' => $rel_id],
						'%d',
						'%d'
					);
				} else {
					$wpdb->insert(
						$pods_table,
						array_merge($rel_data, $match_data),
						'%d'
					);
				}
            }
            
            wp_cache_flush();
        }
    }

    // @todo: This beta code was never implemented. Will we ever need something like this?
    /*
    public function flt_delete_postmeta($from_post, $target_id, $args) {
		$defaults = ['apply_empty' => false, 'empty_target_only' => false, 'source_meta_keys' => [], 'meta_fields' => []];
		$args = array_merge($defaults, $args);
		foreach (array_keys($defaults) as $var) {
			$$var = $args[$var];
        }
        
        global $wpdb;

		if ($apply_empty && $meta_fields && !defined('REVISIONARY_PRESERVE_META') && !defined('REVISIONARY_PRESERVE_' . strtoupper($from_post->post_type) . '_META' )) {
			$can_remove_empty_fields = apply_filters('revisionary_removable_meta_fields', [], $target_id);

			if (array_intersect($can_remove_empty_fields, $meta_fields)) {
				$delete_meta_keys = array_diff(\get_post_custom_keys($target_id), $source_meta_keys);

				foreach($delete_meta_keys as $meta_key) {
					delete_post_meta($target_id, $meta_key);
				}
			}
		}
    }
    */
}
