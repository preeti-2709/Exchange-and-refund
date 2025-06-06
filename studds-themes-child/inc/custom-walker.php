<?php
if ( class_exists('Boxshop_Walker_Nav_Menu') ) {

    class My_Custom_Walker_Nav_Menu extends Walker_Nav_Menu {
        public $menu_config = array();
        public $parent_megamenu;
        public $megamenu_column;
        public $parent_sidebar;
        public $parent_static_html;
        public $custom_menu_item = false; // flag to track if we're inside HELMETS or ACCESSORIES
    
        function __construct() {
            global $boxshop_theme_options;
            if (isset($boxshop_theme_options['ts_menu_num_widget'])) {
                $this->menu_config['num_widget'] = (int)$boxshop_theme_options['ts_menu_num_widget'];
            }
            if (isset($boxshop_theme_options['ts_menu_thumb_width'])) {
                $this->menu_config['thumb_width'] = (int)$boxshop_theme_options['ts_menu_thumb_width'];
            }
            if (isset($boxshop_theme_options['ts_menu_thumb_height'])) {
                $this->menu_config['thumb_height'] = (int)$boxshop_theme_options['ts_menu_thumb_height'];
            }
        }
    
        function start_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);
            
            // Custom condition: special <ul> only if inside HELMETS or ACCESSORIES
            if ( $this->custom_menu_item ) {
                $output .= "\n$indent<ul class=\"sub-menu custom-sub-menu-for-helmets-accessories\">\n";
            } else {
                $output .= "\n$indent<ul class=\"sub-menu\">\n";
            }
        }
    
        function end_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }
    
        function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
            global $wp_query;
            $indent = ($depth) ? str_repeat("\t", $depth) : '';
            $item_output = '';

            // Custom logic: HELMETS / ACCESSORIES
            $custom_titles = array('HELMETS', 'ACCESSORIES');
            $this->custom_menu_item = ($depth === 0 && in_array(strtoupper($item->title), $custom_titles));

            // ID and class
            if ($this->custom_menu_item) {
                $output .= $indent . '<li id="menu-change menu-item-' . $item->ID . '" class="custom-li-for-' . strtolower($item->title) . '">';
            } else {
                $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter((array)$item->classes), $item, $args));
                $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
                $id_attr = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
                $id_attr = $id_attr ? ' id="' . esc_attr($id_attr) . '"' : '';
                $output .= $indent . '<li' . $id_attr . $class_names . '>';
            }

            // Attributes
            $atts = array(
                'title'  => !empty($item->attr_title) ? $item->attr_title : '',
                'target' => !empty($item->target) ? $item->target : '',
                'rel'    => !empty($item->xfn) ? $item->xfn : '',
                'href'   => !empty($item->url) ? $item->url : ''
            );

            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            // Title & icon
            $thumbnail_id = get_post_meta($item->ID, '_menu_item_ts_thumbnail_id', true);
            $icon_html = '';
            if ($thumbnail_id > 0) {
                $icon_html = '<span class="menu-icon">' . wp_get_attachment_image($thumbnail_id, 'boxshop_menu_icon_thumb', false) . '</span>';
            }

            $title = '<span class="menu-label">' . apply_filters('the_title', $item->title, $item->ID) . '</span>';

            // Build link
            $item_output .= '<a' . $attributes . '>' . $icon_html . $title;

            if (strlen($item->description) > 0) {
                $item_output .= '<div class="menu-desc menu-desc-lv' . $depth . '">' . esc_html($item->description) . '</div>';
            }

            $item_output .= '</a>';

            // Add submenu icon if needed
            if ($item->sub_count > 0 || $this->parent_megamenu) {
                $item_output .= '<span class="ts-menu-drop-icon"></span>';
            }

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
    
        function end_el(&$output, $item, $depth = 0, $args = array()) {
            $output .= "</li>\n";
        }
    }

}
