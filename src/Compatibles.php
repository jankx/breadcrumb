<?php
namespace Jankx\Breadcrumb;

class Compatibles {
    protected static $dectector;
    protected static $thirdparties;
    protected static $default_conditions = array(
        'validate' => '',
        'type' => '',
    );

    protected $callable;
    protected $args;

    public function __construct() {
        if (is_null(static::$thirdparties)) {
            static::$thirdparties = apply_filters('jankx_breadcrumb_thirdparties', array(
                'yoast_seo' => array(
                    'conditions'=> array(
                        array(
                            'type' => 'function',
                            'validate' => 'yoast_breadcrumb'
                        ),
                        array(
                            'type' => 'option',
                            'validate' => 'wpseo_titles.breadcrumbs-enable'
                        )
                    ),
                    'callable' => 'yoast_breadcrumb',
                    'args' => array('', '')
                ),
            ));
        }
    }

    public static function detect() {
        if (is_null(static::$dectector)) {
            static::$dectector = new static();
        }
        return static::$dectector;
    }

    public function checkConditionStatus($condition, $type = 'function') {
        switch (strtolower($type)) {
            case 'function':
                return function_exists($condition);
            case 'option':
                $option_condition = explode('.', $condition);
                $option_name = array_shift($option_condition);
                $option_values = get_option($option_name);
                if (empty($option_condition)) {
                    return $option_values;
                }
                foreach($option_condition as $key) {
                    if (!isset($option_values[$key])) {
                        return false;
                    }
                    $option_values = $option_values[$key];
                }
                return $option_values;
            default:
                return is_callable($condition);
        }
    }

    public function findCallable() {
        foreach(array_values(static::$thirdparties) as $thidparty) {
            if (empty($thidparty['callable'])) {
                continue;
            }
            $isOk = true;
            if (isset($thidparty['conditions']) && is_array($thidparty['conditions'])) {
                foreach($thidparty['conditions'] as $subcondition) {
                    $subcondition = wp_parse_args($subcondition, static::$default_conditions);
                    if (!$this->checkConditionStatus($subcondition['validate'], $subcondition['type'])) {
                        $isOk = false;
                        break;
                    }
                }
            } else {
                $isOk = $this->checkConditionStatus($thidparty['callable'], $thidparty['type']);
            }

            if ($isOk) {
                $this->callable = $thidparty['callable'];
                $this->args = isset($thidparty['args']) ? $thidparty['args'] : array();
                return;
            }
        }
        $breadcrumb = new Breadcrumb();
        $this->callable = array($breadcrumb, 'render');
    }

    public function getCallable() {
        return $this->callable;
    }

    public function getArgs() {
        return is_array($this->args) ? $this->args : array();
    }

    public function hasBreadcrumb() {
        return !is_null($this->callable);
    }
}
