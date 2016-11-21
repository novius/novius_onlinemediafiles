<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

$media_icon = function () {
    return function ($item) {
        return $item->onme_thumbnail ? $item->onme_thumbnail : '';
    };
};

return array(
    'i18n' => array(
        // Crud
        'notification item added' => __('Item added !'),
        'notification item deleted' => __('Media deleted !'),

        // General errors
        'notification item does not exist anymore' => __('This media file doesn’t exist any more. It has been deleted.'),
        'notification item not found' => __('We cannot find this media file.'),

        // Deletion popup
        'deleting item title' => __('Deleting the media ‘{{title}}’'),

        # Delete action's labels
        'deleting button 1 item' => __('Yes, delete this media file'),
    ),
    'data_mapping' => array(
        'title' => array(
            'column' => 'onme_title',
            'title' => __('Title'),
            '' => '',
            'cellFormatters' => array(
                'icon' => array(
                    'type' => 'icon',
                    'column' => 'icon',
                    'size' => 16,
                ),
            ),
        ),
        'thumbnailAlternate' => array(
            'value' => $media_icon(64),
        ),
        'icon' => array(
            'value' => $media_icon(16),
        ),
    ),
    'actions' => array(
        'add' => array(
            'label' => __('Add a media file'),
            'visible' => array(
                'check_permission' => array('Novius\OnlineMediaFiles\Permission', 'checkMediaVisible'),
            ),
            'disabled' => array(
                'check_draft' => array('Novius\OnlineMediaFiles\Permission', 'checkPermissionDraft'),
            ),
        ),
        'edit' => array(
            'disabled' => array(
                'check_draft' => array('Novius\OnlineMediaFiles\Permission', 'checkPermissionDraft'),
                'check_folder_restriction' => array('Novius\OnlineMediaFiles\Permission', 'isMediaInRestrictedFolder'),
            ),
        ),
        'delete' => array(
            'disabled' => array(
                'check_draft' => array('Novius\OnlineMediaFiles\Permission', 'checkPermissionDraft'),
                'check_folder_restriction' => array('Novius\OnlineMediaFiles\Permission', 'isMediaInRestrictedFolder'),
            ),
        ),
    ),
);