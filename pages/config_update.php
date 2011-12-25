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

form_security_validate( 'plugin_Dashboard_config_update' );

$f_width = gpc_get_int ( 'width', 4 );
$f_reset_width = gpc_get_bool( 'reset-width', false );

if ( $f_reset_width ) {
    plugin_config_delete( 'width' );
} else {
    plugin_config_set( 'width', $f_width );
}

$f_filter = gpc_get_int_array ( 'filter', array() );
$f_reset_boxes = gpc_get_bool( 'reset-boxes', false );

if ( $f_reset_boxes ) {
	plugin_config_delete( 'boxes' );
} else {
	$f_boxes = array();
	foreach ( $f_filter as $t_filter_id => $t_filter_pos ) {
		if ( $t_filter_pos != 0 ) $f_boxes[$t_filter_id] = $t_filter_pos;
	}
	plugin_config_set( 'boxes', $f_boxes );
}

form_security_purge( 'plugin_Dashboard_config_update' );
print_successful_redirect( plugin_page( 'dashboard', true ) );