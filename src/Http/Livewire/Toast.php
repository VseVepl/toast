<?php

namespace Vsent\LaravelLivewireToasts\Http\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On; // For Livewire 3 event listeners

class Toast extends Component
{
    public Collection $toasts;
    public array $config;

    // Configuration properties that can be dynamically updated if needed
    public string $position;
    public string $mobilePosition;
    public bool $pauseOnHover;
    public bool $pauseOnWindowBlur;
    public bool $reverseOrderOnStack;
    public int $maxToastsDisplay;
    public bool $clearAllOnNavigate;
    public bool $allowSwipeToDismiss;
    public string $defaultAnimationPreset;

    // Sound related properties
    public bool $soundsGloballyEnabled;
    public string $soundBasePath;
    public float $defaultVolume;
    public bool $defaultLoop;
    public int $soundThrottleMs;


    public function mount()
    {
        $this->toasts = collect();
        $this->config = config('toasts'); // Load entire config

        // Initialize properties from config
        $this->position = $this->config['display']['position'];
        $this->mobilePosition = $this->config['display']['mobile_position'];
        $this->pauseOnHover = $this->config['behavior']['pause_on_hover'];
        $this->pauseOnWindowBlur = $this->config['behavior']['pause_on_window_blur'];
        $this->reverseOrderOnStack = $this->config['behavior']['reverse_order_on_stack'];
        $this->maxToastsDisplay = $this->config['behavior']['max_toasts_display'];
        $this->clearAllOnNavigate = $this->config['behavior']['clear_all_on_navigate'];
        $this->allowSwipeToDismiss = $this->config['behavior']['allow_swipe_to_dismiss'];
        $this->defaultAnimationPreset = $this->config['animations']['preset'];

        $this->soundsGloballyEnabled = $this->config['sounds']['global']['enabled'];
        $this->soundBasePath = rtrim($this->config['sounds']['global']['base_path'] ?? 'sounds/', '/');
        $this->defaultVolume = $this->config['sounds']['global']['default_volume'];
        $this->defaultLoop = $this->config['sounds']['global']['default_loop'];
        $this->soundThrottleMs = $this->config['sounds']['global']['throttle_ms'] ?? 50;
    }

    #[On('newToast')]
    public function addToast(array $payload)
    {
        if (Arr::get($this->config, 'behavior.duplicate_detection.enabled', false)) {
            $threshold = Arr::get($this->config, 'behavior.duplicate_detection.duration_threshold', 1000);
            $isDuplicate = $this->toasts->contains(function ($toast) use ($payload, $threshold) {
                return $toast['message'] === $payload['message'] &&
                       $toast['type'] === $payload['type'] &&
                       (microtime(true) * 1000 - $toast['timestamp']) < $threshold;
            });
            if ($isDuplicate) {
                return;
            }
        }

        $payload['timestamp'] = microtime(true) * 1000;

        if ($this->toasts->count() >= $this->maxToastsDisplay && $this->maxToastsDisplay > 0) {
            if (Arr::get($this->config, 'behavior.queue_mode', 'fifo') === 'fifo') { // Corrected config key
                $this->toasts->shift();
            } else { // lifo
                $this->toasts->pop();
            }
        }

        $this->toasts->push($payload);

        if (!empty($payload['sound']) && !empty($payload['sound']['src']) && $this->soundsGloballyEnabled) {
            $soundData = $payload['sound'];
            if (!Str::startsWith($soundData['src'], ['http://', 'https://', '/'])) {
                 $soundData['src'] = $this->soundBasePath . '/' . ltrim($soundData['src'], '/');
            }
            $this->dispatch('playToastSound', sound: $soundData);
        }
    }

