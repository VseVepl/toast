@props([
    'currentToasts' => collect(),
    'positionClasses' => 'fixed top-0 right-0 z-50 p-4 space-y-4',
    'mobilePositionClasses' => 'fixed bottom-0 right-0 z-50 p-4 space-y-4',
    'maxWidthDesktop' => 'max-w-md',
    'maxWidthMobile' => 'max-w-xs',
    'pauseOnHover' => true,
    'pauseOnWindowBlur' => true,
    'allowSwipeToDismiss' => true,
    'closeButtonDefaultConfig' => [],
    'progressBarDefaultConfig' => [],
    'animationsDefaultConfig' => [],
    'animationPresets' => [],
    'defaultAnimationPresetName' => 'slide_from_bottom',
    'soundsGloballyEnabled' => false,
    'soundThrottleMs' => 50,
    'clearAllOnNavigate' => true,
])

@php
    $soundsGloballyEnabledJs = Js::from($soundsGloballyEnabled);
    $requireInteractionOnMobileConfigJs = Js::from(config('toasts.sounds.global.require_interaction_on_mobile', true));
@endphp

<div
    aria-live="{{ config('toasts.behavior.aria_live_region', 'polite') }}"
    role="region"
    x-data="toastContainer({
        toasts: {{ $currentToasts->map(function($toast) { return array_merge($toast, ['show' => false]); })->values()->toJson() }},
        initialToastsCount: {{ $currentToasts->count() }},
        positionClasses: '{{ $positionClasses }}',
        mobilePositionClasses: '{{ $mobilePositionClasses }}',
        maxWidthDesktop: '{{ $maxWidthDesktop }}',
        maxWidthMobile: '{{ $maxWidthMobile }}',
        pauseOnHover: {{ Js::from($pauseOnHover) }},
        pauseOnWindowBlur: {{ Js::from($pauseOnWindowBlur) }},
        allowSwipeToDismiss: {{ Js::from($allowSwipeToDismiss) }},
        soundsGloballyEnabled: {{ $soundsGloballyEnabledJs }},
        soundThrottleMs: {{ Js::from($soundThrottleMs) }},
        clearAllOnNavigate: {{ Js::from($clearAllOnNavigate) }},
        requireInteractionOnMobileConfig: {{ $requireInteractionOnMobileConfigJs }}
    })
    @clear-all-toasts.window="clearAll()"
    @play-toast-sound.window="playSound($event.detail.sound)"
    @if($clearAllOnNavigate)
        @navigate.window="clearAll()"
    @endif
    :class="`${currentPositionClasses} ${currentMaxWidthClasses}`"
    style="z-index: {{ config('toasts.display.z_index', 1050) }};"
    class="fixed p-4 space-y-3 w-full sm:w-auto"
>
    <template x-for="toast in toasts" :key="toast.internalId">
        <x-laravel-livewire-toasts::toast-item
            ::toast-data="toast"
            ::config="{{ json_encode(config('toasts')) }}"
            ::default-animation-preset-name="'{{ $defaultAnimationPresetName }}'"
            ::animation-presets="{{ json_encode($animationPresets) }}"
            ::animations-default-config="{{ json_encode($animationsDefaultConfig) }}"
            ::close-button-default-config="{{ json_encode($closeButtonDefaultConfig) }}"
            ::progress-bar-default-config="{{ json_encode($progressBarDefaultConfig) }}"
            @dismiss-toast="removeToast(toast.id)"
            @auto-dismiss-toast="autoDismiss(toast.id)"
        />
    </template>

    <audio x-ref="toastAudioPlayer" style="display: none;"></audio>
</div>

