<?php
/**
 * Expects query var 'client_nav_args' => [
 *   'stages'      => [ slug => [title,group,next], … ],
 *   'stage'       => current slug,
 *   'real_post_id'=> int
 * ]
 */
$args      = get_query_var('client_nav_args', []);
$stages    = $args['stages']      ?? [];
$current   = $args['stage']       ?? '';
$real_id   = $args['real_post_id']?? 0;

$keys      = array_keys($stages);
$idx       = array_search($current, $keys, true);
$visible   = array_slice($keys, 0, $idx+1);
$step    = $stages;
?>
<div class="nav-align-top mb-4">
  <ul class="nav nav-pills mb-3 nav-fill" id="clientTab" role="tablist">
    <?php foreach($visible as $slug):
      $step    = $stages[$slug];
      $is_active = ($slug === $current);
      $is_disabled = empty($step['group']);
      $tab_id = $slug.'-tab';
      $pane_id = $slug;
    ?>
      <li class="nav-item mb-1 mb-sm-0" role="presentation">
        <button type="button" class="nav-link <?php echo $is_active ? 'active' : ''; ?> <?php echo $is_disabled ? 'disabled' : ''; ?>"
           id="<?php echo esc_attr($tab_id); ?>"
           data-bs-toggle="tab"
           data-bs-target="#<?php echo esc_attr($pane_id); ?>"
           data-tab="<?php echo esc_attr($pane_id); ?>"
           role="tab"
           aria-controls="<?php echo esc_attr($pane_id); ?>"
           aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
          <span class="d-flex align-items-center">
            <i class="bx bx-file me-1"></i> <?php echo esc_html($step['title']); ?>
          </span>
        </button>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
