<?php
/**
 * Remove hidden preferences from the database.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * https://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @author TyA <tya.wiki@gmail.com>
 * @see https://phabricator.wikimedia.org/T32976
 * @ingroup Maintenance
 */

require_once __DIR__ . '/Maintenance.php';

/**
 * Maintenance script that removes hidden preferences from the database.
 *
 * @ingroup Maintenance
 */
class CleanupPreferences extends Maintenance {
	public function execute() {
		global $wgHiddenPrefs;

		$dbw = $this->getDB( DB_MASTER );
		$this->beginTransaction( $dbw, __METHOD__ );
		foreach ( $wgHiddenPrefs as $item ) {
			$dbw->delete(
				'user_properties',
				[ 'up_property' => $item ],
				__METHOD__
			);
		};
		$this->commitTransaction( $dbw, __METHOD__ );
		$this->output( "Finished!\n" );
	}
}

$maintClass = 'CleanupPreferences'; // Tells it to run the class
require_once RUN_MAINTENANCE_IF_MAIN;
