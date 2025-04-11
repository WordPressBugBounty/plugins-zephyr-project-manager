<?php

/**
 * @package ZephyrProjectManager
 */

namespace ZephyrProjectManager\Core;

if (!defined('ABSPATH')) {
	die;
}

use ZephyrProjectManager\Core\Utillities;
use ZephyrProjectManager\Core\Projects;
use ZephyrProjectManager\Base\BaseController;
use ZephyrProjectManager\Api\ColorPickerApi;

class Categories {
	const SORT_ALPHA = 0;

	/**
	 * Creates a new category
	 */
	public static function create($args) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		$settings = array();
		$settings['name'] = (isset($args['name'])) ? sanitize_text_field($args['name']) : 'Untitled';
		$settings['description'] = (isset($args['description'])) ? sanitize_textarea_field($args['description']) : '';
		$settings['color'] 	= (isset($args['color'])) ? sanitize_text_field($args['color']) : false;

		if (ColorPickerApi::checkColor($settings['color']) !== false) {
			$settings['color'] = ColorPickerApi::sanitizeColor($settings['color']);
		} else {
			$settings['color'] = '#eee';
		}

		$wpdb->insert($table_name, $settings);
		return $wpdb->insert_id;
	}

	/**
	 * Updates category
	 */
	public static function update($id, $args) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		if (isset($args['color'])) {
			if (ColorPickerApi::checkColor($args['color']) !== false) {
				$args['color'] = ColorPickerApi::sanitizeColor($args['color']);
			} else {
				$args['color'] = '#eee';
			}
		}

		$where = array(
			'id' => $id
		);

		$wpdb->update($table_name, $args, $where);
		return $args;
	}

	/**
	 * Deletes a category
	 */
	public static function delete($id) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		$settings = array(
			'id' => $id
		);

		$wpdb->delete($table_name, $settings, ['%d']);
	}

	/**
	 * Retrieves all categories from the database
	 * @return object
	 */
	public static function fetch() {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;
		$categories = $wpdb->get_results("SELECT * FROM {$table_name}");
		return $categories;
	}

	/**
	 * Retrieves all categories
	 * @return object
	 */
	public static function get_categories() {
		$manager = ZephyrProjectManager();
		$categories = $manager::get_categories();
		return $categories;
	}

	/**
	 * Retrieves the data for a category from the database
	 * @param int $id The ID of the category to retrieve the data for
	 * @return object
	 */
	public static function fetch_category($id) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		if (!empty($id)) {
			$category = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
		} else {
			$category = null;
		}

		return $category;
	}

	/**
	 * Retrieves the data for a category from the global manager
	 * @param int $id The ID of the category to retrieve the data for
	 * @return object
	 */
	public static function get_category($id) {
		$manager = ZephyrProjectManager();
		$category = $manager::get_category($id);
		return $category;
	}

	/**
	 * Returns the total number of categories
	 * @return int
	 */
	public static function get_category_total() {
		$categories = Categories::get_categories();
		$category_count = sizeof($categories);
		return $category_count;
	}

	/**
	 * Displays a list of created categories
	 */
	public static function display_category_list() {
		return require_once(ZPM_PLUGIN_PATH . '/templates/parts/category_list.php');
	}

	public static function new_category_modal() {
?>
		<!-- New Category modal -->
		<div id="zpm_new_category_modal" class="zpm-modal" aria-modal="true" aria-hidden="true">
			<div class="zpm_create_category">
				<h3 class="zpm-modal-header"><?php esc_html_e('New Category', 'zephyr-project-manager'); ?></h3>

				<div class="zpm-form__group">
					<input type="text" name="zpm_category_name" id="zpm_category_name" class="zpm-form__field" placeholder="<?php esc_html_e('Name', 'zephyr-project-manager'); ?>">
					<label for="zpm_category_name" class="zpm-form__label"><?php esc_html_e('Name', 'zephyr-project-manager'); ?></label>
				</div>

				<div class="zpm-form__group">
					<textarea type="text" name="zpm_category_description" id="zpm_category_description" class="zpm-form__field" placeholder="<?php esc_html_e('Description', 'zephyr-project-manager'); ?>"></textarea>
					<label for="zpm_category_description" class="zpm-form__label"><?php esc_html_e('Description', 'zephyr-project-manager'); ?></label>
				</div>

				<label class="zpm_label" for="zpm_category_color"><?php esc_html_e('Color', 'zephyr-project-manager'); ?></label>
				<input type="text" id="zpm_category_color" class="zpm_input">
			</div>
			<button class="zpm_button" name="zpm_create_category" id="zpm_create_category"><?php esc_html_e('Create Category', 'zephyr-project-manager'); ?></button>
		</div>
	<?php
	}

	public static function new_status_modal() {
	?>
		<!-- New Status modal -->
		<div id="zpm_new_status_modal" class="zpm-modal" aria-modal="true" aria-hidden="true">
			<div class="zpm_create_category">
				<h3 class="zpm-modal-header"><?php esc_html_e('New Priority / Status', 'zephyr-project-manager'); ?></h3>

				<div class="zpm-form__group">
					<input type="text" name="zpm_status_name" id="zpm_status_name" class="zpm-form__field" placeholder="<?php esc_html_e('Name', 'zephyr-project-manager'); ?>">
					<label for="zpm_status_name" class="zpm-form__label"><?php esc_html_e('Name', 'zephyr-project-manager'); ?></label>
				</div>

				<label class="zpm_label" for="zpm_category_color"><?php esc_html_e('Color', 'zephyr-project-manager'); ?></label>
				<input type="text" id="zpm_status_color" class="zpm_input zpm-color-picker">
				<input id="zpm-status-type__new" type="hidden">
			</div>
			<button class="zpm_button" name="zpm_create_status" id="zpm_create_status"><?php esc_html_e('Create', 'zephyr-project-manager'); ?></button>
		</div>
	<?php
	}

	public static function edit_status_modal() {
	?>
		<!-- Edit Status modal -->
		<div id="zpm_edit_status_modal" class="zpm-modal" aria-modal="true" aria-hidden="true">
			<div class="zpm_create_category">
				<h3 class="zpm-modal-header"><?php esc_html_e('Edit Priority / Status', 'zephyr-project-manager'); ?></h3>

				<input type="hidden" id="zpm-edit-status-id" />
				<div class="zpm-form__group">
					<input type="text" name="zpm_status_name" id="zpm_edit_status_name" class="zpm-form__field" placeholder="<?php esc_html_e('Name', 'zephyr-project-manager'); ?>">
					<label for="zpm_status_name" class="zpm-form__label"><?php esc_html_e('Name', 'zephyr-project-manager'); ?></label>
				</div>

				<label class="zpm_label" for="zpm_category_color"><?php esc_html_e('Color', 'zephyr-project-manager'); ?></label>
				<input type="text" id="zpm_edit_status_color" class="zpm_input zpm-color-picker">
				<input id="zpm-status-type__edit" type="hidden">
			</div>
			<button class="zpm_button" name="zpm_create_status" id="zpm_edit_status"><?php esc_html_e('Save Changes', 'zephyr-project-manager'); ?></button>
		</div>
	<?php
	}

	public static function card_html($category) {
		$color = $category->color;
		$colorDark = Utillities::adjust_brightness($color, -40);
		$projects = Projects::category_projects($category->id, true);
		$projects = apply_filters('zpm_category_projects', $projects);
		$projectCount = sizeof($projects);
		$baseURL = is_admin() ? esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects')) : Utillities::get_frontend_url() . '?action=projects';
		$baseURL .= '&category_id=' . $category->id;

		if ($category->id == 'my_projects') {
			$baseURL .= '&user=' . get_current_user_id();
			$projectCount = Projects::getUserProjectCount();
		}

		if (!in_array($category->id, ['-1', 'all', 'my_projects'])) {
			// if (empty($projectCount)) return;
		}

		$url = apply_filters('zpm_category_project_url', $baseURL);

	?>
		<a class="zpm-category__grid-cell zpm-grid__cell" href="<?php echo esc_url($url); ?>" data-category-id="<?php echo esc_attr($category->id); ?>">
			<div class="zpm-card zpm-category-card" style="background: <?php echo esc_attr($color); ?>;
					background: -moz-linear-gradient(-45deg, <?php echo esc_attr($color); ?> 0%, <?php echo esc_attr($colorDark); ?> 100%);
					background: -webkit-linear-gradient(-45deg, <?php echo esc_attr($color); ?> 0%,<?php echo esc_attr($colorDark); ?> 100%);
					background: linear-gradient(135deg, <?php echo esc_attr($color); ?> 0%,<?php echo esc_attr($colorDark); ?> 100%);
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo esc_attr($color); ?>', endColorstr='<?php echo esc_attr($colorDark); ?>',GradientType=1 );">
				<p class="zpm-category-card__name"><?php echo esc_html($category->name); ?></p>
				<p class="zpm-category-card__description"><?php echo esc_html($category->description); ?></p>
				<span class="zpm-category-card__count"><span class="zpm-category-card__count-value"><?php echo esc_html($projectCount) ?></span> <?php echo sprintf(esc_html(_n('Project', 'Projects', $projectCount, 'zephyr-project-manager'))); ?></span>
			</div>
		</a>

	<?php
	}

	public static function categorySelect($args = []) {
		$default = [
			'id' => 'zpm-category-select',
			'multi' => true,
			'placeholder' => __('Select Option', 'zephyr-project-manager'),
			'selected_items' => [],
			'display_children' => true
		];
		$args = wp_parse_args($args, $default);
		ob_start();
		$categories = Categories::sort(Categories::get_categories());
	?>

		<select id="<?php echo esc_attr($args['id']); ?>" class="zpm_input zpm-input-chosen zpm-chosen-select" data-placeholder="<?php echo esc_attr($args['placeholder']); ?>">
			<option value=""><?php esc_html_e('None', 'zephyr-project-manager'); ?></option>

			<?php foreach ($categories as $category) : ?>
				<?php $children = Categories::getChildren($category->id); ?>
				<?php $selected = (is_array($args['selected_items']) && in_array($category->id, $args['selected_items']) ? 'selected' : ''); ?>
				<?php if (!Categories::hasParent($category)) : ?>
					<option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr($category->id); ?>"><?php echo esc_html($category->name); ?></option>
				<?php endif; ?>
				<?php foreach ($children as $child) : ?>
					<?php $selected = (is_array($args['selected_items']) && in_array($child->id, $args['selected_items']) ? 'selected' : ''); ?>
					<option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr($child->id); ?>"> - <?php echo esc_html($child->name); ?></option>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</select>

<?php

		$html = ob_get_clean();
		return $html;
	}

	public static function getChildren($id) {
		return [];
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;
		$categories = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE parent_id = %d", $id));
		return $categories;
	}

	public static function getChildrenCount($id) {
		return 0;
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE parent_id = %d", $id));
		return $count;
	}

	// Checks if a category is a parent
	public static function isParent($category) {
		if ($category->parent_id !== 0) {
			return false;
		}
		return true;
	}

	// Checks if a category has a parent
	public static function hasParent($category) {
		return false;
		$parentID = property_exists($category, 'parent_id') ? $category->parent_id : 0;
		$parentID = intval($parentID);
		if ($parentID !== 0) {
			return true;
		}
		return false;
	}

	// Checkis if category has childre
	public static function hasChildren($id) {
		$children = Categories::getChildren($id);;
		if (!empty($children)) {
			return true;
		} else {
			return false;
		}
	}

	// Returns comma separated string from category ID array
	public static function getCategoryString($catIDs = []) {
		$finalString = '';
		foreach ($catIDs as $i => $id) {
			$cat = Categories::get_category($id);
			if (!is_null($cat)) {
				$finalString .= $cat->name;
				if ($i < count($catIDs) - 1) {
					$finalString .= ', ';
				}
			}
		}

		return $finalString;
	}

	public static function sort($categories, $sorting = self::SORT_ALPHA) {

		if ($sorting == self::SORT_ALPHA) {
			usort($categories, function ($a, $b) {
				return strcmp($a->name, $b->name);
			});
		}

		return $categories;
	}

	public static function canView($categoryID) {
		// $settings = Utillities::general_settings();
		$viewAssigned = current_user_can('zpm_view_assigned_projects');

		if (Utillities::hasPerm('all_zephyr_capabilities')) return true;

		// $viewAssignedCategories = $settings['view_assigned_categories_only'];
		$userID = get_current_user_id();

		if ($categoryID == 'my_projects') return true;

		if ($categoryID == '-1') {
			if ($viewAssigned) {
				return false;
			}
		}

		if (!$viewAssigned) return true;

		if ($categoryID == '-1') {
			if ($viewAssigned) {
				return false;
			} else {
				return true;
			}
		}

		$projects = Projects::category_projects($categoryID, true);
		$hasAccess = false;

		foreach ($projects as $project) {
			if (!$hasAccess) {
				$hasAccess = Projects::isTeamMember($project, $userID) || Projects::isAssignee($project);
			}
		}

		return $hasAccess;
	}
}
