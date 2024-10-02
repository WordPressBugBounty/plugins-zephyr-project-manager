<?php

/**
 * @package ZephyrProjectManager
 */

namespace ZephyrProjectManager\Base;

if (!defined('ABSPATH')) {
	die;
}

use ZephyrProjectManager\Base\BaseController;

class SettingsLinks {

	public function register() {
		add_filter('plugin_action_links_' . ZPM_PLUGIN, array($this, 'settings_links'));
	}

	public function settings_links($links) {
		/* translators: Settings link format for Zephyr Project Manager. %1$s: Opening anchor tag for admin settings URL, %2$s: Closing anchor tag */
		$settings_link = sprintf(esc_html('%1$s Settings %2$s', 'zephyr-project-manager'), '<a href="admin.php?page=zephyr_project_manager">', '</a>');

		/* translators: Purchase link format for Zephyr Project Manager Premium. %1$s: Opening anchor tag for purchase URL, %2$s: Closing anchor tag */
		$purchase_link = sprintf(esc_html('%1$s Purchase Premium %2$s', 'zephyr-project-manager'), '<a href="https://zephyr-one.com/purchase-pro">', '</a>');
		array_push($links, $settings_link);

		if (!BaseController::is_pro()) {
			array_push($links, $purchase_link);
		}

		return $links;
	}
}