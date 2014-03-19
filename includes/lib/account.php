<?php
/*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2008  CAcert Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Function to recalculate the cached Assurer status
 *
 * @param int $userID
 * 	if the user ID is not given the flag will be recalculated for all users
 *
 * @return bool
 * 	false if there was an error on fixing the flag. This does NOT return the
 * 	new value of the flag
 */
function fix_assurer_flag($userID = NULL)
{
	// Update Assurer-Flag on users table if 100 points and CATS passed.
	//
	// We may have some performance issues here if no userID is given
	// there are ~150k assurances and ~220k users currently
	// but the exists-clause on cats_passed should be a good filter
	$sql = '
		UPDATE `users` AS `u` SET `assurer` = 1
		WHERE '.(
					($userID === NULL) ?
					'`u`.`assurer` = 0' :
					'`u`.`id` = \''.intval($userID).'\''
				).'
			AND EXISTS(
				SELECT 1 FROM `cats_passed` AS `cp`, `cats_variant` AS `cv`
				WHERE `cp`.`variant_id` = `cv`.`id`
					AND `cv`.`type_id` = 1
					AND `cp`.`user_id` = `u`.`id`
			)
			AND (
				SELECT SUM(`points`) FROM `notary` AS `n`
				WHERE `n`.`to` = `u`.`id`
					AND (`n`.`expire` > now()
					OR `n`.`expire` IS NULL)
			) >= 100';

	$query = mysql_query($sql);
	if (!$query) {
		return false;
	}
	// Challenge has been passed and non-expired points >= 100

	// Reset flag if requirements are not met
	//
	// Also a bit performance critical but assurer flag is only set on
	// ~5k accounts
	$sql = '
		UPDATE `users` AS `u` SET `assurer` = 0
		WHERE '.(
					($userID === NULL) ?
					'`u`.`assurer` <> 0' :
					'`u`.`id` = \''.intval($userID).'\''
				).'
			AND (
				NOT EXISTS(
					SELECT 1 FROM `cats_passed` AS `cp`,
						`cats_variant` AS `cv`
					WHERE `cp`.`variant_id` = `cv`.`id`
						AND `cv`.`type_id` = 1
						AND `cp`.`user_id` = `u`.`id`
				)
				OR (
					SELECT SUM(`points`) FROM `notary` AS `n`
					WHERE `n`.`to` = `u`.`id`
						AND (
							`n`.`expire` > now()
							OR `n`.`expire` IS NULL
						)
				) < 100
			)';

	$query = mysql_query($sql);
	if (!$query) {
		return false;
	}

	return true;
}


/**
 * Contains a map of all hash algorithms currently supported for signing.
 *
 * @var array(string=>string) identifier => display_string
 */
define("HASH_ALGORITHMS", array(
		"sha256" => "SHA256 "._("recommended, because the other algorithms might break on some older versions of the GnuTLS library (older than 3.x)."),
		"sha384" => "SHA384",
		"sha512" => "SHA512",
		));

/**
 * The identifier of the default hash algorithm used as found in HASH_ALGORITHMS
 *
 * @var string
 */
define("DEFAULT_HASH_ALGORITHM", "sha256");
