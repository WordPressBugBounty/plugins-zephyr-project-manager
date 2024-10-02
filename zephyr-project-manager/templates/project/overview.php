<?php
    // Project overview (used for PDF, printing, etc.)

    if (!defined('ABSPATH')) die;

    use ZephyrProjectManager\Core\Tasks;
    use ZephyrProjectManager\Core\Projects;

    if (is_null($project)) return;

    $tasks = Tasks::get_project_tasks($project->id);
    $files = Projects::getFiles($project->id);
    $description = zpm_esc_html($project->description);
?>

<div class="zpm-project-overview" data-project-overview="<?php echo esc_attr($project->id); ?>">
    <?php do_action('zpm/project/overview/before', $project); ?>

    <header class="zpm-project-overview-header" data-project-overview-name><?php echo esc_html($project->name); ?></header>
    
    <div class="zpm-overview-field">
        <label class="zpm-overview-field--label"><?php esc_html_e('Name', 'zephyr-project-manager'); ?></label>
        <div class="zpm-overview-field--value"><?php echo esc_html($project->name); ?></div>
    </div>
    <div class="zpm-overview-field">
        <label class="zpm-overview-field--label"><?php esc_html_e('Description', 'zephyr-project-manager'); ?></label>
        <div class="zpm-overview-field--value"><?php echo !empty($description) ? $description : __('None', 'zephyr-project-manager'); ?></div>
    </div>
    <div class="zpm-overview-field">
        <label class="zpm-overview-field--label"><?php esc_html_e('Start Date', 'zephyr-project-manager'); ?></label>
        <div class="zpm-overview-field--value"><?php echo zpm_date($project->date_start, __('None', 'zephyr-project-manager')); ?></div>
    </div>
    <div class="zpm-overview-field">
        <label class="zpm-overview-field--label"><?php esc_html_e('Due Date', 'zephyr-project-manager'); ?></label>
        <div class="zpm-overview-field--value"><?php echo zpm_date($project->date_due, __('None', 'zephyr-project-manager')); ?></div>
    </div>

    <?php do_action('zpm/project/overview/fields', $project); ?>

    <div class="zpm-overview-field">
        <label class="zpm-overview-field--label"><?php esc_html_e('Tasks', 'zephyr-project-manager'); ?></label>
        <div class="zpm-overview-field--value">
            <?php if (empty($tasks)): ?>
                <?php esc_html_e('No tasks', 'zephyr-project-manager'); ?>
            <?php endif; ?>
            <?php foreach ($tasks as $task): ?>
                <?php $description = zpm_esc_html($task->description); ?>
                <?php $isCompleted = Tasks::isCompleted($task); ?>
                <div>
                    <span>
                        <label for="zpm_task_id_<?php echo esc_attr($task->id); ?>" class="zpm-material-checkbox">
                        <input type="checkbox" id="zpm_task_id_<?php echo esc_attr($task->id); ?>" name="zpm_task_id_<?php echo esc_attr($task->id); ?>" class="zpm_task_mark_complete zpm_toggle invisible" data-task-id="<?php echo esc_attr($task->id); ?>" aria-label="Toggle task completion" <?php echo $isCompleted ? 'checked' : ''; ?>>
                        <span class="zpm-material-checkbox-label"></span>
                      </label>
                    </span>
                    <span><?php echo esc_html($task->name); ?></span>
                    <?php if (!empty($description)): ?>
                        <span> - <?php echo $description; ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="zpm-overview-field">
        <label class="zpm-overview-field--label"><?php esc_html_e('Files', 'zephyr-project-manager'); ?></label>
        <div class="zpm-overview-field--value">
            <?php if (empty($files)): ?>
                <?php esc_html_e('No files', 'zephyr-project-manager'); ?>
            <?php endif; ?>
            <?php foreach ($files as $file): ?>
                <a href="<?php echo esc_url($file['url']); ?>" target="_BLANK"><?php echo esc_attr($file['name']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php do_action('zpm/project/overview/after', $project); ?>
</div>