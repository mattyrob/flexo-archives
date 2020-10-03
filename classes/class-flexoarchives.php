<?php
class FlexoArchives {
	// Options constants
	public $old_options_name       = 'widget_flexo';
	public $options_name           = 'flexo_archives';
	public $opt_animate            = 'animate'; // bool: list animation enabled
	public $opt_nofollow           = 'nofollow'; // bool: add rel="nofollow" to links
	public $opt_count              = 'count'; // bool: monthly post counts in lists
	public $opt_yrtotal            = 'yeartotal';// bool: yearly post total
	public $opt_wtitle             = 'title'; // string; widget title string
	public $opt_converted          = '2'; // array: converted non-multi widget settings
	public $opt_month_desc         = 'month-descend'; // bool: order months descending
	public $opt_collapse_decades   = 'collapse-decades'; // bool: collapse complete decades
	public $opt_yrcount;

	// Filename constants
	public $flexo_js      = 'flexo.js';
	public $flexo_anim_js = 'flexo-anim.js';

	// Options array
	public $options;

	/**
	 * PHP constructor
	 */
	public function __construct() {
		$this->initialize();
	}

	/**
	 * Register plugin callbacks with WordPress
	 */
	public function initialize() {
		// get translations loaded
		add_action( 'init', array( $this, 'load_translations' ) );

		// make sure options are initialized
		$this->set_default_options();

		// register callbacks
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'options_menu_item' ) );
		}

		add_action( 'widgets_init', array( $this, 'widget_init' ) );

		register_activation_hook( FLEXOPLUGIN, array( __CLASS__, 'activate' ) );
	}

	/**
	 * Sets the default values for unset options
	 */
	public function set_default_options() {
		$options = $this->get_opts();

		$global_defaults = array(
			$this->opt_animate            => true,
			$this->opt_nofollow           => false,
			$this->opt_month_desc         => false,
			$this->opt_yrcount            => false,
		);

		// global defaults
		foreach ( $global_defaults as $def_key => $def_value ) {
			if ( ! isset( $options[ $def_key ] ) ) {
				$options[ $def_key ] = $def_value;
			}
		}

		// widget options
		foreach ( $options as $opts_key => $opts_value ) {
			if ( is_numeric( $opts_key ) ) {
				// default widget title is "Archives"
				if ( ! isset( $opts_value[ $this->opt_wtitle ] ) ) {
					$opts_value[ $this->opt_wtitle ] = wp_strip_all_tags( __( 'Archives', 'flexo-archives' ) );
				}

				// post counts disabled
				if ( ! isset( $opts_value[ $this->opt_count ] ) ) {
					$opts_value[ $this->opt_count ] = false;
				}
			}
		}

		$this->set_opts( $options );
	}

	/**
	 * Gets the entire options array from the database. Converts
	 * old-style options to new-style multi-widget options.
	 *
	 * Returns: An array of options. Individual options
	 * can be accessed by their keys, defined as class
	 * constants (see above).
	 */
	public function get_opts() {
		// options not initialized yet
		if ( is_null( $this->options ) ) {
			$this->options = get_option( $this->options_name );

			if ( ! $this->options ) {
				// convert old-style options to multi-widget options
				if ( get_option( $this->old_options_name ) ) {
					require_once FLEXOPATH . 'classes/class-flexoarchives-upgrade.php';
					$flexoarchive_upgrade = new FlexoArchives_Upgrade();
					$flexoarchive_upgrade->convert_old_opts();
				} else {
					// this will get populated by defaults
					$this->options = array();
				}
			}
		}

		if ( isset( $this->options[0] ) ) {
			unset( $this->options[0] );
		}

		return $this->options;
	}

	/**
	 * Save a modified options array to the database
	 *
	 * Arguments: An array containing the options. Array
	 * keys are defined as class constants (see above).
	 */
	public function set_opts( $newoptions = null ) {
		$options = $this->get_opts();
		if ( $options !== $newoptions ) {
			$this->options = $newoptions;
			update_option( $this->options_name, $newoptions );
		}
	}

	/**
	 * Gets the widget title set in the database
	 */
	public function widget_title( $widget_num ) {
		$options = $this->get_opts();

		$widget_opts = isset( $options[ $widget_num ] ) ? $options[ $widget_num ] : false;
		return $widget_opts ? esc_attr( $widget_opts[ $this->opt_wtitle ] ) : __( 'Archives', 'flexo-archives' );
	}

	/**
	 * Reports whether the user enabled post counts
	 */
	public function widget_count_enabled( $widget_num ) {
		$options = $this->get_opts();

		$widget_opts = isset( $options[ $widget_num ] ) ? $options[ $widget_num ] : false;
		return $widget_opts ? $widget_opts[ $this->opt_count ] : false;
	}

	/**
	 * Reports whether the user enabled yearly post totals
	 */
	public function yearly_total_enabled() {
		$options = $this->get_opts();
		return $options[ $this->opt_yrtotal ];
	}

	/**
	 * Reports whether list animation is enabled
	 */
	public function animation_enabled() {
		$options = $this->get_opts();
		return $options[ $this->opt_animate ];
	}

	/**
	 * Reports whether links should have rel="nofollow" added.
	 */
	public function nofollow_enabled() {
		$options = $this->get_opts();
		return $options[ $this->opt_nofollow ];
	}

	/**
	 * How should months in the lists be sorted
	 */
	public function month_order() {
		$options = $this->get_opts();
		return $options[ $this->opt_month_desc ] ? 'DESC' : 'ASC';
	}

	/**
	 * Whether to collapse complete decades
	 */
	public function collapse_decades() {
		$options = $this->get_opts();
		return $options[ $this->opt_collapse_decades ];
	}


	/**
	 * Loads translated strings from catalogs in ./lang
	 */
	public function load_translations() {
		$lang_dir = FLEXOPATH . 'lang';
		load_plugin_textdomain( 'flexo-archives', false, $lang_dir );
	}

	/**
	 * Function to register our sidebar widget with WordPress
	 */
	public function widget_init() {
		// Check for required functions
		if ( ! function_exists( 'wp_register_sidebar_widget' ) ) {
			return;
		}

		// Call the registration function on init
		$this->register_widgets();
	}

	/**
	 * Register the configuration page
	 */
	public function options_menu_item() {
		$page_title = __( 'Flexo Archives Advanced Options', 'flexo-archives' );
		$menu_title = __( 'Flexo Archives', 'flexo-archives' );
		$menu_slug  = 'flexo-archvies-options';

		$flexo_options = add_options_page( $page_title, $menu_title, 'manage_options', $menu_slug, array( &$this, 'options_page' ) );
		// add stylesheet for settings page
		add_action( "admin_print_styles-$flexo_options", array( &$this, 'options_page_css' ) );
	}

	/**
	* Options page css
	*/
	public function options_page_css() {
		$css_url = plugins_url( 'flexo-admin-style.css', __FILE__ );
		wp_enqueue_style( 'flexo-admin-style', $css_url, array(), '1.0' );
	}

	/**
	 * Output advanced plugin configuration page
	 */
	public function options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient priveleges to access this page.', 'flexo-archives' ) );
		}

		// form submitted
		$options    = $this->get_opts();
		$newoptions = $this->get_opts();

		if ( ! empty( $_POST['flexo-submit'] ) &&
			check_admin_referer( 'flexo-archives-options-page' ) ) {
			$newoptions[ $this->opt_animate ]          = isset( $_POST['flexo-animate'] );
			$newoptions[ $this->opt_nofollow ]         = isset( $_POST['flexo-nofollow'] );
			$newoptions[ $this->opt_month_desc ]       = isset( $_POST['flexo-monthdesc'] );
			$newoptions[ $this->opt_yrtotal ]          = isset( $_POST['flexo-yrtotal'] );
			$newoptions[ $this->opt_collapse_decades ] = isset( $_POST['flexo-decades'] );
		}

		// save if options changed
		if ( $options !== $newoptions ) {
			$options = $newoptions;
			$this->set_opts( $newoptions );
		}

		$animate    = $this->animation_enabled() ? 'checked="checked"' : '';
		$total      = $this->yearly_total_enabled() ? 'checked="checked"' : '';
		$nofollow   = $this->nofollow_enabled() ? 'checked="checked"' : '';
		$monthdesc  = $this->month_order() === 'DESC' ? 'checked="checked"' : '';
		$decades    = $this->collapse_decades() ? 'checked="checked"' : '';

		?>
