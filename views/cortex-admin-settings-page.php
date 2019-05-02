<div class="wrap">

    <?php settings_errors() ?>

    <h1><?php _e('Cortex Settings', 'cortex') ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields('cortex') ?>
        <?php do_settings_sections('cortex') ?>
        <?php submit_button() ?>
    </form>

</div>