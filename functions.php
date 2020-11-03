<?php
use Jankx\Breadcrumb\Compatibles;

function jankx_breadcrumb() {
    $breadcrumb = Compatibles::detect();
    $callable = $breadcrumb->getCallable();
    if (!is_callable($callable)) {
        // Don't show anything
        return;
    }

    return call_user_func_array(
        $callable,
        apply_filters(
            'jankx_breadcrumb_callback_args',
            $breadcrumb->getArgs(),
            $breadcrumb
        )
    );
}

function jankx_load_breadcrumb() {
    if (is_home() || is_front_page()) {
        return;
    }
    $detector = Compatibles::detect();
    $detector->findCallable();
    if (!$detector->hasBreadcrumb()) {
        return;
    }
    ?>
    <div id="jankx-breacrumb" class="breadcrumb">
        <?php jankx_open_container(); ?>
        <?php echo jankx_breadcrumb(); ?>
        <?php jankx_close_container(); ?>
    </div>
    <?php
}
add_action('jankx_template_before_main_content_sidebar', 'jankx_load_breadcrumb');
