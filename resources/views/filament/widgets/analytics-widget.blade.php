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
                        <p class="text-sm mt-1" id="error-message">Please check your GA4 configuration and try again.</p>
                    </div>
                </div>
                <button id="retry-analytics" class="mt-3 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors">
                    Retry
                </button>
            </div>

            <!-- Analytics Content -->
            <div id="analytics-content" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Page Views -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Page Views</h3>
                        <div class="text-3xl font-bold text-blue-600" id="page-views-total">0</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Today: <span id="page-views-today">0</span> | 
                            Yesterday: <span id="page-views-yesterday">0</span>
                        </div>
                        <div class="text-xs mt-2" id="page-views-change">
                            <span class="text-green-600">+0%</span> from yesterday
                        </div>
                    </div>

                    <!-- Event Interactions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Interactions</h3>
                        <div class="text-3xl font-bold text-green-600" id="event-clicks-total">0</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Registrations: <span id="event-registrations">0</span>
                        </div>
                        <div class="text-xs mt-2">
                            Conversion Rate: <span class="text-blue-600" id="conversion-rate">0%</span>
                        </div>
                    </div>

                    <!-- Active Users -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Active Users</h3>
                        <div class="text-3xl font-bold text-purple-600" id="active-users-total">0</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            New: <span id="new-users">0</span> | 
                            Returning: <span id="returning-users">0</span>
                        </div>
                        <div class="text-xs mt-2">
                            Avg. Session: <span class="text-orange-600" id="session-duration">0</span>min
                        </div>
                    </div>

                    <!-- Real-time Data -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Real-time</h3>
                        <div class="text-3xl font-bold text-red-600" id="realtime-users">0</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">Users active now</div>
                        <div class="text-xs mt-2">
                            Page Views: <span class="text-blue-600" id="realtime-pageviews">0</span>
                        </div>
                    </div>

                    <!-- Top Events -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Events</h3>
                        <div id="top-events-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                    </div>

                    <!-- Social Interactions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Social Activity</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-900 dark:text-white">Follows</span>
                                <span class="text-sm font-medium text-blue-600" id="social-follows">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-900 dark:text-white">Likes</span>
                                <span class="text-sm font-medium text-green-600" id="social-likes">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-900 dark:text-white">Shares</span>
                                <span class="text-sm font-medium text-purple-600" id="social-shares">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Submissions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Form Submissions</h3>
                        <div class="text-3xl font-bold text-indigo-600" id="form-submissions-total">0</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Success Rate: <span class="text-green-600" id="form-success-rate">0%</span>
                        </div>
                        <div id="form-types-list" class="space-y-1 mt-3">
                            <div class="text-xs text-gray-500">Loading form types...</div>
                        </div>
                    </div>

                    <!-- Top Pages -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Pages</h3>
                        <div id="top-pages-list" class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const widgetId = '{{ $widgetId }}';
                    
                    function waitForAnalytics() {
                        if (window.filamentAnalytics) {
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
                        
                        async function updateDashboard() {
                            showLoading();
                            
                            try {
                                const data = await window.filamentAnalytics.fetchAnalyticsData();
                                
                                document.getElementById('page-views-total').textContent = data.pageViews.total.toLocaleString();
                                document.getElementById('page-views-today').textContent = data.pageViews.today.toLocaleString();
                                document.getElementById('page-views-yesterday').textContent = data.pageViews.yesterday.toLocaleString();
                                
                                const changeEl = document.getElementById('page-views-change');
                                const change = data.pageViews.weekly_change;
                                const changeClass = change >= 0 ? 'text-green-600' : 'text-red-600';
                                const changePrefix = change >= 0 ? '+' : '';
                                changeEl.innerHTML = `<span class="${changeClass}">${changePrefix}${change}%</span> from yesterday`;
                                
                                document.getElementById('event-clicks-total').textContent = data.eventInteractions.total_clicks.toLocaleString();
                                document.getElementById('event-registrations').textContent = data.eventInteractions.registrations.toLocaleString();
                                document.getElementById('conversion-rate').textContent = data.eventInteractions.conversion_rate + '%';
                                
                                document.getElementById('active-users-total').textContent = data.userEngagement.active_users.toLocaleString();
                                document.getElementById('new-users').textContent = data.userEngagement.new_users.toLocaleString();
                                document.getElementById('returning-users').textContent = data.userEngagement.returning_users.toLocaleString();
                                document.getElementById('session-duration').textContent = data.userEngagement.session_duration;
                                
                                document.getElementById('realtime-users').textContent = data.realTimeData.active_users_now.toLocaleString();
                                document.getElementById('realtime-pageviews').textContent = data.realTimeData.current_page_views.toLocaleString();
                                
                                const topEventsList = document.getElementById('top-events-list');
                                topEventsList.innerHTML = '';
                                if (data.eventInteractions.top_events.length === 0) {
                                    topEventsList.innerHTML = '<div class="text-sm text-gray-600 dark:text-gray-400">No events found</div>';
                                } else {
                                    data.eventInteractions.top_events.slice(0, 5).forEach(event => {
                                        const div = document.createElement('div');
                                        div.className = 'flex justify-between items-center text-xs';
                                        div.innerHTML = `
                                            <span class="text-gray-900 dark:text-white truncate">${event.name}</span>
                                            <span class="text-blue-600 font-medium">${event.clicks}</span>
                                        `;
                                        topEventsList.appendChild(div);
                                    });
                                }
                                
                                document.getElementById('social-follows').textContent = data.socialInteractions.total_follows.toLocaleString();
                                document.getElementById('social-likes').textContent = data.socialInteractions.total_likes.toLocaleString();
                                document.getElementById('social-shares').textContent = data.socialInteractions.shares.toLocaleString();
                                
                                document.getElementById('form-submissions-total').textContent = data.formSubmissions.total_submissions.toLocaleString();
                                document.getElementById('form-success-rate').textContent = data.formSubmissions.success_rate + '%';
                                
                                const formTypesList = document.getElementById('form-types-list');
                                formTypesList.innerHTML = '';
                                Object.entries(data.formSubmissions.form_types).forEach(([type, count]) => {
                                    const div = document.createElement('div');
                                    div.className = 'flex justify-between items-center text-xs';
                                    div.innerHTML = `
                                        <span class="text-gray-700 dark:text-gray-300">${type}</span>
                                        <span class="text-indigo-600 font-medium">${count}</span>
                                    `;
                                    formTypesList.appendChild(div);
                                });
                                
                                const topPagesList = document.getElementById('top-pages-list');
                                topPagesList.innerHTML = '';
                                if (data.pageViews.top_pages.length === 0) {
                                    topPagesList.innerHTML = '<div class="text-sm text-gray-600 dark:text-gray-400">No pages found</div>';
                                } else {
                                    data.pageViews.top_pages.slice(0, 5).forEach(page => {
                                        const div = document.createElement('div');
                                        div.className = 'flex justify-between items-center text-xs';
                                        div.innerHTML = `
                                            <span class="text-gray-900 dark:text-white truncate">${page.path}</span>
                                            <span class="text-blue-600 font-medium">${page.views}</span>
                                        `;
                                        topPagesList.appendChild(div);
                                    });
                                }
                                
                                
                                
                                showContent();
                                
                            } catch (error) {
                                console.error('Error updating analytics dashboard:', error);
                                showError(error.message || 'Failed to load analytics data');
                            }
                        }
                     
                        document.getElementById('retry-analytics').addEventListener('click', function() {
                            updateDashboard();
                        });
                        
                        updateDashboard();
                        
                        
                    }
                    
                    waitForAnalytics();
                });
            </script>
        @endpush
    </x-filament::section>
</x-filament-widgets::widget>