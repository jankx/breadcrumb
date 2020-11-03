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
            array(),
            $breadcrumb
        )
    );
}
