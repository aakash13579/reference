<?php
/**
 * My Plugin functions
 *
 * @package My Plugin/Functions
 * @since 1.0
 */

defined( 'ABSPATH' ) || exit;

// Check plugin's requirements.
function mp_check_requirements() {
	return Mp_Check_Requirements::instance();
}