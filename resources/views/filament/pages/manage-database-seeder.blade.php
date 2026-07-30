<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Banner -->
        <div class="p-6 bg-amber-500/10 border border-amber-500/20 rounded-xl">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-500 text-white rounded-lg">
                    <x-heroicon-o-circle-stack class="w-8 h-8" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Sample & Production Data Manager</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Populate your application database with Kenya's top Destinations, Hidden Gems, Safari Routes, Operators, and Outdoor Activities.
                    </p>
                </div>
            </div>
        </div>

        <!-- Live Database Stats Grid -->
        @php $stats = $this->getStats(); @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 font-medium">Destinations</div>
                <div class="text-2xl font-bold text-amber-600">{{ $stats['destinations'] }}</div>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 font-medium">Hidden Gems</div>
                <div class="text-2xl font-bold text-amber-600">{{ $stats['gems'] }}</div>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 font-medium">Routes & Trails</div>
                <div class="text-2xl font-bold text-amber-600">{{ $stats['routes'] }}</div>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 font-medium">Tour Operators</div>
                <div class="text-2xl font-bold text-amber-600">{{ $stats['operators'] }}</div>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 font-medium">Group Activities</div>
                <div class="text-2xl font-bold text-amber-600">{{ $stats['activities'] }}</div>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 font-medium">Bookings</div>
                <div class="text-2xl font-bold text-amber-600">{{ $stats['bookings'] }}</div>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 font-medium">Registered Users</div>
                <div class="text-2xl font-bold text-amber-600">{{ $stats['users'] }}</div>
            </div>
        </div>

        <!-- Seeder Control Card -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Populate & Sync Database Data</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Clicking the button below will run the application seeders (DiscoverSeeder, ChallengesAndStoriesSeeder, ExplorersSeeder) to populate authentic sample travel content across all API endpoints.
            </p>
            <div>
                <x-filament::button
                    wire:click="runSeeder"
                    color="amber"
                    icon="heroicon-o-arrow-path"
                    size="lg"
                >
                    Populate Production Sample Data
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
