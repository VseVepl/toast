@props([
    'toastData',
    'config',
    'defaultAnimationPresetName',
    'animationPresets',
    'animationsDefaultConfig',
    'closeButtonDefaultConfig',
    'progressBarDefaultConfig',
])

@php
    $toastType = $toastData['type'] ?? 'default';
    $typeConfig = $config['types'][$toastType] ?? $config['types']['default'] ?? [];
    $layoutPresetName = $toastData['layout_preset'] ?? $typeConfig['layout_preset'] ?? $config['types']['defaults']['layout_preset'] ?? 'default';
    $layoutConfig = $config['types']['layouts'][$layoutPresetName] ?? $config['types']['layouts']['default'];

    $animationPresetName = $toastData['animation_preset'] ?? $typeConfig['animation_preset'] ?? $defaultAnimationPresetName;
    $animation = $animationPresets[$animationPresetName] ?? $animationPresets['none'] ?? [];
    $animation = array_merge($animationsDefaultConfig, $animation); // Merge with global animation defaults

    $duration = $toastData['duration'] ?? $typeConfig['duration'] ?? $config['display']['default_duration'];
    $dismissible = $toastData['dismissible'] ?? $typeConfig['dismissible'] ?? $config['types']['defaults']['dismissible'] ?? true;

    // Resolve close button settings: toastData > typeConfig > global config > component default prop
    $_userCloseButtonConfig = $toastData['close_button_config'] ?? [];
    $_typeCloseButtonConfig = $typeConfig['close_button'] ?? []; // This might be true/false or an array
    if (is_bool($_typeCloseButtonConfig)) { $_typeCloseButtonConfig = ['enabled' => $_typeCloseButtonConfig]; }
    $_globalCloseButtonConfig = $config['close_button'] ?? [];
    $closeButtonSettings = array_merge($closeButtonDefaultConfig, $_globalCloseButtonConfig, $_typeCloseButtonConfig, $_userCloseButtonConfig);
    $showCloseButton = $dismissible && ($closeButtonSettings['enabled'] ?? true);

    // Resolve progress bar settings
    $_userProgressBarConfig = $toastData['progress_bar_config'] ?? [];
    $_typeProgressBarConfig = $typeConfig['progress_bar'] ?? []; // This might be true/false or an array
    if (is_bool($_typeProgressBarConfig)) { $_typeProgressBarConfig = ['enabled' => $_typeProgressBarConfig]; }
    $_globalProgressBarConfig = $config['progress_bar'] ?? [];
    $progressBarSettings = array_merge($progressBarDefaultConfig, $_globalProgressBarConfig, $_typeProgressBarConfig, $_userProgressBarConfig);
    $showProgressBar = ($toastData['show_progress'] ?? $typeConfig['show_progress'] ?? $config['types']['defaults']['show_progress'] ?? $progressBarSettings['enabled'] ?? false) && $duration > 0;

    $typeSpecificProgressBarOverrides = $config['progress_bar']['type_overrides'][$toastType] ?? [];
    // Simplified fallback for progress bar foreground, relying on the 'default' key in type_overrides
    $progressBarForeground = $typeSpecificProgressBarOverrides['foreground'] ?? $progressBarSettings['type_overrides']['default']['foreground'] ?? 'bg-gray-500 dark:bg-gray-400';
    $progressBarHeight = $typeSpecificProgressBarOverrides['height'] ?? $progressBarSettings['height'] ?? 'h-1';

    $bgColor = $toastData['bg'] ?? $typeConfig['bg'] ?? 'bg-gray-800';
    $textColor = $toastData['text_color'] ?? $typeConfig['text_color'] ?? 'text-white';
    $icon = $toastData['icon'] ?? $typeConfig['icon'] ?? '';
    $ariaRole = $toastData['aria_role'] ?? $typeConfig['aria_role'] ?? $config['types']['defaults']['aria_role'] ?? 'status';
@endphp

