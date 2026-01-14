<?php

namespace Vsent\LaravelLivewireToasts\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str; // Import Str
use Livewire\EventBus;

class ToastMessage
{
    protected static function dispatchToast(array $payload)
    {
        // In Livewire 3, the event bus is typically accessed via the component instance or a global helper if available.
        // Assuming a global event dispatch mechanism for simplicity here.
        // Replace with `Livewire::dispatch()` if running in a context where it's available,
        // or use `event(new SomeGlobalToastEvent($payload))` if you prefer Laravel events.
        // For direct Livewire component event emission without a component instance:
        \Livewire\Livewire::dispatch('newToast', payload: $payload);
    }

    public static function show(string $message, string $type = 'default', ?string $title = null, array $options = [])
    {
        $defaultTypeConfig = config('toasts.types.defaults', []);
        $specificTypeConfig = config("toasts.types.{$type}", []);

        // Precedence: $options > $specificTypeConfig > $defaultTypeConfig
        $mergedConfig = array_replace_recursive($defaultTypeConfig, $specificTypeConfig, $options);

        // Prepare sound configuration
        $soundPayload = null; // Default to no sound if not configured or globally disabled
        if (config('toasts.sounds.global.enabled', false)) {
            $soundSettings = $mergedConfig['sound'] ?? [];
            $soundAssetKey = $soundSettings['src'] ?? null;

            if (!$soundAssetKey) { // If not in options, try type-specific config
                $soundAssetKey = config("toasts.types.{$type}.sound.src") ?? config('toasts.types.defaults.sound.src') ?? null;
            }
            if (!$soundAssetKey) { // If not in type-specific, try mapping
                $soundAssetKey = config("toasts.sounds.types.{$type}") ?? null;
            }
            if (!$soundAssetKey && $type === 'default') { // Fallback for default type's global sound
                 $defaultSoundName = config('toasts.sounds.global.default_sound');
                 if ($defaultSoundName) {
                    $soundAssetKey = Str::beforeLast($defaultSoundName, '.mp3'); // Assuming key is filename without extension
                 }
            } else if (!$soundAssetKey) { // Fallback for other types to use global default sound
                 $defaultSoundName = config('toasts.sounds.global.default_sound');
                 if ($defaultSoundName) {
                    $soundAssetKey = Str::beforeLast($defaultSoundName, '.mp3');
                 }
            }

            if ($soundAssetKey) {
                $soundAssetConfig = config("toasts.sounds.assets.{$soundAssetKey}", []);
                $globalSoundDefaults = config('toasts.sounds.global', []);

                $soundPayload = [
                    'src' => $soundAssetConfig['src'] ?? $soundAssetKey . '.mp3',
                    'volume' => $soundSettings['volume'] ?? $soundAssetConfig['volume'] ?? $globalSoundDefaults['default_volume'],
                    'loop' => $soundSettings['loop'] ?? $soundAssetConfig['loop'] ?? $globalSoundDefaults['default_loop'],
                    'playback_rate' => $soundSettings['playback_rate'] ?? $soundAssetConfig['playback_rate'] ?? 1.0,
                    'base_path' => rtrim($globalSoundDefaults['base_path'] ?? 'sounds/', '/'),
                    'enabled_globally' => true, // Sounds are on and we have a sound to play
                    'throttle_ms' => $globalSoundDefaults['throttle_ms'] ?? 50,
                ];
            } else {
                // Sounds are globally enabled, but no specific sound for this toast.
                // Send minimal payload to indicate sound system is active for potential global actions (like mute toggles).
                $soundPayload = ['enabled_globally' => true, 'src' => null];
            }
        }


        $payload = [
            'id' => Str::uuid()->toString(),
            'message' => $message,
            'type' => $type,
            'title' => $title,
            'duration' => Arr::get($mergedConfig, 'duration'),
            'icon' => Arr::get($mergedConfig, 'icon'),
            'bg' => Arr::get($mergedConfig, 'bg'),
            'text_color' => Arr::get($mergedConfig, 'text_color'),
            'show_progress' => Arr::get($mergedConfig, 'show_progress'),
            'dismissible' => Arr::get($mergedConfig, 'dismissible'),
            'position' => Arr::get($mergedConfig, 'position', config('toasts.display.position')),
            'layout_preset' => Arr::get($mergedConfig, 'layout_preset', config('toasts.types.defaults.layout_preset', 'default')),
            'animation_preset' => Arr::get($mergedConfig, 'animation_preset', config('toasts.animations.preset')),
            'actions' => Arr::get($mergedConfig, 'actions', []),
            'sound' => $soundPayload,
            'aria_role' => Arr::get($mergedConfig, 'aria_role'),
            'close_button_config' => $options['close_button'] ?? config('toasts.close_button'), // Pass full config
            'progress_bar_config' => $options['progress_bar'] ?? config('toasts.progress_bar'), // Pass full config
            'type_config' => $specificTypeConfig, // Pass the specific type's own config as well
        ];

        self::dispatchToast($payload);
    }

    public static function success(string $message, ?string $title = null, array $options = [])
    {
        self::show($message, 'success', $title, $options);
    }

    public static function error(string $message, ?string $title = null, array $options = [])
    {
        self::show($message, 'error', $title, $options);
    }

    public static function warning(string $message, ?string $title = null, array $options = [])
    {
        self::show($message, 'warning', $title, $options);
    }

    public static function info(string $message, ?string $title = null, array $options = [])
    {
        self::show($message, 'info', $title, $options);
    }

    public static function default(string $message, ?string $title = null, array $options = [])
    {
        self::show($message, 'default', $title, $options);
    }
}
