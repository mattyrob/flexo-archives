<?php
/*
Plugin Name: Flexo Archives
Description: Displays archives as a list of years that expand when clicked
Author: Matthew Robinson and originally Heath Harrelson
Version: 3.0

*/

/*
 * This is updated and improved from:
 * Flexo Archives Widget by Heath Harrelson, Copyright (C) 2011
 * http://wordpress.org/extend/plugins/flexo-archives-widget/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 */

/**
 * Output the archive lists as a standalone function, for users
 * can't or don't want to use the widget.
 */
function flexo_standalone_archives() {
	$archives = new FlexoArchives();

	if ( $archives->standalone_enabled() ) {
		echo $archives->build_archives_list(
			$archives->standalone_count_enabled(),
			$archives->yearly_total_enabled()
		);
	}
}

global $flexo_archives;
define( 'FLEXOPATH', trailingslashit( dirname( __FILE__ ) ) );
define( 'FLEXODIR', trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );

require_once FLEXOPATH . 'classes/class-flexoarchives.php';
$flexo_archives = new FlexoArchives();
