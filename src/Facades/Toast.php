<?php

namespace Vsent\LaravelLivewireToasts\Facades;

use Illuminate\Support\Facades\Facade;
use Vsent\LaravelLivewireToasts\Helpers\ToastMessage;

/**
 * @method static void show(string $message, string $type = 'default', ?string $title = null, array $options = [])
 * @method static void success(string $message, ?string $title = null, array $options = [])
 * @method static void error(string $message, ?string $title = null, array $options = [])
 * @method static void warning(string $message, ?string $title = null, array $options = [])
 * @method static void info(string $message, ?string $title = null, array $options = [])
 * @method static void default(string $message, ?string $title = null, array $options = [])
 *
 * @see \Vsent\LaravelLivewireToasts\Helpers\ToastMessage
 */
class Toast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ToastMessage::class;
    }
}