<div
    x-data="toastItem({
        id: '{{ $toastData['id'] }}',
        showInitially: {{ Js::from($toastData['show']) }},
        duration: {{ $duration }},
        autoDismiss: {{ ($duration > 0 && $dismissible) ? 'true' : 'false' }},
        pauseOnHover: {{ Js::from($config['behavior']['pause_on_hover']) }},
        allowSwipe: {{ Js::from($config['behavior']['allow_swipe_to_dismiss'] && $dismissible) }},
        animation: {{ json_encode($animation) }},
        toastData: {{ json_encode($toastData) }}
    })"
    :id="'toast-' + id"
    x-show="show"
    x-transition:enter="{{ $animation['enter_transition_classes'] ?? '' }} duration-{{ $animation['enter_duration'] ?? 300 }}ms {{ $animation['enter_easing'] ?? '' }}"
    x-transition:enter-start="{{ $animation['enter_from'] ?? '' }}"
    x-transition:enter-end="{{ $animation['enter_to'] ?? '' }}"
    x-transition:leave="{{ $animation['leave_transition_classes'] ?? '' }} duration-{{ $animation['leave_duration'] ?? 200 }}ms {{ $animation['leave_easing'] ?? '' }}"
    x-transition:leave-start="{{ $animation['leave_from'] ?? '' }}"
    x-transition:leave-end="{{ $animation['leave_to'] ?? '' }}"
    @if(!empty($animation['hooks']['onEnterStart'])) x-on:alpine:enter:start="{{ $animation['hooks']['onEnterStart'] }}" @endif
    @if(!empty($animation['hooks']['onEnterEnd'])) x-on:alpine:enter:end="{{ $animation['hooks']['onEnterEnd'] }}" @endif
    @if(!empty($animation['hooks']['onLeaveStart'])) x-on:alpine:leave:start="{{ $animation['hooks']['onLeaveStart'] }}" @endif
    @if(!empty($animation['hooks']['onLeaveEnd'])) x-on:alpine:leave:end="{{ $animation['hooks']['onLeaveEnd'] }}" @endif
    class="{{ $layoutConfig['wrapper_classes'] ?? '' }} {{ $bgColor }} {{ $textColor }} w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden relative"
    style="{{ $animation['transform_origin'] ? 'transform-origin: ' . $animation['transform_origin'] . ';' : '' }}"
    role="{{ $ariaRole }}"
    aria-live="{{ $ariaRole === 'alert' ? 'assertive' : 'polite' }}"
    aria-atomic="true"
    tabindex="0"
    @mouseenter="pauseDismissTimer"
    @mouseleave="resumeDismissTimer"
    @focus="pauseDismissTimer"
    @blur="resumeDismissTimer"
    x-init="initToast()"
    x-ref="toastElement"
