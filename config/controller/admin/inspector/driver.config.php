<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

// Get the available drivers
$config = \Config::load('novius_onlinemediafiles::config', true);

// Load the drivers common config
list($application, $file) = \Config::configFile('\Novius\OnlineMediaFiles\Driver');
$common_config = \Config::load($application . '::' . $file, true);
if (!is_array($common_config)) {
	$common_config = array();
}

// Get drivers count
$count = \DB::query(sprintf(
    'SELECT `onme_driver_name`, COUNT(onme_id) AS count FROM `%s` GROUP BY `onme_driver_name',
    \Novius\OnlineMediaFiles\Model_Media::table()
))->execute()->as_array();
foreach ($count as $k => $row) {
    $driver_class = \Novius\OnlineMediaFiles\Driver::buildDriverClass(\Arr::get($row, 'onme_driver_name'));
    $count[$driver_class] = \Arr::get($row, 'count', 0);
}
//$count = \Arr::pluck($count, 'count', 'onme_driver_name');

// Build data
$data = array();
foreach ($config['drivers'] as $driver_name) {
	$driver_class = \Novius\OnlineMediaFiles\Driver::buildDriverClass($driver_name);

	// Loads the driver's custom config
	list($application, $file) = \Config::configFile($driver_class);
	$driver_config = \Config::load($application . '::' . $file, true);

    // Merges with common config
	$driver_config = \Arr::merge($common_config, (array) $driver_config);

    // Title with count
    $title = \Arr::get($driver_config, 'name', $driver_name);
    $title .= sprintf(' (%d)', \Arr::get($count, $driver_class, 0));

	$data[] = array(
		'id' 	=> $driver_name,
		'title' => $title,
		'icon' 	=> \Arr::get($driver_config, 'icon.16'),
	);
}

return array(
    'data' => $data,
    'input' => array(
        'key' => 'onme_driver_name',
        'query' => function ($value, $query) {
            $ext = array();
            $other = array();
            $value = (array) $value;

			$config = \Config::load('novius_onlinemediafiles::config', true);
            foreach ($config['drivers'] as $driver) {
                $driver_class = \Novius\OnlineMediaFiles\Driver::buildDriverClass($driver);
                $driver_name = \Novius\OnlineMediaFiles\Driver::buildDriverName($driver_class);
                if (in_array($driver, $value)) {
                    $ext[] = $driver_name;
                } else {
                    $other[] = $driver_name;
                }
            }
            $opened = false;
            if (!empty($ext)) {
                $opened or $query->and_where_open();
                $opened = true;
                $query->or_where(array('onme_driver_name', 'IN', $ext));
            }
            if (in_array('other', $value)) {
                $opened or $query->and_where_open();
                $opened = true;
                $query->or_where(array('onme_driver_name', 'NOT IN', $other));
            }
            $opened and $query->and_where_close();

            return $query;
        },
    ),
    'appdesk' => array(
        'vertical' => true,
        'label' => __('Types'),
        'url' => 'admin/novius_onlinemediafiles/inspector/driver/list',
        'inputName' => 'onme_driver_name[]',
        'grid' => array(
            'columns' => array(
                'title' => array(
                    'headerText' => __('Types'),
                    'dataKey' => 'title',
                ),
                'id' => array(
                    'visible' => false,
                ),
                'icon' => array(
                    'visible' => false,
                ),
            ),
        ),
    ),
);
