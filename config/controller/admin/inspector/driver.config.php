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

$data = array();
foreach ($config['drivers'] as $driver_name) {
	$driver_class = \Novius\OnlineMediaFiles\Driver::buildDriverClass($driver_name);

	// Loads the driver's custom config
	list($application, $file) = \Config::configFile($driver_class);
	$driver_config = \Config::load($application . '::' . $file, true);

    // Merges with common config
	$driver_config = \Arr::merge($common_config, (array) $driver_config);


	$data[] = array(
		'id' 	=> $driver_name,
		'title' => \Arr::get($driver_config, 'name', $driver_name),
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
                if (in_array($driver, $value)) {
                    $ext[] = $driver;
                } else {
                    $other[] = $driver;
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
