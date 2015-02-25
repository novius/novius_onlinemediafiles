/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

ALTER TABLE  `novius_onlinemediafiles_link`
CHANGE  `onli_foreign_id`  `onli_foreign_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `novius_onlinemediafiles_link`
ADD  `onli_foreign_context_common_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER  `onli_foreign_id` ;

ALTER TABLE  `novius_onlinemediafiles_link`
ADD INDEX (  `onli_from_table` ,  `onli_foreign_id` ) ;

ALTER TABLE  `novius_onlinemediafiles_link`
ADD INDEX (  `onli_foreign_context_common_id` ,  `onli_from_table` ) ;
