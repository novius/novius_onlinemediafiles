<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

namespace Novius\OnlineMediaFiles\Migrations;

/**
 * Class C2d
 * @package Novius\OnlineMediaFiles\Migrations
 * A migration class that migrate your onlinemediafiles tables to the new tables name
 */
class C2d extends \Nos\Migration
{
    public function __construct($path)
    {
        if (!\DBUtil::table_exists('novius_onlinemediafiles'))
        {
            //The first migration was executed before
            self::executeSqlFile(__DIR__.DIRECTORY_SEPARATOR.'001_install.sql');

            // Migrate the old data into the new table
            if (\DBUtil::table_exists('onlinemediafiles'))
            {
                $sql = 'INSERT INTO novius_onlinemediafiles SELECT * FROM onlinemediafiles;';
                \Db::query($sql)->execute();
                \DBUtil::drop_table('onlinemediafiles');

                $sql = 'INSERT INTO novius_onlinemediafiles_folder SELECT * FROM onlinemediafiles_folder;';
                \Db::query($sql)->execute();
                \DBUtil::drop_table('onlinemediafiles_folder');

                $sql = 'INSERT INTO novius_onlinemediafiles_link SELECT * FROM onlinemediafiles_link;';
                \Db::query($sql)->execute();
                \DBUtil::drop_table('onlinemediafiles_link');
            }
        }
    }
}
