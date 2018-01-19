<?php

/**
 * Fetch a value from the environment.
 *
 * @param string $varname
 * @param null   $default
 * @return mixed
 */
function env(string $varname, $default = null)
{
    $value = getenv($varname);

    if ($value === false) {
        return $default;
    }

    switch ($value) {
        case 'true':
            $value = true;
            break;
        case 'false':
            $value = false;
            break;
        case 'null':
            $value = null;
            break;
    }

    return $value;
}
