<?php
/**
 * All known Languages
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Config\Config;


Config::set('languages', [
    'En' => [
        'info' => 'English',
        'name' => 'English',
        'locale' => 'en-US',
        'dir' => 'ltr'
    ],
    'Ru' => [
        'info' => 'Russian',
        'name' => 'Русский',
        'locale' => 'ru-RU',
        'dir' => 'ltr'
    ],
    'Ceb' => [
        'info' => 'Cebuano',
        'name' => 'Cebuano',
        'locale' => 'ceb',
        'dir' => 'ltr'
    ],
    'Id' => [
        'info' => 'Indonesian',
        'name' => 'Bahasa Indonesia',
        'locale' => 'id',
        'dir' => 'ltr'
    ],
    'Fr' => [
        'info' => 'French',
        'name' => 'Français',
        'locale' => 'fr',
        'dir' => 'ltr'
    ],
]);