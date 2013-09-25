/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
 
CREATE TABLE IF NOT EXISTS `onlinemediafiles` (
  `onme_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `onme_folder_id` int(10) unsigned NOT NULL,
  `onme_title` varchar(255) NOT NULL,
  `onme_description` text,
  `onme_url` varchar(255) NOT NULL,
  `onme_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `onme_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `onme_metadatas` text,
  `onme_thumbnail` varchar(255) DEFAULT NULL,
  `onme_driver_name` varchar(100) NOT NULL,
  PRIMARY KEY (`onme_id`),
  KEY `onme_folder_id` (`onme_folder_id`)
) DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `onlinemediafiles_folder` (
  `onfo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `onfo_parent_id` int(10) unsigned DEFAULT NULL,
  `onfo_title` varchar(100) NOT NULL,
  `onfo_path` varchar(255) NOT NULL,
  `onfo_dir_name` varchar(50) DEFAULT NULL,
  `onfo_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `onfo_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`onfo_id`),
  KEY `onfo_parent_id` (`onfo_parent_id`)
) DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `onlinemediafiles_link` (
  `onli_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `onli_from_table` varchar(255) NOT NULL,
  `onli_foreign_id` int(10) unsigned NOT NULL,
  `onli_key` varchar(30) NOT NULL,
  `onli_onme_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`onli_id`)
) DEFAULT CHARSET=utf8 ;
