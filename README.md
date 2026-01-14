# Laravel Livewire Toasts (vsent/laravel-livewire-toasts)

A highly configurable toast notification package for Laravel 12.x and Livewire 3.x.

## Features

-   Seamless integration with Livewire 3.
-   Rich configuration options via `config/toasts.php`.
-   Multiple toast types (success, error, warning, info, default, custom).
-   Tailwind CSS styling with Dark Mode support.
-   Alpine.js powered animations and interactions.
-   Configurable positions, durations, progress bars, close buttons.
-   Sound effects for notifications (publishable assets).
-   Queueing, priority, and swipe-to-dismiss.
-   Easy-to-use Facade and Helper for dispatching toasts.

## Installation

1.  **Require the package via Composer:**
    ```bash
    composer require vsent/laravel-livewire-toasts
    ```

2.  **Publish Configuration (Optional):**
    To customize the default configuration, publish the `toasts.php` file:
    ```bash
    php artisan vendor:publish --tag=toasts-config
    ```
    This will create `config/toasts.php`.

3.  **Publish Views (Optional):**
    If you want to customize the toast Blade components:
    ```bash
    php artisan vendor:publish --tag=toasts-views
    ```
    This will publish `toast-container.blade.php` and `toast-item.blade.php` to `resources/views/vendor/laravel-livewire-toasts/components/`.

4.  **Publish Sound Assets (Optional):**
    To use the default sound effects or add your own:
    ```bash
    php artisan vendor:publish --tag=toasts-assets
    ```
    This will publish sound files to `public/vendor/laravel-livewire-toasts/sounds/`.

    Alternatively, you can publish all assets for this package by using the provider:
    ```bash
    php artisan vendor:publish --provider="Vsent\LaravelLivewireToasts\Providers\ToastServiceProvider"
    ```
    (This command will list all available publish tags for this provider: `toasts-config`, `toasts-views`, and `toasts-assets`.)

5.  **Include the Livewire Component:**
    Add the Livewire toast container component to your main Blade layout file(s), usually right before the closing `</body>` tag:

    ```html
    <!-- Example in resources/views/layouts/app.blade.php -->
    ...
    <livewire:livewire-toast />
    @livewireScripts <!-- If not already present -->
    </body>
    </html>
    ```

## Usage

You can dispatch toasts from your Livewire components or any other part of your Laravel application using the `Vsent\LaravelLivewireToasts\Helpers\ToastMessage` helper or the `Vsent\LaravelLivewireToasts\Facades\Toast` facade.

Make sure to import the Facade if you use it:
`use Vsent\LaravelLivewireToasts\Facades\Toast;`

**Examples:**

```php
// Basic success toast
Toast::success('Profile updated successfully!');

// Error toast with a title
Toast::error('Payment Failed', 'There was an issue processing your payment.');

// Warning toast
Toast::warning('Your subscription is about to expire.');

// Info toast
Toast::info('A new feature has been added to your account.');

// Default toast
Toast::default('This is a general notification.');

// Customizing a toast (overriding config defaults)
Toast::show(
    message: 'Custom toast message',
    type: 'info', // or 'success', 'error', etc.
    title: 'Custom Title',
    options: [
        'duration' => 10000, // 10 seconds
        'position' => 'bottom-left',
        'dismissible' => false,
        'show_progress' => true,
        'sound' => ['src' => 'custom_alert'], // Refers to key in config.sounds.assets
        // Add more options from config/toasts.php as needed
    ]
);
```

## Configuration Overview

The main configuration file is `config/toasts.php`. Here are some key areas:

*   **`animations`**: Define animation presets (enter/leave transitions, duration, easing).
*   **`behavior`**: Control auto-dismiss, pause on hover, max toasts, queueing, swipe-to-dismiss.
    *   `clear_all_on_navigate`: Clears toasts on Livewire/Turbolinks navigation.
*   **`close_button`**: Configure appearance and behavior of the close button.
*   **`display`**: Set default duration, position (e.g., `top-right`, `bottom-center`), max width.
    *   `z_index`: Controls the stack order of the toast container (default: `1050`).
*   **`progress_bar`**: Enable/disable, set appearance (height, colors).
*   **`queue`**: Configure limits per type and overall.
*   **`sounds`**:
    *   `global.enabled`: Toggle sounds globally.
    *   `global.base_path`: Path to sound files. Published assets will be in `public/vendor/laravel-livewire-toasts/sounds/`, so this path is relative to your `public` directory (e.g., `vendor/laravel-livewire-toasts/sounds/`).
    *   `global.require_interaction_on_mobile`: If true, defers first sound on mobile until user interaction.
    *   `assets`: Define individual sound files and their properties (volume, loop).
    *   `types`: Map toast types to specific sound assets.
*   **`types`**: Define default properties for all toasts and specific overrides for each type (`success`, `error`, etc.).
    *   `defaults`: Base settings for all toasts.
    *   `layouts`: Define different structural layouts for toasts.
    *   Per-type settings include `bg` (background), `text_color`, `icon`, `duration`, `sound`, `priority`, etc. These support Tailwind CSS classes, including `dark:` variants for dark mode.

### Dark Mode

The package uses Tailwind CSS utility classes, including `dark:` variants (e.g., `bg-slate-800 dark:bg-slate-200`). Ensure your main Laravel application has Tailwind's dark mode strategy enabled (e.g., class-based or media-query based) for these to work correctly.

## Prerequisites

*   Laravel 11.x or 12.x
*   Livewire 3.x
*   Tailwind CSS (expected to be set up in your project)
*   Alpine.js (comes with Livewire 3)

---
