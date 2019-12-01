<?php
class FlexoArchives_Upgrade {
	/**
	 * Convert old-style options to global or widget options for
	 * the multi-widget version as appropriate.
	 */
	public function convert_old_opts() {
		$old = get_option( $this->old_options_name );

		// widget options
		$widget_opts = array(
			$this->opt_wtitle => $old[ $this->opt_wtitle ],
			$this->opt_count  => $old[ $this->opt_count ],
		);

		$this->replace_old_widget_id();
		$this->options = array( $this->opt_converted => $widget_opts );

		// global options
		if ( isset( $old[ $this->opt_standalone ] ) ) {
			$this->options[ $this->opt_standalone ] = $old[ $this->opt_standalone ];
		}

		if ( isset( $old[ $this->opt_animate ] ) ) {
			$this->options[ $this->opt_animate ] = $old[ $this->opt_animate ];
		}

		// save converted options and clean up
		delete_option( $this->old_options_name );
		update_option( $this->options_name, $this->options );
	}

	/**
	 * Converts the name of the old single widget in the sidebar
	 * settings. Unless this is done, the widget will disappear
	 * from the sidebar and its settings are lost. Converting the
	 * name will keep the widget working for existing users when
	 * upgrading to multi-widget capability.
	 */
	public function replace_old_widget_id() {
		$replacement_id = 'flexo-archives-' . $this->opt_converted;

		$sidebars = get_option( 'sidebars_widgets' );

		$modified_sidebar_key = false;
		$modified_sidebar_arr = false;
		$modified             = false;

		// bail if db fetch failed
		if ( ! is_array( $sidebars ) ) {
			return;
		}

		// iterate the sidebars and replace the widget id of the old version
		// $sidebars is a mixed array, where keys mostly point to arrays
		foreach ( $sidebars as $sidebar => $widgets ) {
			// skip non-array elements
			if ( ! is_array( $widgets ) ) {
				continue;
			}

			// iterate arrays found; one for each sidebar
			foreach ( $widgets as $widget_index => $widget_id ) {
				// found the only old-style widget
				if ( 'flexo-archives' === $widget_id ) {
					$modified_sidebar_key = $sidebar;
					$modified_sidebar_arr = $widgets;

					$modified_sidebar_arr[ $widget_index ] = $replacement_id;

					$modified = true;
					break 2; // break out of both foreach loops
				}
			}
		}

		// save
		if ( $modified ) {
			$sidebars[ $modified_sidebar_key ] = $modified_sidebar_arr;
			update_option( 'sidebars_widgets', $sidebars );
		}
	}
}