@once
<script>
    document.addEventListener('alpine:initializing', () => {
        Alpine.data('toastContainer', (options) => ({
            toasts: [],
            lastSoundPlayedAt: 0,
            currentPositionClasses: '',
            currentMaxWidthClasses: '',
            userInteracted: false, // For mobile sound interaction

            init() {
                this.toasts = options.toasts.map((toast, index) => {
                    return { ...toast, show: false, internalId: Alpine.raw(toast.id) + '-' + index };
                });

                this.userInteracted = !(options.soundsGloballyEnabled && options.requireInteractionOnMobileConfig && /Mobi|Android/i.test(navigator.userAgent));

                if (!this.userInteracted) {
                    ['click', 'touchstart'].forEach(eventType => {
                        document.addEventListener(eventType, () => this.handleFirstInteraction(), { once: true, passive: true });
                    });
                }

                this.$watch('toasts', (newToasts, oldToasts) => {
                    const added = newToasts.filter(nt => !oldToasts.find(ot => ot.internalId === nt.internalId));
                    added.forEach(toast => {
                        this.$nextTick(() => {
                           const found = this.toasts.find(t => t.internalId === toast.internalId);
                           if(found) found.show = true;
                        });
                    });
                });

                if (options.initialToastsCount > 0) {
                    this.toasts.forEach(toast => {
                        this.$nextTick(() => { toast.show = true; });
                    });
                }

                this.updateResponsiveClasses();
                window.addEventListener('resize', () => this.updateResponsiveClasses());

                if (options.clearAllOnNavigate) {
                    document.addEventListener('livewire:navigating', () => {
                        this.clearAll();
                    });
                }
            },

            handleFirstInteraction() {
                if (!this.userInteracted && options.soundsGloballyEnabled && options.requireInteractionOnMobileConfig && /Mobi|Android/i.test(navigator.userAgent)) {
                    this.userInteracted = true;
                    const player = this.$refs.toastAudioPlayer;
                    if (player && player.paused) {
                        player.src = "data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA"; // Short silent WAV
                        player.volume = 0.01;
                        player.play().then(() => {
                           player.pause();
                           player.currentTime = 0;
                           player.src = '';
                        }).catch(e => { /* console.warn('Silent audio play failed', e) */ });
                    }
                }
            },

            updateResponsiveClasses() {
                if (window.innerWidth < 640) {
                    this.currentPositionClasses = options.mobilePositionClasses;
                    this.currentMaxWidthClasses = options.maxWidthMobile;
                } else {
                    this.currentPositionClasses = options.positionClasses;
                    this.currentMaxWidthClasses = options.maxWidthDesktop;
                }
            },

            addToast(newToast) {
                const exists = this.toasts.some(t => t.id === newToast.id);
                if (!exists) {
                    this.toasts.push({ ...newToast, show: false, internalId: Alpine.raw(newToast.id) + '-' + this.toasts.length });
                }
            },

            removeToast(toastId) {
                const toast = this.toasts.find(t => t.id === toastId);
                if (toast) {
                    toast.show = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== toastId);
                        if (this.$wire) { this.$wire.dismissToast(toastId); }
                    }, (Alpine.raw(toast.animation)?.leave_duration || Alpine.raw(this.animationPresets)?.default?.leave_duration || 300) + 50);
                }
            },

            autoDismiss(toastId) {
                 if (this.$wire) { this.$wire.autoDismissToast(toastId); }
            },

            clearAll() {
                this.toasts.forEach(toast => toast.show = false);
                setTimeout(() => {
                    this.toasts = [];
                }, 500);
            },

            playSound(sound) {
                if (options.soundsGloballyEnabled && options.requireInteractionOnMobileConfig && /Mobi|Android/i.test(navigator.userAgent) && !this.userInteracted) {
                    // console.log('Sound deferred until user interaction on mobile');
                    return;
                }
                if (!options.soundsGloballyEnabled || !sound || !sound.src) return;
                const now = Date.now();
                if (now - this.lastSoundPlayedAt < options.soundThrottleMs) return;
                this.lastSoundPlayedAt = now;
                const player = this.$refs.toastAudioPlayer;
                if (player) {
                    let soundSrc = sound.src;
                    if (sound.base_path && !soundSrc.startsWith('http') && !soundSrc.startsWith('/')) {
                        soundSrc = `${sound.base_path.replace(/\/+$/, '')}/${soundSrc.replace(/^\/+/, '')}`;
                    }
                    player.src = soundSrc;
                    player.volume = sound.volume !== null ? sound.volume : 1.0;
                    player.loop = sound.loop !== null ? sound.loop : false;
                    if (sound.playback_rate) player.playbackRate = sound.playback_rate;
                    player.play().catch(e => console.error('Toast sound error:', e));
                }
            },
        }));
    });
</script>
@endonce
