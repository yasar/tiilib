<?php

class TiiHlpHtml
{
    const DEFAULT_TAG = 'input';

    public static function GetTag($attributes, $tag = null)
    {
        $_attributes = array();
		
        if (is_null($tag)) {
            if (!isset($attributes['tag'])) {
                $tag = self::DEFAULT_TAG;
                error_log('input_type/tag is not defined. set to "' . $tag . '".',
                    E_USER_WARNING);
            } else {
                $tag = $attributes['tag'];
                unset($attributes['tag']);
            }
        }

        $keys = array_keys($attributes);
        foreach ($keys as $key) {
            $_attributes[] = $key . '="' . $attributes[$key] . '"';
        }
        $_attributes = implode(' ', $_attributes);

        switch ($tag) {
            case 'input':
                return '<input ' . $_attributes . ' />';
                break;

            case 'select':
                break;

            case 'textarea':
                break;

            case 'button':
                break;
        }
    }
}
