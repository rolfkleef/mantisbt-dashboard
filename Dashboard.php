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

class DashboardPlugin extends MantisPlugin {
	function register() {
		$this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );
		$this->page = 'config_page';

		$this->version = '0.1';     # Plugin version string
		$this->requires = array(    # Plugin dependencies, array of basename => version pairs
            'MantisCore' => '1.2.0, >= 1.2.0',  #   Should always depend on an appropriate version of MantisBT
		);

		$this->author = 'Rolf Kleef';         # Author/team name
		$this->contact = 'rolf@drostan.org';        # Author/team e-mail address
		$this->url = '';            # Support webpage
	}

	function config() {
		return array(
			'width' => 4,
			'boxes' => array(),
			'manage_threshold'	=> ADMINISTRATOR,
		);
	}

    function hooks() {
        return array(
            'EVENT_MENU_MAIN_FRONT' => 'addMainMenu',
        );
    }

    function addMainMenu( $p_event ) {
        return array (
        	'<a href="' . plugin_page( 'dashboard' ) . '">' . plugin_lang_get( 'title' ) .'</a>'
        );
    }

}