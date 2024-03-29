<?php
/**
 * WP GooDoc
 *
 * @package       GOODOC
 * @author        Damian Makarski
 * @license       gplv2
 * @version       0.2.0
 *
 * @wordpress-plugin
 * Plugin Name:   WP GooDoc
 * Plugin URI:    https://doc-r.com/
 * Description:   Plugin to import Google Documents into a WordPress site
 * Version:       0.2.0
 * Author:        Damian Makarski
 * Author URI:    https://www.buymeacoffee.com/mcarena
 * Text Domain:   goodoc
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with WP GooDoc. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin name
define( 'GOODOC_NAME', 'WP GooDoc' );

// Plugin version
define( 'GOODOC_VERSION', '0.2.0' );

// Plugin Root File
define( 'GOODOC_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'GOODOC_PLUGIN_BASE', plugin_basename( GOODOC_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'GOODOC_PLUGIN_DIR', plugin_dir_path( GOODOC_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'GOODOC_PLUGIN_URL', plugin_dir_url( GOODOC_PLUGIN_FILE ) );

// GooDOc Server URL
const GOODOC_SERVER_URL = 'https://doc-r.com';

// API URL
const GOODOC_API_URL = GOODOC_SERVER_URL . '/wp-json/api/v1/';

/**
 * Load the required classes
 */

//The plugin treats and classes
require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Logger.php';
require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Init.php';
require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Common_API.php';
require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Import.php';
require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Import_Page.php';
require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Settings.php';
require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Ajax.php';

/**
 * The main function to load the only instance
 * of our primary class.
 *
 * @return GOODOC_Classes\GOODOC_Init
 * @since   0.0.1
 * @author  Dzmitry Makarski
 */

function WP_GOODOC(): GOODOC_Classes\GOODOC_Init {
	return new GOODOC_Classes\GOODOC_Init();
}

WP_GOODOC();