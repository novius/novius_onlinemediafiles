/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

define(
    ['jquery-nos-appdesk'],
    function($) {
        "use strict";
        return function(appDesk) {
            return {
                appdesk : {
                    inspectors : {
                        driver : {
                            grid : {
                                columns : {
                                    title : {
                                        cellFormatter : function(args) {
                                            if ($.isPlainObject(args.row.data)) {
                                                var text = "";
                                                if (args.row.data.icon) {
                                                    text += "<img style=\"vertical-align:middle\" src=\"static/apps/novius_onlinemediafiles/icons/16/" + args.row.data.icon + "\"> ";
                                                }
                                                text += args.row.data.title;

                                                args.$container.html(text);

                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            };
        };
    }
);
