<?php
add_action( 'admin_menu', function() {
  add_options_page( __('Bootstrap Core Blocks', 'bcb'), __('Bootstrap Core Blocks', 'bcb'), 'manage_options', 'bootstrap-core-blocks', 'bootstrap_core_blocks_settings' );
} );

function bootstrap_core_blocks_settings() {

  // Load settings from WP API
  $bcbsettings = get_option("bootstrap-core-blocks", ['posttypes' => []]);

  // Save settings from settings form
  if (isset($_POST['save_bcb_settings'])) {
    $bcbsettings['posttypes'] = $_POST['posttypes'];
    update_option("bootstrap-core-blocks", $bcbsettings);
  }


  // Rendering of Settings page
  $title        = __('Bootstrap Core Blocks Settings', 'bcb');
  $cardtitle    = __('Activate Bootstrap Core blocks on these Post Types', 'bcb');

  // TODO: detect which post types have the Block Editor enabled.
  $posttypes    = get_post_types(['public' => true, 'show_ui' => true ]);
  if(isset($posttypes['attachment'])) unset($posttypes['attachment']);

  $bcbEnabled = "";

  function renderCheckbox($opts) {
    $checked = $opts['checked']?"checked":"";

    $name = $opts['name'];
    $id = $opts['id_base'].'['.$opts['id'].']';

    return <<<CB
        <label for="$id">
            <input type="checkbox" id="$id" name="$id" $checked>
            $name
        </label><br>
CB;
  }

  foreach ($posttypes as $type) {
    $bcbEnabled .= renderCheckbox([
      'id_base' => 'posttypes',
      'id' => $type,
      'name' => ucfirst($type),
      'checked' => isset($bcbsettings['posttypes'][$type])?$bcbsettings['posttypes'][$type]:false
    ]);
  }

  $template = <<<TEMPLATE
<div class="wrap">
  <h1>$title</h1>

  <!-- IMPORT -->
  <div class="card">
    <h2>$cardtitle</h2>
    <form method="post" id="save_settings" action="">
    $bcbEnabled
    <p class="submit"><input type="submit" name="save_bcb_settings" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
  </div>
</div>
TEMPLATE;

  print $template;
}
