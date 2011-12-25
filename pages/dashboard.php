<?php
# MantisBT Dashboard Plugin
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

	require_once( 'core.php' );

	require_once( 'compress_api.php' );
	require_once( 'filter_api.php' );
	require_once( 'last_visited_api.php' );

	auth_ensure_user_authenticated();

	$t_current_user_id = auth_get_current_user_id();

	# Improve performance by caching category data in one pass
	category_get_all_rows( helper_get_current_project() );

	compress_enable();

	# don't index my view page
	html_robots_noindex();

	html_page_top1( plugin_lang_get( 'title' ) );

	if ( current_user_get_pref( 'refresh_delay' ) > 0 ) {
		html_meta_redirect( plugin_page( 'dashboard'), current_user_get_pref( 'refresh_delay' )*60 );
	}

	html_page_top2();

	print_recently_visited();

	$f_page_number		= gpc_get_int( 'page_number', 1 );

	$t_per_page = config_get( 'my_view_bug_count' );
	$t_bug_count = null;
	$t_page_count = null;

	$t_filters = filter_db_get_available_queries();
	$t_config_boxes = plugin_config_get( 'boxes' );
	$t_boxes = array();

	if ( is_array($t_config_boxes) && (sizeof($t_config_boxes) >0) ) {
		$t_result = asort($t_config_boxes);

		foreach ( $t_config_boxes as $t_filter_id => $t_box_position ) {
			if ( ($t_box_position > 0) && array_key_exists($t_filter_id, $t_filters)) {
				$t_boxes[$t_filter_id] = $t_filters[$t_filter_id];
			}
		}
	} else {
		// Show all filters
		$t_boxes = $t_filters;
	}

	$t_project_id = helper_get_current_project();
?>

<div align="center">
<?php
	$t_status_legend_position = config_get( 'status_legend_position' );

	if ( $t_status_legend_position == STATUS_LEGEND_POSITION_TOP || $t_status_legend_position == STATUS_LEGEND_POSITION_BOTH ) {
		html_status_legend();
		echo '<br />';
	}
?>
<table class="hide" border="0" cellspacing="3" cellpadding="0">

<?php
	$t_number_of_boxes = count ( $t_boxes );
	$t_boxes_position = config_get( 'my_view_boxes_fixed_position' );
	$t_counter = 0;
	# determine width of view in number of boxes, hard-code default to 2 columns
	$t_boxes_width = plugin_config_get( 'width' );
	# determine width of each box/table cell in percentage
	$t_td_width = (int) 100/$t_boxes_width;
	$t_counter = 0;

	while (list ($t_box_id, $t_box_title) = each ($t_boxes)) {
		$t_counter++;

		# check the style of displaying boxes - fixed (ie. each box in a separate table cell) or not
		if ( ON == $t_boxes_position ) {
			# for "first" box number: start new row
			if ( 1 == $t_counter%$t_boxes_width ) {
				echo '<tr>';
			}

			echo '<td valign="top" width="'.$t_td_width.'%">';
				include( $t_plugin_path . 'Dashboard' . DIRECTORY_SEPARATOR . 'box_inc.php' );
			echo '</td>';

			# for "last" box on a row: end the row
			if ( 0 == $t_counter%$t_boxes_width ) {
				echo '</tr>';
			}
		}
		else if ( OFF == $t_boxes_position ) {
			# start new table row and column for first box
			if ( 1 == $t_counter ) {
				echo '<tr><td valign="top" width="'.$t_td_width.'%">';
			}

			# start new table column for the second half of boxes
			if ( $t_counter == ceil ($t_number_of_boxes/$t_boxes_width) + 1 ) {
				echo '<td valign="top" width="'.$t_td_width.'%">';
			}

			# display the required box
			include 'my_view_inc.php';
			echo '<br />';

			# close the first column for first half of boxes
			if ( $t_counter == ceil ($t_number_of_boxes/$t_boxes_width) ) {
				echo '</td>';
			}
		}
	}


	# Close the box groups depending on the layout mode and whether an empty cell
	# is required to pad the number of cells in the last row to the full width of
	# the table.
	if ( ON == $t_boxes_position && $t_counter == $t_number_of_boxes && 1 == $t_counter%3 ) {
		echo '<td valign="top" width="'.$t_td_width.'%"></td></tr>';
	} else if ( OFF == $t_boxes_position && $t_counter == $t_number_of_boxes ) {
		echo '</td></tr>';
	}

?>

</table>
<?php
	if ( $t_status_legend_position == STATUS_LEGEND_POSITION_BOTTOM || $t_status_legend_position == STATUS_LEGEND_POSITION_BOTH ) {
		html_status_legend();
	}
?>
</div>

<?php
	html_page_bottom();