    public function dismissToast(string $toastId)
    {
        $this->toasts = $this->toasts->reject(function ($toast) use ($toastId) {
            return $toast['id'] === $toastId;
        })->values();

        if ($this->soundsGloballyEnabled && isset($this->config['sounds']['assets']['dismiss'])) {
            $dismissSoundKey = 'dismiss';
            $soundAssetConfig = $this->config['sounds']['assets'][$dismissSoundKey] ?? null;
            if ($soundAssetConfig) {
                 $soundData = [
                    'src' => $soundAssetConfig['src'] ?? $dismissSoundKey . '.mp3',
                    'volume' => $soundAssetConfig['volume'] ?? $this->defaultVolume,
                    'loop' => $soundAssetConfig['loop'] ?? $this->defaultLoop,
                    'playback_rate' => $soundAssetConfig['playback_rate'] ?? 1.0,
                    'base_path' => $this->soundBasePath,
                    'throttle_ms' => $this->soundThrottleMs,
                ];
                if (!Str::startsWith($soundData['src'], ['http://', 'https://', '/'])) {
                     $soundData['src'] = $this->soundBasePath . '/' . ltrim($soundData['src'], '/');
                }
                $this->dispatch('playToastSound', sound: $soundData);
            }
        }
    }

    public function autoDismissToast(string $toastId)
    {
        if ($this->toasts->contains('id', $toastId)) {
            $this->dismissToast($toastId);
        }
    }

    #[On('clearAllToasts')]
    public function clearAllToasts()
    {
        $this->toasts = collect();
    }

    public function render()
    {
        $viewData = [
            'currentToasts' => $this->reverseOrderOnStack ? $this->toasts->reverse() : $this->toasts,
            'positionClasses' => $this->getPositionClasses(),
            'mobilePositionClasses' => $this->getMobilePositionClasses(), // Added mobile specific classes
            'maxWidthDesktop' => $this->config['display']['max_width'],
            'maxWidthMobile' => $this->config['display']['mobile_max_width'],
            'pauseOnHover' => $this->pauseOnHover,
            'pauseOnWindowBlur' => $this->pauseOnWindowBlur,
            'allowSwipeToDismiss' => $this->allowSwipeToDismiss,
            'closeButtonDefaultConfig' => $this->config['close_button'],
            'progressBarDefaultConfig' => $this->config['progress_bar'],
            'animationsDefaultConfig' => $this->config['animations']['global'],
            'animationPresets' => $this->config['animations']['presets'],
            'defaultAnimationPresetName' => $this->defaultAnimationPreset,
            'soundsGloballyEnabled' => $this->soundsGloballyEnabled,
            'soundThrottleMs' => $this->soundThrottleMs,
        ];
        return view('laravel-livewire-toasts::components.toast-container', $viewData);
    }

    protected function getPositionClasses(): string
    {
        $positionMap = [
            'top-left' => 'fixed top-0 left-0 z-50 p-4 space-y-4',
            'top-center' => 'fixed top-0 left-1/2 transform -translate-x-1/2 z-50 p-4 space-y-4',
            'top-right' => 'fixed top-0 right-0 z-50 p-4 space-y-4',
            'bottom-left' => 'fixed bottom-0 left-0 z-50 p-4 space-y-4',
            'bottom-center' => 'fixed bottom-0 left-1/2 transform -translate-x-1/2 z-50 p-4 space-y-4',
            'bottom-right' => 'fixed bottom-0 right-0 z-50 p-4 space-y-4',
        ];
        return $positionMap[$this->position] ?? $positionMap['top-right'];
    }

    protected function getMobilePositionClasses(): string
    {
        // This is a simplified version. Tailwind typically uses responsive prefixes like sm:, md:.
        // A more robust solution might involve passing both desktop and mobile to view and using CSS or Alpine.
        $positionMap = [
            'top-left' => 'fixed top-0 left-0 z-50 p-4 space-y-4', // Example, might need specific mobile classes
            'top-center' => 'fixed top-0 left-1/2 transform -translate-x-1/2 z-50 p-4 space-y-4',
            'top-right' => 'fixed top-0 right-0 z-50 p-4 space-y-4',
            'bottom-left' => 'fixed bottom-0 left-0 z-50 p-4 space-y-4',
            'bottom-center' => 'fixed bottom-0 left-1/2 transform -translate-x-1/2 z-50 p-4 space-y-4',
            'bottom-right' => 'fixed bottom-0 right-0 z-50 p-4 space-y-4',
        ];
        return $positionMap[$this->mobilePosition] ?? $positionMap['bottom-right']; // Default mobile to bottom-right
    }
}