>
    <div class="flex items-start p-4">
        @if(!empty($icon))
            <div class="flex-shrink-0 {{ $layoutConfig['icon_wrapper_classes'] ?? '' }}">
                {!! $icon !!}
            </div>
        @endif
        <div class="ml-3 w-0 flex-1 {{ $layoutConfig['content_wrapper_classes'] ?? '' }}">
            @if(!empty($toastData['title']))
                <p class="text-sm font-medium">{{ $toastData['title'] }}</p>
            @endif
            <p class="text-sm">{{ $toastData['message'] }}</p>
            @if(!empty($toastData['actions']) && $layoutPresetName === 'with_actions')
                <div class="mt-2 pt-2 {{ $layoutConfig['action_container_classes'] ?? 'flex space-x-3' }}">
                    @foreach($toastData['actions'] as $action)
                        <button type="button" @click="handleAction('{{ $action['handler'] ?? '' }}', {{ json_encode($action['params'] ?? []) }})" class="{{ $action['classes'] ?? 'text-sm font-medium underline' }}">
                            {{ $action['label'] }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
        @if($showCloseButton)
            <div class="ml-4 flex-shrink-0 flex {{ $layoutConfig['close_button_wrapper_classes'] ?? '' }} {{ $closeButtonSettings['position_classes'] ?? '' }}">
                <button type="button" @click.prevent="dismiss()" aria-label="{{ $closeButtonSettings['aria_label'] }}" class="{{ $closeButtonSettings['base_classes'] }} {{ $closeButtonSettings['size_classes'] }} {{ $closeButtonSettings['color_classes'] }} {{ $closeButtonSettings['hover_classes'] }} {{ $closeButtonSettings['transition_classes'] }}">
                    {!! $closeButtonSettings['icon'] !!}
                </button>
            </div>
        @endif
    </div>
    @if($showProgressBar)
        <div class="absolute bottom-0 left-0 right-0 {{ $progressBarSettings['base_class'] ?? '' }} {{ $progressBarHeight }} {{ $progressBarSettings['background']['light'] ?? 'bg-black/10' }} dark:{{ $progressBarSettings['background']['dark'] ?? 'bg-white/10' }} {{ $layoutConfig['progress_bar_classes'] ?? '' }}" x-ref="progressBarContainer">
            <div class="{{ $progressBarForeground }} h-full" style="width: 100%;" x-ref="progressBarElement"></div>
        </div>
    @endif
</div>

@once
<script>
    document.addEventListener('alpine:initializing', () => {
        Alpine.data('toastItem', (options) => ({
            id: options.id,
            show: false, // Start hidden, parent will set to true for entry animation
            duration: options.duration,
            autoDismiss: options.autoDismiss,
            pauseOnHover: options.pauseOnHover,
            allowSwipe: options.allowSwipe,
            animation: options.animation,
            toastData: options.toastData,
            timer: null, paused: false, startTime: null, remaining: options.duration, progressWidth: 100,
            swipeStartX: 0, swipeThreshold: 50,

            initToast() {
                // Show is now controlled by the parent toastContainer for entry animation.
                // We only start the timer when this component's show becomes true.
                this.$watch('show', (newValue) => {
                    if (newValue === true && this.autoDismiss) this.startDismissTimer();
                    else if (newValue === false) this.clearDismissTimer();
                });
                if(options.showInitially) { // If Livewire loaded this toast already visible
                    this.show = true;
                }

                if (this.allowSwipe) {
                    this.$refs.toastElement.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: true });
                    this.$refs.toastElement.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: false });
                    this.$refs.toastElement.addEventListener('touchend', (e) => this.handleTouchEnd(e));
                }
            },
            startDismissTimer() {
                this.clearDismissTimer();
                if (this.duration > 0 && this.autoDismiss) {
                    this.startTime = Date.now();
                    this.remaining = this.duration;
                    if (this.$refs.progressBarElement) {
                        this.$refs.progressBarElement.style.transition = 'width ' + this.remaining + 'ms linear';
                        this.$refs.progressBarElement.style.width = '0%';
                    }
                    this.timer = setTimeout(() => this.dismiss(), this.remaining);
                }
            },
            pauseDismissTimer() {
                if (!this.paused && this.autoDismiss && this.pauseOnHover && this.timer) {
                    this.clearDismissTimer();
                    this.remaining -= (Date.now() - this.startTime);
                    if (this.$refs.progressBarElement) {
                         const currentWidth = (this.remaining / this.duration) * 100;
                         this.$refs.progressBarElement.style.transition = 'none';
                         this.$refs.progressBarElement.style.width = currentWidth + '%';
                    }
                    this.paused = true;
                }
            },
            resumeDismissTimer() {
                if (this.paused && this.autoDismiss && this.pauseOnHover) {
                    this.paused = false;
                    if (this.remaining > 0) {
                        this.startTime = Date.now();
                        if (this.$refs.progressBarElement) {
                            this.$refs.progressBarElement.style.transition = 'width ' + this.remaining + 'ms linear';
                            this.$refs.progressBarElement.style.width = '0%';
                        }
                        this.timer = setTimeout(() => this.dismiss(), this.remaining);
                    } else {
                        this.dismiss();
                    }
                }
            },
            clearDismissTimer() { clearTimeout(this.timer); this.timer = null; },
            dismiss() {
                this.show = false; // Triggers leave animation
                this.$dispatch('dismiss-toast', this.id);
            },
            handleAction(handlerName, params) {
                if (handlerName) {
                    if (handlerName.startsWith('$wire.')) this.$wire.call(handlerName.substring(6), ...params);
                    else if (typeof window[handlerName] === 'function') window[handlerName](...params, this.id, this.toastData);
                    else if (handlerName === 'dismissToast') this.dismiss();
                    else console.warn(`Toast action handler '${handlerName}' not found.`);
                }
            },
            handleTouchStart(e) { if (!this.allowSwipe) return; this.swipeStartX = e.touches[0].clientX; },
            handleTouchMove(e) { if (!this.allowSwipe || this.swipeStartX === 0) return; const dX = e.touches[0].clientX - this.swipeStartX; if (Math.abs(dX) > 10) e.preventDefault(); },
            handleTouchEnd(e) { if (!this.allowSwipe || this.swipeStartX === 0) return; const dX = e.changedTouches[0].clientX - this.swipeStartX; if (Math.abs(dX) > this.swipeThreshold) this.dismiss(); this.swipeStartX = 0; }
        }));
    });
</script>
@endonce
