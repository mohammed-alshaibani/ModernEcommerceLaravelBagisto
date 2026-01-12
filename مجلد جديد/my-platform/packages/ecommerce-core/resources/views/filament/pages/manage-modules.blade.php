<x-filament-panels::page>
    @if(!$activeModule)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($modules as $module)
                <x-filament::section>
                    <x-slot name="heading">
                        {{ $module['name'] }}
                    </x-slot>
                    
                    <div class="flex justify-end mt-4">
                        <x-filament::button tag="a" href="?module={{ urlencode($module['class']) }}">
                            Manage Data
                        </x-filament::button>
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    @else
        <div class="mb-4">
            <x-filament::button tag="a" href="?" color="gray">
                &larr; Back to Modules
            </x-filament::button>
        </div>

        {{ $this->table }}
    @endif
</x-filament-panels::page>