<div class="wrap">
	<h2><?php _e( 'Advanced Flexo Archives Options', 'flexo-archives' ); ?></h2>
	<!-- <div class="narrow"> -->
	<div id="flexo-admin-form">
	<form name="flexo-options-form" method="post" action="">
		<?php wp_nonce_field( 'flexo-archives-options-page' ); ?>
	<fieldset>
		<legend><?php _e( 'Global Options', 'flexo-archives' ); ?></legend>
		<p><label for="flexo-animate"><input type="checkbox" class="checkbox" id="flexo-animate" name="flexo-animate" <?php echo $animate; ?>/> <?php _e( 'animate collapsing and expanding lists', 'flexo-archives' ); ?></label></p>
		<p><label for="flexo-nofollow"><input type="checkbox" class="checkbox" id="flexo-nofollow" name="flexo-nofollow" <?php echo $nofollow; ?>/> <?php _e( 'add rel="nofollow" to links', 'flexo-archives' ); ?></label></p>
		<p><label for="flexo-monthdesc"><input type="checkbox" class="checkbox" id="flexo-monthdesc" name="flexo-monthdesc" <?php echo $monthdesc; ?>/> <?php _e( 'sort months in descending order', 'flexo-archives' ); ?></label></p>
		<p><label for="flexo-yrtotal"><input type="checkbox" class="checkbox" id="flexo-yrtotal" name="flexo-yrtotal" <?php echo $total; ?>/> <?php _e( 'show yearly post totals in lists', 'flexo-archives' ); ?></label></p>
		<p><label for="flexo-decades"><input type="checkbox" class="checkbox" id="flexo-decades" name="flexo-decades" <?php echo $decades; ?>/> <?php _e( 'collapse complete decades', 'flexo-acrhives' ); ?></label></p>
	</fieldset>

	<input type="submit" name="flexo-submit" class="button-primary" value="<?php _e( 'Submit', 'flexo-archives' ); ?>"/>
	</form>
	</div>
