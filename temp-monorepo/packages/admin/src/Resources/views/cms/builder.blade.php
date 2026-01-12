@php
    $currentLocale = core()->getRequestedLocale();
@endphp

<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.cms.builder.title')
    </x-slot>

    <style>
        /* GrapesJS Enhanced Styling */
        .gjs-wrapper {
            height: calc(100vh - 60px);
        }
        
        #gjs {
            border: none;
        }

        /* Panel Containers */
        .blocks-container {
            width: 280px;
            height: 100%;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid #ddd;
        }

        .layers-container,
        .styles-container,
        .traits-container {
            width: 320px;
            height: 100%;
            overflow-y: auto;
            background: #fff;
            border-left: 1px solid #ddd;
            padding: 10px;
        }

        .layers-container { display: none; }
        .styles-container { display: block; }
        .traits-container { display: none; }

        /* Panel Buttons */
        .panel__devices,
        .panel__basic-actions,
        .panel__switcher {
            display: flex;
            gap: 5px;
        }

        .gjs-pn-btn {
            padding: 8px 12px;
            cursor: pointer;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
           border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .gjs-pn-btn: hover {
            background: #e5e7eb;
        }

        .gjs-pn-btn.gjs-pn-active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        /* Block Categories */
        .gjs-block-category .gjs-title {
            background: #f3f4f6;
            padding: 10px;
            font-weight: 600;
            border-bottom: 1px solid #d1d5db;
        }

        .gjs-block {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin: 10px;
            text-align: center;
            cursor: move;
            transition: all 0.2s;
        }

        .gjs-block:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .gjs-block-label {
            font-size: 13px;
            margin-top: 8px;
        }

        /* Canvas */
        .gjs-cv-canvas {
            background: #f9fafb;
        }

        /* Toolbar */
        .panel__top {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>

    <div class="flex h-screen flex-col">
        <!-- Builder Header -->
        <div class="flex items-center justify-between border-b border-gray-300 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4">
                <a
                    href="{{ route('admin.cms.edit', $page->id) }}"
                    class="icon-arrow-left text-2xl"
                    title="Back to Edit"
                ></a>
                <div>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                        {{ $page->translate($currentLocale->code)['page_title'] ?? 'Page Builder' }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Wix-style Visual Page Builder - Drag components, customize styles, build complete layouts
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Device Switcher -->
                <div class="panel__devices flex items-center gap-2"></div>

                @if ($page->translate($currentLocale->code))
                    <a
                        href="{{ route('shop.cms.page', $page->translate($currentLocale->code)['url_key']) }}"
                        class="secondary-button"
                        target="_blank"
                    >
                        <span class="icon-eye"></span>
                        Preview Live
                    </a>
                @endif

                <!-- Basic Actions -->
                <div class="panel__basic-actions flex items-center gap-2"></div>
            </div>
        </div>

        <!-- Main Builder Area -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Left Sidebar - Blocks -->
            <div class="blocks-container">
                <div style="padding: 15px; border-bottom: 1px solid #ddd;">
                    <h3 style="font-weight: 600; font-size: 16px; margin-bottom: 5px;">Components</h3>
                    <p style="font-size: 12px; color: #6b7280;">Drag blocks onto the canvas</p>
                </div>
                <!-- Blocks will be injected here by GrapesJS -->
            </div>

            <!-- Center Canvas -->
            <div class="flex-1 relative">
                <!-- Top Toolbar -->
                <div class="panel__top">
                    <div class="panel__switcher"></div>
                    <div style="color: #6b7280; font-size: 14px;">
                        <span class="icon-info-circle"></span> Click any element to customize
                    </div>
                </div>

                <!-- GrapesJS Canvas -->
                <v-page-builder
                    :page-id="{{ $page->id }}"
                    ref="builder"
                >
                    <div class="flex items-center justify-center" style="height: calc(100vh - 200px);">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Loading Visual Builder...</p>
                        </div>
                    </div>
                </v-page-builder>
            </div>

            <!-- Right Sidebar - Properties -->
            <div class="flex flex-col" style="width: 320px; border-left: 1px solid #ddd;">
                <!-- Layers Panel -->
                <div class="layers-container">
                    <h3 style="font-weight: 600; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb;">
                        <span class="fa fa-bars"></span> Layers
                   </h3>
                    <!-- Layers will be injected here -->
                </div>

                <!-- Styles Panel -->
                <div class="styles-container">
                    <h3 style="font-weight: 600; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb;">
                        <span class="fa fa-paint-brush"></span> Styles
                    </h3>
                    <!-- Styles will be injected here -->
                </div>

                <!-- Settings/Traits Panel -->
                <div class="traits-container">
                    <h3 style="font-weight: 600; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb;">
                        <span class="fa fa-cog"></span> Settings
                    </h3>
                    <!-- Traits will be injected here -->
                </div>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            // Store builder instance globally for save button access
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    const builderComponent = window.app?._instance?.refs?.builder;
                    if (builderComponent) {
                        window.builderInstance = builderComponent;
                    }
                }, 1000);
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Ctrl+S to save
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    if (window.builderInstance) {
                        window.builderInstance.savePageData();
                    }
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
