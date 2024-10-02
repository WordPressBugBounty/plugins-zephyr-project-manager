<?php

/**
 * Template for displaying the task list
 */

if (!defined('ABSPATH')) {
	die;
}

use ZephyrProjectManager\Core\Tasks;
use ZephyrProjectManager\Core\Projects;
use ZephyrProjectManager\Core\Utillities;
use ZephyrProjectManager\ZephyrProjectManager;

$manager = ZephyrProjectManager::get_instance();
$tasks = Tasks::get_tasks();
$task_count = 0;
$userId = get_current_user_id();

if (isset($filters['user_tasks'])) {
	$tasks = Tasks::get_user_tasks($userId);
}

if (isset($_GET['status'])) {
	if ($_GET['status'] == 'pending') {
		$tasks = Tasks::get_user_completed_tasks($userId, '0');
	}
} else if (isset($_GET['completed'])) {
	if ($_GET['completed'] == 'true') {
		$tasks = Tasks::get_user_completed_tasks($userId);
	}
}

$isProjectList = false;

if (isset($_GET['project']) || isset($_POST['project_id'])) {
	$isProjectList = true;
	$projectId = isset($_GET['project']) ? zpm_sanitize_int($_GET['project']) : '';
	$projectId = empty($projectId) && isset($_POST['project_id']) ? zpm_sanitize_int($_POST['project_id']) : $projectId;
	$tasks = Projects::getOrderedTasks($projectId, $tasks);
}

if (isset($filters['sort'])) {
	$tasks = Tasks::sortTasks($tasks, $filters['sort']);
}


$hideCompletedTasks = Utillities::getSetting('hide_completed_tasks');
$currentUserID = get_current_user_id();

ob_start();
?>
	<div id="zpm-task-list__<?php echo $isProjectList ? 'project' : 'tasks'; ?>" class="zpm_task_list">
<?php
$taskListStart = ob_get_clean();
?>

<?php echo apply_filters('zpm/task/list/wrap/start', $taskListStart); ?>

	<?php if (!empty($tasks)) : ?>
		<?php foreach ($tasks as $task) : ?>
			<?php
			if (isset($filters['user_tasks']) && $filters['user_tasks'] == $currentUserID) {
				if ($hideCompletedTasks) {
					if (Tasks::isCompleted($task)) {
						continue;
					}
				}
			}
						
			$project = $manager::get_project($task->project);
			
			if (isset($_GET['project']) && $_GET['project'] !== $task->project || (isset($_POST['project_id']) && $_POST['project_id'] !== $task->project)) continue;
			
			$frontend = !is_admin();

			if (isset($_POST['frontend'])) {
				$frontend = zpm_sanitize_bool($_POST['frontend']);
			}

			$row = Tasks::new_task_row($task, $frontend);
			echo $row;

			if (!empty($row)) {
				$task_count++;
			}
			?>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ($task_count <= 0) : ?>
		<p class="zpm_message_center"><?php esc_html_e('There are no tasks yet.', 'zephyr-project-manager'); ?></p>
	<?php endif; ?>
<?php echo apply_filters('zpm/task/list/wrap/end', '</div>'); ?>