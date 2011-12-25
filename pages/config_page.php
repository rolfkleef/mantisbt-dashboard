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

auth_reauthenticate();
access_ensure_global_level( plugin_config_get( 'manage_threshold' ) );

html_page_top1( plugin_lang_get( 'title' ) );
html_page_top2();

print_manage_menu();

$t_width = plugin_config_get( 'width' );
$t_boxes = plugin_config_get( 'boxes' );
$t_filters = filter_db_get_available_queries();

foreach ( $t_filters as $t_filter_id => $t_filter_name ) {
	if ( !array_key_exists($t_filter_id, $t_boxes)) $t_boxes[$t_filter_id] = 0;
}

?>
<h1><?php echo plugin_lang_get( 'title' ) ?></h1>
<form action="<?php echo plugin_page( 'config_update' ) ?>" method="post">
<?php echo form_security_field( 'plugin_Dashboard_config_update' ) ?>

<p>
<label><?php echo plugin_lang_get('number_of_boxes') ?> <input name="width" size=1 value="<?php echo string_attribute( $t_width ) ?>"/></label>
<label><input type="checkbox" name="reset-width"/> <?php echo plugin_lang_get('reset_number_of_boxes') ?></label>
</p>

<p><?php echo plugin_lang_get('order_filters_shown') ?></p>

<ul>
<?php
foreach ( $t_filters as $t_filter_id => $t_filter_name ) {
?>
<li><label><input name="filter[<?php echo $t_filter_id ?>]" size=1 value="<?php echo $t_boxes[$t_filter_id] ?>"/> <?php echo $t_filter_name ?></label></li>
<?php
}
?>
</ul>

<p>
<label><input type="checkbox" name="reset-boxes"/> <?php echo plugin_lang_get('reset_order_of_boxes') ?></label>
</p>

<p>
<input type="submit" value="<?php echo plugin_lang_get( 'update_configuration' ) ?>"/>
</p>
</form>

<?php
html_page_bottom1( __FILE__ );
