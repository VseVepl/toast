<?php
// config/toasts.php
return [
    /*
    |--------------------------------------------------------------------------
    | Toast Notification System Configuration
    |--------------------------------------------------------------------------
    |
    | This consolidated configuration combines all toast notification settings
    | including animations, behavior, display, types, and other features.
    |
    */

    /* Animation Settings */
    'animations' => [
        'preset' => 'slide_from_bottom', // Default animation preset to use
        'presets' => [
            'slide_from_bottom' => [
                'enter_transition_classes' => 'transition',
                'enter_easing' => 'ease-out',
                'enter_duration' => 300, // milliseconds
                'enter_from' => 'opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2',
                'enter_to' => 'opacity-100 translate-y-0 sm:translate-x-0',
                'leave_transition_classes' => 'transition',
                'leave_easing' => 'ease-in',
                'leave_duration' => 200, // milliseconds
                'leave_from' => 'opacity-100',
                'leave_to' => 'opacity-0',
                'delay' => 0, // milliseconds
                'hooks' => [ // Optional JavaScript hooks for Alpine.js transitions
                    'onEnterStart' => null,
                    'onEnterEnd' => null,
                    'onLeaveStart' => null,
                    'onLeaveEnd' => null,
                ],
            ],
            'slide_from_right' => [
                'enter_transition_classes' => 'transition',
                'enter_easing' => 'ease-out',
                'enter_duration' => 300,
                'enter_from' => 'opacity-0 translate-x-full',
                'enter_to' => 'opacity-100 translate-x-0',
                'leave_transition_classes' => 'transition',
                'leave_easing' => 'ease-in',
                'leave_duration' => 200,
                'leave_from' => 'opacity-100 translate-x-0',
                'leave_to' => 'opacity-0 translate-x-full',
                'delay' => 0,
                'hooks' => [],
            ],
            'fade' => [
                'enter_transition_classes' => 'transition',
                'enter_easing' => 'ease-out',
                'enter_duration' => 300,
                'enter_from' => 'opacity-0',
                'enter_to' => 'opacity-100',
                'leave_transition_classes' => 'transition',
                'leave_easing' => 'ease-in',
                'leave_duration' => 200,
                'leave_from' => 'opacity-100',
                'leave_to' => 'opacity-0',
                'delay' => 0,
                'hooks' => [],
            ],
            'scale_fade' => [
                'enter_transition_classes' => 'transition transform',
                'enter_easing' => 'ease-out',
                'enter_duration' => 300,
                'enter_from' => 'opacity-0 scale-95',
                'enter_to' => 'opacity-100 scale-100',
                'leave_transition_classes' => 'transition transform',
                'leave_easing' => 'ease-in',
                'leave_duration' => 200,
                'leave_from' => 'opacity-100 scale-100',
                'leave_to' => 'opacity-0 scale-95',
                'delay' => 0,
                'hooks' => [],
                'transform_origin' => 'bottom right', // CSS transform-origin property for scaling
            ],
            'none' => [ // No animations
                'enter_transition_classes' => '',
                'enter_easing' => '',
                'enter_duration' => 0,
                'enter_from' => '',
                'enter_to' => '',
                'leave_transition_classes' => '',
                'leave_easing' => '',
                'leave_duration' => 0,
                'leave_from' => '',
                'leave_to' => '',
                'delay' => 0,
                'hooks' => [],
            ],
        ],
        'global' => [ // Global animation settings applied unless overridden by presets or types
            'default_enter_duration' => 300,
            'default_enter_easing' => 'ease-out',
            'default_leave_duration' => 200,
            'default_leave_easing' => 'ease-in',
            'default_transition_classes' => 'transition',
            'enable_js_hooks_globally' => false, // Whether to enable Alpine.js transition hooks globally
        ],
    ],

    /* Behavior Settings */
    'behavior' => [
        'auto_dismiss' => true, // Whether toasts should auto-dismiss by default
        'pause_on_hover' => true, // Whether the auto-dismiss timer pauses on hover
        'pause_on_window_blur' => true, // Whether timers pause if the window loses focus
        'reverse_order_on_stack' => false, // Display newest toasts at the top of the stack (if position allows)
        'aria_live_region' => 'polite', // 'polite' (default) or 'assertive' for accessibility
        'allow_swipe_to_dismiss' => true, // Whether toasts can be dismissed by swiping on touch devices
        'max_toasts_display' => 5, // Max number of toasts simultaneously visible across all priorities
        'queue_mode' => 'fifo', // 'fifo' (first-in, first-out) or 'lifo' (last-in, first-out) for general queuing
        'clear_all_on_navigate' => true, // Clear all toasts when a new page load or Livewire navigation occurs
        'duplicate_detection' => [
            'enabled' => false, // Whether to prevent adding duplicate toasts
            'duration_threshold' => 1000, // Time in ms within which a toast is considered a duplicate
        ],
    ],

    /* Close Button Settings */
    'close_button' => [
        'enabled' => true, // Whether a close button is shown by default
        'html_element' => 'button', // The HTML element to use for the close button
        'aria_label' => 'Close notification', // ARIA label for accessibility
        'base_classes' => 'flex items-center justify-center p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-current focus:ring-opacity-50',
        'size_classes' => 'h-5 w-5', // Tailwind classes for size
        'color_classes' => 'text-white', // Tailwind classes for color
        'hover_classes' => 'hover:bg-black/10 hover:text-white/80', // Tailwind classes for hover state
        'position_classes' => 'absolute top-2 right-2', // Tailwind classes for positioning within the toast
        'icon' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>', // SVG icon for the close button
        'transition_classes' => 'transition duration-150 ease-in-out', // Tailwind classes for transition
    ],

    /* Display Settings */
    'display' => [
        'default_duration' => 5000, // Default duration for toasts if not specified (milliseconds)
        'position' => 'top-right', // Default desktop screen position ('top-right', 'bottom-right', etc.)
        'mobile_position' => 'bottom-right', // Default mobile screen position (responsive)
        'max_width' => 'max-w-md', // Tailwind class for maximum width on desktop
        'mobile_max_width' => 'max-w-xs', // Tailwind class for maximum width on mobile
        'z_index' => 1050, // Common z-index for overlays like toasts and modals
    ],

    /* Progress Bar Settings */
    'progress_bar' => [
        'enabled' => true, // Whether progress bars are enabled by default
        'base_class' => 'overflow-hidden',
        'height' => 'h-1', // Tailwind class for default height
        'width' => [
            'default' => 'w-full', // Tailwind class for default width
            'mobile' => 'w-[95%]', // Tailwind class for mobile width
        ],
        'background' => [
            'light' => 'bg-black/10', // Background color for the track in light mode
            'dark' => 'bg-white/10', // Background color for the track in dark mode
        ],
        'transition' => [
            'property' => 'width', // CSS property for transition
            'timing' => 'ease-linear', // CSS easing function
            'duration' => 'duration-[--toast-duration]', // CSS variable-based duration
        ],
        'type_overrides' => [ // Overrides for specific toast types
            'success' => ['foreground' => 'bg-green-400/90 dark:bg-green-500/80'],
            'error' => ['foreground' => 'bg-red-400/90 dark:bg-red-500/80', 'height' => 'h-[2px]'],
            'default' => ['foreground' => 'bg-gray-500 dark:bg-gray-400'], // Default fallback
        ],
    ],

    /* Queue Settings */
    'queue' => [
        'max_toasts' => 6, // Absolute max toasts displayed at once (replaces global_limit)
        'per_type_limit' => 3, // Max toasts of a specific *type* (e.g., max 3 'error' toasts)
        'lifetime' => 10000, // Default queue lifetime for a toast if not dismissed (milliseconds, for memory management)

        /* Priority Configuration */
        'priority' => [
            'enabled' => true, // Whether priority queuing is active
            'levels' => [
                'high' => [
                    'limit' => 2, // Max high priority toasts displayed simultaneously
                    'types' => ['error', 'critical'] // Toast types considered 'high' priority
                ],
                'normal' => [
                    'limit' => 3, // Max normal priority toasts
                    'types' => ['warning', 'info']
                ],
                'low' => [
                    'limit' => 5, // Max low priority toasts
                    'types' => ['default', 'success']
                ],
            ],
            'overflow_behavior' => 'distribute',
            /*
            |--------------------------------------------------------------------------
            | Overflow Behavior Explanation
            |--------------------------------------------------------------------------
            |
            | 'distribute' - When a priority level reaches its limit, remaining toasts
            |               will be distributed to other priority levels if they have capacity
            |               (e.g., a 'high' toast overflows to 'normal' if 'high' is full and 'normal' has space).
            |               If all levels are full, the oldest toast of the lowest priority
            |               (that matches the overflowing toast's type or is generally the lowest)
            |               might be removed or simply queued and not displayed until space.
            |               For this implementation, 'distribute' will mean if a higher
            |               priority has met its limit, it will push out a lower priority
            |               toast if necessary to make space, respecting global limit.
            |
            | 'strict' - Strictly enforce priority limits, never allowing overflow.
            |            If a priority level is full, new toasts for that priority are dropped
            |            or queued without displacing lower priority toasts.
            |            (For this implementation, 'strict' will mean if a limit is met,
            |            new toasts are simply not displayed until space opens up in that specific queue.)
            |
            */
        ],
    ],

    /* Sound Effects */
    'sounds' => [
        'global' => [
            'enabled' => true, // Whether sound effects are globally enabled
            'base_path' => 'sounds/', // Base path for sound files within public/vendor/toast-messages/
            'default_sound' => 'notification.mp3', // Default sound to play if type has no specific sound
            'default_volume' => 1.0, // Global default volume (0.0 to 1.0)
            'default_loop' => false, // Global default loop behavior
            'throttle_ms' => 50, // Minimum time between consecutive sound plays (ms)
            'require_interaction_on_mobile' => true, // If true, the first sound on mobile will only play after a user interaction with the page.
        ],
        'assets' => [ // Define individual sound assets and their properties
            'notification' => [
                'src' => 'notification.mp3',
                'volume' => 0.8,
            ],
            'success' => [
                'src' => 'success.mp3',
                'volume' => 0.9,
            ],
            'error' => [
                'src' => 'error.mp3',
                'volume' => 1.0,
            ],
            'warning' => [
                'src' => 'warning.mp3',
                'playback_rate' => 1.1, // Playback speed (e.g., 1.1 for 110% speed)
            ],
            'info' => [
                'src' => 'info.mp3',
                'loop' => false,
            ],
            'dismiss' => [
                'src' => 'dismiss.mp3',
                'volume' => 0.5,
            ],
            'custom_alert' => [
                'src' => 'custom_alert.mp3',
                'volume' => 0.7,
                'loop' => true,
            ],
        ],
        'types' => [ // Map toast types to sound assets
            'success' => 'success',
            'error' => 'error',
            'warning' => 'warning',
            'info' => 'info',
            'default' => 'notification',
            'critical' => 'error', // Critical toasts might use the error sound
            'custom_notification' => 'custom_alert',
        ],
    ],

    /* Toast Types Configuration */
    'types' => [
        'defaults' => [ // Default properties for all toast types unless overridden
            'duration' => 5000,
            'text_color' => 'text-white',
            'show_progress' => true,
            'dismissible' => true,
            'position' => null, // Will use global display position if not set
            'layout_preset' => 'default', // Default layout to use
            'aria_role' => 'status', // ARIA role for accessibility
            'close_button' => true, // Whether to show a close button for this type
            'sound' => [ // Default sound properties, can be overridden by 'sounds.assets' or specific type
                'src' => null, // No specific sound by default, will use 'sounds.global.default_sound'
                'volume' => null, // Will use 'sounds.global.default_volume'
                'loop' => null, // Will use 'sounds.global.default_loop'
            ],
            'priority' => 'low', // Default priority level for new toasts
            'animation_preset' => null, // Will use 'animations.preset' if not set
        ],
        'layouts' => [ // Define layout presets with Tailwind classes
            'default' => [
                'wrapper_classes' => 'p-4 rounded-lg shadow-lg flex items-center space-x-4',
                'icon_wrapper_classes' => 'flex-shrink-0',
                'content_wrapper_classes' => 'flex-grow',
                'close_button_classes' => 'ml-auto text-current hover:opacity-75',
                'progress_bar_classes' => 'h-1 rounded-b-lg absolute bottom-0 left-0 right-0',
            ],
            // You can add more layout presets here (e.g., 'compact', 'with_actions')
            'with_actions' => [
                'wrapper_classes' => 'p-4 rounded-lg shadow-lg flex flex-col space-y-2',
                'icon_wrapper_classes' => 'flex-shrink-0',
                'content_wrapper_classes' => 'flex-grow',
                'close_button_classes' => 'ml-auto text-current hover:opacity-75',
                'progress_bar_classes' => 'h-1 rounded-b-lg absolute bottom-0 left-0 right-0',
                'action_container_classes' => 'flex justify-end space-x-2 mt-2',
            ],
        ],
        // Define specific toast types and their overrides
        'success' => [
            'bg' => 'bg-green-600 dark:bg-green-700',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            'duration' => 3000,
            'text_color' => 'text-white',
            'aria_role' => 'status',
            'progress_bar' => [
                'bg' => 'bg-green-500', // Foreground color for success progress bar
                'height' => 'h-1', // Can override global progress bar height
            ],
            'sound' => ['src' => 'success'], // Refers to the 'success' asset in 'sounds.assets'
            'priority' => 'low',
        ],
        'error' => [
            'bg' => 'bg-red-600 dark:bg-red-700',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            'duration' => 6000,
            'text_color' => 'text-white',
            'show_progress' => true,
            'aria_role' => 'alert',
            'progress_bar' => ['bg' => 'bg-red-500'],
            'sound' => ['src' => 'error', 'volume' => 0.8],
            'actions' => [ // Example actions for error toasts
                [
                    'label' => 'Retry',
                    'handler' => 'retryErrorHandler', // JavaScript function/method to call
                    'classes' => 'text-white underline hover:opacity-75',
                ],
            ],
            'priority' => 'high',
        ],
        'warning' => [
            'bg' => 'bg-yellow-500 dark:bg-yellow-600',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.174 3.374 1.945 3.374h14.71c1.771 0 2.812-1.874 1.945-3.374L13.94 2.332c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
            'duration' => 4000,
            'text_color' => 'text-gray-800 dark:text-white',
            'progress_bar' => ['bg' => 'bg-yellow-400'],
            'dismissible' => true,
            'sound' => ['src' => 'warning'],
            'priority' => 'normal',
        ],
        'info' => [
            'bg' => 'bg-blue-600 dark:bg-blue-700',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>',
            'duration' => 4000,
            'text_color' => 'text-white',
            'dismissible' => true,
            'progress_bar' => ['bg' => 'bg-blue-500'],
            'sound' => ['src' => 'info'],
            'priority' => 'normal',
        ],
        'default' => [
            'bg' => 'bg-gray-800 dark:bg-gray-900',
            'icon' => '', // No default icon
            'duration' => 3000,
            'text_color' => 'text-white',
            'show_progress' => false,
            'dismissible' => true,
            'progress_bar' => ['bg' => 'bg-gray-700 dark:bg-gray-600'], // Ensure dark variant for default type's progress bar
            'priority' => 'low',
        ],
        'custom_notification' => [
            'bg' => 'bg-purple-600 dark:bg-purple-800',
            'icon' => '<svg class="h-6 w-6 text-purple-100" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.04 5.455 1.31m5.714 0a24.248 24.248 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>', // Placeholder for a custom icon
            'text_color' => 'text-purple-100',
            'duration' => 7000,
            'show_progress' => true,
            'dismissible' => false,
            'layout_preset' => 'with_actions',
            'progress_bar' => ['bg' => 'bg-purple-400'],
            'actions' => [
                [
                    'label' => 'View Details',
                    'handler' => 'viewCustomDetails',
                    'classes' => 'text-purple-100 underline hover:opacity-75',
                ],
                [
                    'label' => 'Dismiss',
                    'handler' => 'dismissToast', // This handler would likely map to Livewire's dismiss method
                    'classes' => 'text-purple-100 font-bold hover:text-purple-50',
                ],
            ],
            'priority' => 'normal',
        ],
        'critical' => [
            'bg' => 'bg-red-800 dark:bg-red-900',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.174 3.374 1.945 3.374h14.71c1.771 0 2.812-1.874 1.945-3.374L13.94 2.332c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
            'duration' => 0, // Duration 0 typically means infinite display until manually dismissed
            'text_color' => 'text-white',
            'show_progress' => false,
            'dismissible' => false, // Cannot be dismissed by user click
            'priority' => 'high',
            'sound' => ['src' => 'error', 'volume' => 1.0, 'loop' => true], // Use 'error' sound, loop it
        ],
    ],
];
