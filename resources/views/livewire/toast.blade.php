<div
    x-data="{
        show: @entangle('show').defer,
        duration: {{ $duration }},
        message: @entangle('message'),
        type: @entangle('type'),
        position: @entangle('position'),
        progress: 100,
        intervalId: null,
        get positionClasses() {
            switch (this.position) {
                case 'top-start': return 'top-0 left-0 mt-4 ml-4';
                case 'top-center': return 'top-0 left-1/2 -translate-x-1/2 mt-4';
                case 'top-end': return 'top-0 right-0 mt-4 mr-4';
                case 'middle-start': return 'top-1/2 left-0 -translate-y-1/2 ml-4';
                case 'middle-center': return 'top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2';
                case 'middle-end': return 'top-1/2 right-0 -translate-y-1/2 mr-4';
                case 'bottom-start': return 'bottom-0 left-0 mb-4 ml-4';
                case 'bottom-center': return 'bottom-0 left-1/2 -translate-x-1/2 mb-4';
                case 'bottom-end': return 'bottom-0 right-0 mb-4 mr-4';
                default: return 'top-0 right-0 mt-4 mr-4';
            }
        },
        get typeConfig() {
            const types = {{ json_encode(config('toasts.types')) }};
            return types[this.type] || types['default'];
        },
        get backgroundClasses() {
            return this.typeConfig.background || 'bg-gray-800';
        },
        get textClasses() {
            return this.typeConfig.text || 'text-white';
        },
        get iconHtml() {
            return this.typeConfig.icon || '';
        },
        get closeButtonConfig() {
            return {{ json_encode(config('toasts.close_button')) }};
        },
        get progressBarConfig() {
            return {{ json_encode(config('toasts.progress_bar')) }};
        },
        startTimeout() {
            if (this.duration > 0 && this.show) {
                this.progress = 100;
                if (this.intervalId) clearInterval(this.intervalId);
                const decrement = 100 / (this.duration / 100);
                this.intervalId = setInterval(() => {
                    this.progress -= decrement;
                    if (this.progress <= 0) {
                        this.hide();
                    }
                }, 100);
            }
        },
        hide() {
            this.show = false;
            if (this.intervalId) clearInterval(this.intervalId);
            this.progress = 100; // Reset progress
            // Optionally, tell Livewire component to hide itself completely
            // $wire.call('hideToast');
        },
        init() {
            this.$watch('show', (value) => {
                if (value) {
                    this.startTimeout();
                    // Play sound if enabled
                    @if(config('toasts.sound.enable'))
                        const soundSource = '{{ asset(config('toasts.sound.source')) }}';
                        if (soundSource) {
                            const audio = new Audio(soundSource);
                            audio.volume = {{ config('toasts.sound.volume', 0.5) }};
                            audio.play().catch(e => console.error('Error playing toast sound:', e));
                        }
                    @endif
                } else {
                    if (this.intervalId) clearInterval(this.intervalId);
                    this.progress = 100;
                }
            });

            // If the component is shown on initial load, start the timeout
            if (this.show) {
                this.startTimeout();
            }

            // Listen for global event to show toast (alternative to Livewire events)
            // window.addEventListener('show-toast', event => {
            //     this.message = event.detail.message || 'Default message';
            //     this.type = event.detail.type || 'default';
            //     this.duration = event.detail.duration || {{ config('toasts.duration', 5000) }};
            //     this.position = event.detail.position || '{{ config('toasts.position', 'top-end') }}';
            //     this.show = true;
            // });
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    :class="`fixed z-50 p-4 rounded-md shadow-lg flex items-center max-w-sm w-full ${positionClasses} ${backgroundClasses} ${textClasses}`"
    role="alert"
    aria-live="assertive"
    aria-atomic="true"
    @click.away="if(duration === 0) hide()"
    style="display: none;"
    x-cloak
>
    <!-- Icon -->
    <template x-if="iconHtml">
        <div x-html="iconHtml" class="flex-shrink-0"></div>
    </template>

    <!-- Message -->
    <div class="flex-grow ml-3" x-text="message">
        {{ $message }}
    </div>

    <!-- Close Button -->
    <template x-if="closeButtonConfig.show">
        <button
            @click.stop="hide()"
            type="button"
            :class="`ml-4 -mr-1 flex-shrink-0 p-1 rounded-md focus:outline-none focus:ring-2 focus:ring-white ${closeButtonConfig.classes || ''}`"
            aria-label="Close"
        >
            <span x-html="closeButtonConfig.html || '&times;'"></span>
        </button>
    </template>

    <!-- Progress Bar -->
    <template x-if="progressBarConfig.show && duration > 0">
        <div :class="`absolute bottom-0 left-0 right-0 h-1 ${progressBarConfig.classes || 'bg-black/20'}`">
            <div :class="`h-full ${typeConfig.progress_fill || progressBarConfig.fill_classes || 'bg-white/50'}`" :style="`width: ${progress}%;`"></div>
        </div>
    </template>
</div>

@once
    @push('scripts')
    <script>
        // Helper function to dispatch a toast event easily from anywhere in your JS code
        // window.showGlobalToast = function(message, type = 'default', duration = 5000, position = 'top-end') {
        //     window.dispatchEvent(new CustomEvent('show-toast', {
        //         detail: { message, type, duration, position }
        //     }));
        // }

        // Or, to call a Livewire component method directly (if you have a single global toast component instance)
        // window.showGlobalToast = function(message, type = 'default', duration = 5000, position = 'top-end') {
        //     Livewire.emit('showToast', message, type, duration, position);
        // }
    </script>
    @endpush
@endonce
