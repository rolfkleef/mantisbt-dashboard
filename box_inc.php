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

/**
 * requires current_user_api
 */
require_once( 'current_user_api.php' );
/**
 * requires bug_api
 */
 require_once( 'bug_api.php' );
/**
 * requires string_api
 */
require_once( 'string_api.php' );
/**
 * requires date_api
 */
require_once( 'date_api.php' );
/**
 * requires icon_api
 */
require_once( 'icon_api.php' );

$t_filter = current_user_get_bug_filter();
if( $t_filter === false ) {
	$t_filter = filter_get_default();
}

$t_sort = $t_filter['sort'];
$t_dir = $t_filter['dir'];

$t_icon_path = config_get( 'icon_path' );

$t_filter = filter_deserialize( filter_db_get_filter ($t_box_id ) );
$rows = filter_get_bug_rows( $f_page_number, $t_per_page, $t_page_count, $t_bug_count, $t_filter );

$t_fields = config_get( 'bug_view_page_fields' );
$t_fields = columns_filter_disabled( $t_fields );

# Improve performance by caching category data in one pass
if( helper_get_current_project() == 0 ) {
	$t_categories = array();
	foreach( $rows as $t_row ) {
		$t_categories[] = $t_row->category_id;
	}

	category_cache_array_rows( array_unique( $t_categories ) );
}

//$t_filter = array_merge( $c_filter[$t_box_title], $t_filter );

# -- ====================== BUG LIST ========================= --
?>

<table class="width100" cellspacing="1">
<?php
# -- Navigation header row --?>
<tr>
<?php
# -- Viewing range info --?>
	<td class="form-title" colspan="2">
<?php
print_link( 'view_all_set.php?type=3&source_query_id=' . $t_box_id, $t_box_title, false, 'subtle' );
echo '&#160;';
print_bracket_link( 'view_all_set.php?type=3&source_query_id=' . $t_box_id, '^', true, 'subtle' );

if( count( $rows ) > 0 ) {
	$v_start = $t_filter[FILTER_PROPERTY_ISSUES_PER_PAGE] * ( $f_page_number - 1 ) + 1;
	$v_end = $v_start + count( $rows ) - 1;
}
else {
	$v_start = 0;
	$v_end = 0;
}
echo "($v_start - $v_end / $t_bug_count)";
?>
	</td>
</tr>

<?php
# -- Loop over bug rows and create $v_* variables --
	$t_count = count( $rows );
	for( $i = 0;$i < $t_count; $i++ ) {
		$t_bug = $rows[$i];

	$t_summary = string_display_line_links( $t_bug->summary );
	$t_last_updated = date( config_get( 'normal_date_format' ), $t_bug->last_updated );

	$t_bug_due_date = $t_bug->__get( 'due_date' );
	if ( !date_is_null( $t_bug_due_date ) ) {
		$t_bug_due_date = date( config_get( 'normal_date_format' ), $t_bug_due_date );
	} else {
		$t_bug_due_date = '';
	}

	# choose color based on status
	$status_color = get_status_color( $t_bug->status );

	# grab the project name
	$project_name = project_get_field( $t_bug->project_id, 'name' );
	?>

<tr bgcolor="<?php echo $status_color?>">
	<?php
	# -- Bug ID and details link + Pencil shortcut --?>
	<td class="center" valign="top" width ="0" nowrap="nowrap">
		<span class="small">
		<?php
			print_bug_link( $t_bug->id );

	echo '<br />';

	if( ON == config_get( 'show_priority_text' ) ) {
		print_formatted_priority_string( $t_bug->status, $t_bug->priority );
	} else {
		print_status_icon( $t_bug->priority );
	}

	?>
		</span>
	</td>

	<?php
	$t_show_due_date = in_array( 'due_date', $t_fields ) && access_has_bug_level( config_get( 'due_date_view_threshold' ), $t_bug->id );

	if ( bug_is_overdue( $t_bug->id ) ) {
		$t_overdue = ' overdue';
	} else {
		$t_overdue = '';
	}

	# -- Summary --?>
	<td class="left<?php echo $t_overdue ?>" valign="top" width="100%">
		<span class="small">
		<?php
			echo $t_summary;

			if ( $t_show_due_date && ( $t_bug_due_date!='' ) ) {
				echo ': ' . $t_bug_due_date;
			}
		?>
		<br />
		<?php
		 	if( ON == config_get( 'show_bug_project_links' ) && helper_get_current_project() != $t_bug->project_id ) {
				echo '[', string_display_line( project_get_name( $t_bug->project_id ) ), '] ';
			}

			# type project name if viewing 'all projects' or bug is in subproject
			echo string_display_line( category_full_name( $t_bug->category_id, false, $t_bug->project_id ) );

			if( $t_bug->last_updated > strtotime( '-' . $t_filter[FILTER_PROPERTY_HIGHLIGHT_CHANGED] . ' hours' ) ) {
				echo ' - <b>' . $t_last_updated . '</b>';
			} else {
				echo ' - ' . $t_last_updated;
			}
	?>
		</span>
	</td>
</tr>
<?php
	# -- end of Repeating bug row --
}

# -- ====================== end of BUG LIST ========================= --
?>
</table>
<?php
// Free the memory allocated for the rows in this box since it is not longer needed.
unset( $rows );

