<?php
/**
 * WP GooDoc
 *
 * @package       GOODOC
 * @author        Dzmitry Makarski
 * @license       gplv3
 * @version       0.0.1
 *
 * @wordpress-plugin
 * Plugin Name:   WP GooDoc
 * Plugin URI:    https://example.com
 * Description:   Plugin to import Google Documents into a WordPress site
 * Version:       0.0.1
 * Author:        Dzmitry Makarski
 * Author URI:    https://example.com
 * Text Domain:   wp-goodoc
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with WP GooDoc. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

// Plugin name
define('GOODOC_NAME', 'WP GooDoc');

// Plugin version
define('GOODOC_VERSION', '0.0.1');

// Plugin Root File
define('GOODOC_PLUGIN_FILE', __FILE__);

// Plugin base
define('GOODOC_PLUGIN_BASE', plugin_basename(GOODOC_PLUGIN_FILE));

// Plugin Folder Path
define('GOODOC_PLUGIN_DIR', plugin_dir_path(GOODOC_PLUGIN_FILE));

// Plugin Folder URL
define('GOODOC_PLUGIN_URL', plugin_dir_url(GOODOC_PLUGIN_FILE));

/**
 * Load the main class for the core functionality
 */
require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Init.php';

/**
 * The main function to load the only instance
 * of our primary class.
 *
 * @return  object|GOODOC_Init
 * @since   0.0.1
 * @author  Dzmitry Makarski
 */

function WP_GOODOC(): GOODOC_Init {
    return new GOODOC_Init();
}

WP_GOODOC();