</div>
		<?php
	}

	/**
	 * Handle widget configuration
	 */
	public function widget_control( $args ) {
		$options    = $this->get_opts();
		$newoptions = $this->get_opts();

		if ( is_array( $_POST ) && ! empty( $_POST['flexo-archives'] ) &&
			check_admin_referer( 'flexo-archives-widget-options' ) ) {
			foreach ( $_POST['flexo-archives'] as $wnum => $vals ) {
				if ( empty( $vals ) && isset( $newoptions[ $wnum ] ) ) {
					continue;
				}

				if ( ! isset( $newoptions[ $wnum ] ) && -1 === $args['number'] ) {
					$args['number']            = $wnum;
					$newoptions['last_number'] = $wnum;
				}
				$newoptions[ $wnum ] = array(
					$this->opt_count  => isset( $vals['flexo-count'] ),
					$this->opt_wtitle => wp_strip_all_tags( stripslashes( $vals['flexo-title'] ) ),
				);
			}

			if ( -1 === $args['number'] && ! empty( $newoptions['last_number'] ) ) {
				$args['number'] = $newoptions['last_number'];
				unset( $newoptions['last_number'] );
			}

			if ( $options !== $newoptions ) {
				$options = $newoptions;
				$this->set_opts( $options );
			}
		}

		$widget_num = ( -1 === $args['number'] ) ? '%i%' : $args['number'];

		$count = $this->widget_count_enabled( $widget_num ) ? 'checked="checked"' : '';
		$title = $this->widget_title( $widget_num );

		wp_nonce_field( 'flexo-archives-widget-options' );
		?>
	<p><label for="flexo-title"><?php _e( 'Title:', 'flexo-archives' ); ?> <input style="width: 90%;" id="flexo-title" name="flexo-archives[<?php echo $widget_num; ?>][flexo-title]" type="text" value="<?php echo $title; ?>" /></label></p>
	<p style="text-align:right;margin-right:40px;"><label for="flexo-count"><?php _e( 'Show post counts', 'flexo-archives' ); ?> <input class="checkbox" type="checkbox" <?php echo $count; ?> id="flexo-count" name="flexo-archives[<?php echo $widget_num; ?>][flexo-count]"/></label></p>
	<input type="hidden" id="flexo-submit" name="flexo-archives[<?php echo $widget_num; ?>][flexo-submit]" value="1" />
		<?php
	}

	/**
	 * Helper function that Adds rel="nofollow" to links in $text
	 */
	public function add_link_nofollow( $text ) {
		return preg_replace_callback( '|<a (.*?)>|i', array( &$this, 'add_link_nofollow_cb' ), $text );
	}

	/**
	 * Callback used to add rel="nofollow" to HTML A element.
	 */
	public function add_link_nofollow_cb( $matches ) {
		$text = $matches[1];
		$text = str_replace( array( ' rel="nofollow"', " rel='nofollow'" ), '', $text );
		return "<a $text rel=\"nofollow\">";
	}

	/**
	 * Helper function to get yearly post totals.
	 *
	 * Returns: An array. Array keys are years, and array values are the
	 * number of posts posted that year. The array is empty on failure.
	 */
	public function year_post_totals() {
		global $wpdb;

		// Support archive filters other plugins may have inserted
		$join          = apply_filters( 'getarchives_join', '' );
		$default_where = "WHERE post_type='post' AND post_status='publish'";
		$where         = apply_filters( 'getarchives_where', $default_where );

		$totals_qstr  = 'SELECT YEAR(post_date) AS `year`, ';
		$totals_qstr .= 'COUNT(YEAR(post_date)) AS `total` ';
		$totals_qstr .= "FROM $wpdb->posts ";
		$totals_qstr .= $join . ' ';
		$totals_qstr .= $where;
		$totals_qstr .= ' GROUP BY YEAR(post_date)';

		$totals_array = array();

		$totals_result = $wpdb->get_results( $totals_qstr );
		if ( $totals_result ) {
			foreach ( $totals_result as $a_result ) {
				$totals_array[ $a_result->year ] = $a_result->total;
			}
		}

		return $totals_array;
	}

	/**
	 * Perform database query to get archives. Archives are sorted in
	 * *descending* order or year and *ascending* order of month
	 *
	 * Returns: result of query if successful, null otherwise
	 */
	public function query_archives() {
		global $wpdb;

		// Support archive filters other plugins may have inserted
		$join          = apply_filters( 'getarchives_join', '' );
		$default_where = "WHERE post_type='post' AND post_status='publish'";
		$where         = apply_filters( 'getarchives_where', $default_where );

		// Query string
		$qstring  = 'SELECT DISTINCT YEAR(post_date) AS `year`,';
		$qstring .= ' MONTH(post_date) AS `month`,';
		$qstring .= " count(ID) AS posts FROM $wpdb->posts ";
		$qstring .= $join . ' ';
		$qstring .= $where;
		$qstring .= ' GROUP BY YEAR(post_date), MONTH(post_date)';
		$qstring .= ' ORDER BY YEAR(post_date) DESC, MONTH(post_date) ';
		$qstring .= $this->month_order();

		// Query database
		$flexo_results = $wpdb->get_results( $qstring );

		// Check we actually got results
		if ( $flexo_results ) {
			return $flexo_results;
		} else {
			// No results or database error
			return null;
		}
	}

	/**
	 * Constructs the nested unordered lists from data obtained from
	 * the database.
	 *
	 * Args:
	 * $count: Boolean. Show per-month post counts.
	 * $total: Boolean. Show per-year post totals.
	 *
	 * Returns: An HTML fragment containing the archives lists
	 */
	public function build_archives_list( $count = false, $total = false ) {
		global $wp_locale;
		$list_html    = '';
		$totals_array = null;

		// Whether we should add rel="nofollow"
		$nofollow = $this->nofollow_enabled();

		// If yearly totals are enabled, get totals from database
		if ( $total ) {
			$totals_array = $this->year_post_totals();
		}

		// If we collapse complete decades
		$collapse_decades = $this->collapse_decades();

		// Get archives from database
		$results = $this->query_archives();

		// Log and retrun an error if query failed.
		if ( is_null( $results ) ) {
			$error_str = __( 'Database query unexpectedly failed.', 'flexo-archives' );
			error_log( __( 'ERROR: ', 'flexo-archives' ) . __FILE__ . '(' . __LINE__ . ') ' . $error_str );
			return "<p>$error_str</p>";
		}

		// create nested array
		$years = array();
		foreach ( $results as $result ) {
			$years[ $result->year ][] = array( $result->month, $result->posts );
		}

		end( $years );
		$end_of_years = key( $years );
		reset( $years );
		$start_of_years = key( $years );
		// translators: year archive text
		$link_title   = __( 'Year %s archives', 'flexo-archives' );
		$decade_title = __( 'Decade %s archives', 'flexo-archives' );
		$decade_start = false;
		$decade_end   = false;

		// Loop over results and print our archive lists
		foreach ( $years as $year => $months ) {
			if ( true === $collapse_decades && '9' === substr( $year, -1 ) && $year !== $start_of_years ) {
				$decade_start = true;
				$decade_end   = true;
			}

			if ( true === $decade_start ) {
				$decade_span = ( $year - 9 ) . ' - ' . $year;
				$title = 'title="' . sprintf( $decade_title, ( $year - 9 ) . '-' . $year ) . '">';
				$list_html   .= '<ul><li><a href="#" class="flexo-decade-link" ' . $title . $decade_span . '</a>';
				$decade_start = false;
			}
			if ( true === $decade_end ) {
				$list_html .= '<ul class="flexo-list">';
			} else {
				$list_html .= '<ul>';
			}

			$year_link  = '<a href="%s" class="flexo-link" ';
			$year_link .= 'title="' . $link_title . '" >';
			if ( false === $total ) {
				$year_link .= '%s</a>';
			} else {
				$yr_total = 0;
				foreach ( $months as $month ) {
					$yr_total = $yr_total + $month[1];
				}
				$year_link .= "%s</a> ( $yr_total )";
			}

			$year_link = sprintf( $year_link, get_year_link( $year ), $year, $year );

			$list_html .= '<li>' . $year_link . $this->ul_ify( $months, $year, $count ) . '</li>';

			if ( ( '0' === substr( $year, -1 ) && true === $decade_end ) || ( true === $collapse_decades && $end_of_years === $year ) ) {
				$list_html .= '</ul></li></ul>';
				$decade_end = false;
			} else {
				$list_html .= '</ul>';
			}

			if ( $nofollow ) {
				$list_html = $this->add_link_nofollow( $list_html );
			}
		}

		return $list_html;
	}

	// create month lists
	public function ul_ify( $months, $year, $count ) {
		global $wp_locale;
		$month_html = '<ul class="flexo-list">';
		foreach ( $months as $month ) {
			$before = '';
			$after  = '';

			$url  = get_month_link( $year, $month[0] );
			$text = $wp_locale->get_month( $month[0] );

			// Append number of posts in month, if they want it
			if ( $count ) {
				$after = '&nbsp;(' . $month[1] . ')' . $after;
			}

			$month_html .= get_archives_link( $url, $text, 'html', $before, $after );
		}
		$month_html .= '</ul>';
		return $month_html;
	}

	/**
	 * Output the archive list as a sidebar widget
	 *
	 * Arguments: $args array passed by WordPress's widgetized
	 * sidebar code
	 */
	public function widget_archives( $args ) {
		$args = wp_parse_args( $args );

		// Fetch widget options
		$args['widget_num'] = (int) str_replace( 'flexo-archives-', '', $args['widget_id'] );

		$title         = $this->widget_title( $args['widget_num'] );
		$count         = $this->widget_count_enabled( $args['widget_num'] );
		$yearly_totals = $this->yearly_total_enabled();

		// Print out the title
		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];

		// Print out the archive list
		echo $this->build_archives_list( $count, $yearly_totals );

		// Close out the widget
		echo $args['after_widget'];
	}

	/**
	 * Helper function that prints the url for our javascript
	 */
	public function script_url() {
		$url = plugins_url() . '/' . FLEXODIR;

		if ( $this->animation_enabled() ) {
			$url .= $this->flexo_anim_js;
		} else {
			$url .= $this->flexo_js;
		}

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return $url;
		} else {
			return str_replace( '.js', '.min.js', $url );
		}
	}

	/**
	 * Register our widgets with the widget system
	 */
	public function register_widgets() {
		$name       = __( 'Flexo Archives', 'flexo-archives' );
		$desc       = __( 'Your archives as an expandable list of years', 'flexo-archives' );
		$widget_cb  = array( &$this, 'widget_archives' );
		$control_cb = array( &$this, 'widget_control' );
		$css_class  = 'flexo';
		$id_base    = 'flexo-archives';

		// Tell the dynamic sidebar about our widget(s)
		if ( function_exists( 'wp_register_sidebar_widget' ) ) {
			$widget_ops  = array(
				'class'       => $css_class,
				'description' => $desc,
			);
			$control_ops = array(
				'width'   => 250,
				'height'  => 100,
				'id_base' => $id_base,
			);
			$id          = 'flexo-archives'; // Never never never translate an id

			$widgets_registered = 0;
			foreach ( array_keys( $this->options ) as $widget_num ) {
				if ( ! is_numeric( $widget_num ) ) {
					continue;
				}

				$id_str = $id . '-' . $widget_num;
				wp_register_sidebar_widget(
					$id_str,
					$name,
					$widget_cb,
					$widget_ops,
					array( 'number' => $widget_num )
				);
				wp_register_widget_control(
					$id_str,
					$name,
					$control_cb,
					$control_ops,
					array( 'number' => $widget_num )
				);
				$widgets_registered++;
			}

			if ( 0 === $widgets_registered ) {
				$id_str = $id . '-1';
				wp_register_sidebar_widget(
					$id_str,
					$name,
					$widget_cb,
					$widget_ops,
					array( 'number' => '1' )
				);
				wp_register_widget_control(
					$id_str,
					$name,
					$control_cb,
					$control_ops,
					array( 'number' => '1' )
				);
			}
		}

		// Add CSS and JavaScript to header if we're active
		if ( ! is_admin() && ! wp_is_mobile() && is_active_widget( array( &$this, 'widget_archives' ) ) ) {
			wp_enqueue_script( 'jquery' );
			wp_register_script( 'flexo', $this->script_url(), array( 'jquery' ), '3.0', true );
			wp_enqueue_script( 'flexo' );
		}
	}

	/**
	 * On activation, register our uninstall hook
	 */
	static function activate() {
		register_uninstall_hook( FLEXOPLUGIN, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Uninstall Function. Deletes plugin configuration from the database
	 */
	static function uninstall() {
		$options = $this->get_opts();

		if ( is_array( $options ) ) {
			delete_option( $this->options_name );
		}
	}
}
