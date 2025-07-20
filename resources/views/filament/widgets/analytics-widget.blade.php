<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Analytics Dashboard
        </x-slot>

        <div id="{{ $widgetId }}" class="analytics-widget">
            <!-- Loading State -->
            <div id="analytics-loading" class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600 dark:text-gray-400">Loading analytics data...</span>
            </div>

            <!-- Error State -->
            <div id="analytics-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="text-red-500 mr-2">⚠️</div>
                    <div class="text-red-700 dark:text-red-300">
                        <strong>Error loading analytics data</strong>
                        <p class="text-sm mt-1" id="error-message">Please check your Firebase configuration and try again.</p>
                    </div>
                </div>
                <button id="retry-analytics" class="mt-3 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors">
                    Retry
                </button>
            </div>

            <!-- Analytics Content -->
            <div id="analytics-content" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- Event Tiers -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Tiers</h3>
                        <div id="event-tiers-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3" id="event-tiers-total">Total: 0</div>
                    </div>

                    <!-- Event Types -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Types</h3>
                        <div id="event-types-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3" id="event-types-total">Total: 0</div>
                    </div>

                    <!-- Esport Titles -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Esport Titles</h3>
                        <div id="esport-titles-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3" id="esport-titles-total">Total: 0</div>
                    </div>

                    <!-- Locations -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Locations</h3>
                        <div id="locations-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3" id="locations-total">Total: 0</div>
                    </div>

                    <!-- Event Names -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Names</h3>
                        <div id="event-names-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3" id="event-names-total">Total: 0</div>
                    </div>

                    <!-- Active Users -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Active Users</h3>
                        <div class="text-3xl font-bold text-purple-600" id="active-users-count">0</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">Unique users tracked</div>
                        <div id="top-users-list" class="space-y-1 mt-3 max-h-32 overflow-y-auto">
                            <div class="text-xs text-gray-500">Loading users...</div>
                        </div>
                    </div>

                    <!-- Social Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Social Actions</h3>
                        <div id="social-actions-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3" id="social-actions-total">Total: 0</div>
                    </div>

                    <!-- Social Target Types -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Social Targets</h3>
                        <div id="social-targets-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3" id="social-targets-total">Total: 0</div>
                    </div>

                    <!-- Form Submissions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Form Submissions</h3>
                        <div id="form-submissions-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3" id="form-submissions-total">Total: 0</div>
                    </div>
                </div>
                
                <!-- Pagination Controls -->
                <div class="mt-6 flex justify-center space-x-2">
                    <button id="prev-page" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Previous
                    </button>
                    <span id="page-info" class="px-4 py-2 text-gray-700 dark:text-gray-300">
                        Page 1
                    </span>
                    <button id="next-page" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const widgetId = '{{ $widgetId }}';
                    let currentPage = 1;
                    const itemsPerPage = 5;
                    
                    function waitForAnalytics() {
                        if (window.getAnalyticsCounts) {
                            initializeAnalyticsDashboard();
                        } else {
                            setTimeout(waitForAnalytics, 100);
                        }
                    }
                    
                    function initializeAnalyticsDashboard() {
                        const loadingEl = document.getElementById('analytics-loading');
                        const errorEl = document.getElementById('analytics-error');
                        const contentEl = document.getElementById('analytics-content');
                        const errorMessageEl = document.getElementById('error-message');
                        
                        function showLoading() {
                            loadingEl.classList.remove('hidden');
                            errorEl.classList.add('hidden');
                            contentEl.classList.add('hidden');
                        }
                        
                        function showError(message) {
                            loadingEl.classList.add('hidden');
                            errorEl.classList.remove('hidden');
                            contentEl.classList.add('hidden');
                            errorMessageEl.textContent = message;
                        }
                        
                        function showContent() {
                            loadingEl.classList.add('hidden');
                            errorEl.classList.add('hidden');
                            contentEl.classList.remove('hidden');
                        }
                        
                        function createCountList(data, containerId, totalId, page = 1) {
                            const container = document.getElementById(containerId);
                            const totalEl = document.getElementById(totalId);
                            
                            if (!data || Object.keys(data).length === 0) {
                                container.innerHTML = '<div class="text-sm text-gray-600 dark:text-gray-400">No data available</div>';
                                totalEl.textContent = 'Total: 0';
                                return;
                            }
                            
                            const entries = Object.entries(data);
                            const total = entries.reduce((sum, [, count]) => sum + count, 0);
                            
                            // Sort by count descending and paginate
                            const sortedEntries = entries.sort(([,a], [,b]) => b - a);
                            const startIndex = (page - 1) * itemsPerPage;
                            const endIndex = startIndex + itemsPerPage;
                            const pageEntries = sortedEntries.slice(startIndex, endIndex);
                            
                            container.innerHTML = '';
                            pageEntries.forEach(([name, count]) => {
                                const div = document.createElement('div');
                                div.className = 'flex justify-between items-center text-sm';
                                div.innerHTML = `
                                    <span class="text-gray-900 dark:text-white truncate pr-2">${name || 'Unknown'}</span>
                                    <span class="text-blue-600 font-medium">${count.toLocaleString()}</span>
                                `;
                                container.appendChild(div);
                            });
                            
                            totalEl.textContent = `Total: ${total.toLocaleString()}`;
                        }
                        
                        async function updateDashboard(page = 1) {
                            showLoading();
                            currentPage = page;
                            
                            try {
                                // Get global counts
                                const globalCounts = await window.getAnalyticsCounts();
                                console.log('Global counts:', globalCounts);
                                
                                if (!globalCounts) {
                                    showError('No analytics data found');
                                    return;
                                }
                                
                                // Update Event Tiers
                                createCountList(globalCounts.eventTiers || {}, 'event-tiers-list', 'event-tiers-total', page);
                                
                                // Update Event Types
                                createCountList(globalCounts.eventTypes || {}, 'event-types-list', 'event-types-total', page);
                                
                                // Update Esport Titles
                                createCountList(globalCounts.esportTitles || {}, 'esport-titles-list', 'esport-titles-total', page);
                                
                                // Update Locations
                                createCountList(globalCounts.locations || {}, 'locations-list', 'locations-total', page);
                                
                                // Update Event Names
                                createCountList(globalCounts.eventNames || {}, 'event-names-list', 'event-names-total', page);
                                
                                // Update Active Users
                                const userIds = globalCounts.userIds || {};
                                const activeUsersCount = Object.keys(userIds).length;
                                document.getElementById('active-users-count').textContent = activeUsersCount.toLocaleString();
                                
                                const topUsersList = document.getElementById('top-users-list');
                                if (activeUsersCount === 0) {
                                    topUsersList.innerHTML = '<div class="text-xs text-gray-500">No users tracked</div>';
                                } else {
                                    const userEntries = Object.entries(userIds).sort(([,a], [,b]) => b - a).slice(0, 10);
                                    topUsersList.innerHTML = '';
                                    userEntries.forEach(([userId, count]) => {
                                        const div = document.createElement('div');
                                        div.className = 'flex justify-between items-center text-xs';
                                        div.innerHTML = `
                                            <span class="text-gray-700 dark:text-gray-300 truncate pr-1">User ${userId}</span>
                                            <span class="text-purple-600 font-medium">${count}</span>
                                        `;
                                        topUsersList.appendChild(div);
                                    });
                                }
                                
                                // For now, show empty data for social and form counts
                                // These would be populated when social/form tracking is implemented
                                createCountList({}, 'social-actions-list', 'social-actions-total', page);
                                createCountList({}, 'social-targets-list', 'social-targets-total', page);
                                createCountList({}, 'form-submissions-list', 'form-submissions-total', page);
                                
                                updatePaginationControls();
                                showContent();
                                
                            } catch (error) {
                                console.error('Error updating analytics dashboard:', error);
                                showError(error.message || 'Failed to load analytics data');
                            }
                        }
                        
                        function updatePaginationControls() {
                            document.getElementById('page-info').textContent = `Page ${currentPage}`;
                            document.getElementById('prev-page').disabled = currentPage <= 1;
                        }
                        
                        // Event listeners
                        document.getElementById('retry-analytics').addEventListener('click', function() {
                            updateDashboard(currentPage);
                        });
                        
                        document.getElementById('prev-page').addEventListener('click', function() {
                            if (currentPage > 1) {
                                updateDashboard(currentPage - 1);
                            }
                        });
                        
                        document.getElementById('next-page').addEventListener('click', function() {
                            updateDashboard(currentPage + 1);
                        });
                        
                        // Initial load
                        updateDashboard();
                    }
                    
                    waitForAnalytics();
                });
            </script>
        @endpush
    </x-filament::section>
</x-filament-widgets::widget>