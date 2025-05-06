import TomSelect from "tom-select";

const teamSelect = new TomSelect('#team-select', {
    valueField: 'id',           
    labelField: 'teamName',    
    searchField: ['teamName'],  
    plugins: ['virtual_scroll'],
    maxOptions: 5,
    preload: true, 
    firstUrl: function (query) {
        return '/api/teams/search?q=' + encodeURIComponent(query);
    },
    load: function (query, callback) {
        const url = this.getUrl(query);

        fetch(url)
            .then(response => response.json())
            .then(json => {
                console.log({json}); 
                if (json.has_more) {
                    const next_url = '/api/teams/search?q=' + encodeURIComponent(query) + '&cursor=' + json.next_cursor;
                    this.setNextUrl(query, next_url);
                }

                callback(json.data);
            }).catch((e) => {
                console.error("Error loading data:", e);
                callback();
            });
    },
    render: {
        option: function (item, escape) {
            return `<div class='d-flex justify-content-start align-items-center'>
            <div class="w-50 d-inline-block text-start text-truncate">
                    <img src="/storage/${escape(item.teamBanner)}" 
                        class="team-banner object-fit-cover rounded-circle "  
                        onerror="this.src='/assets/images/404q.png';"
                        width="40" height="40"
                    >
                    <p class="mx-3 d-inline-block my-0 py-0">${escape(item.teamName)}</p>
                    <span class="mx-2 fs-7">${item.country_flag}</span>
                </div>
            <p class="mx-2 d-none text-start w-50 text-muted d-lg-inline-block text-truncate my-0 py-0">${escape(item.teamDescription)}</p>
            </div>`;
        },
        item: function (item, escape) {
            // This is what shows after selection
            return `<div class="d-flex align-items-center border border-secondary px-2 py-1">
                        <img src="/storage/${escape(item.teamBanner)}" 
                        class="team-banner object-fit-cover border border-secondary rounded-circle "  
                        onerror="this.src='/assets/images/404q.png';"
                        width="35" height="35"
                    >
                    <p class="mx-3 d-inline my-0 py-0">${escape(item.teamName)}</p>
                    <span class="mx-2">${item.country_flag}</span>
            </div>`;
        },
        // no_results: function(data, escape) {
        //     return '<div class="no-results">No teams found</div>';
        // },
        onInitialize: function() {
            const self = this;
            
            self.load('');
        },
        loading_more: function (data, escape) {
            return `<div class="loading-more-results w-100 py-2 d-flex align-items-center">
    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
    Loading more teams
</div>`;
        }
    },
});

// document.querySelector('#team-select').addEventListener('focus', function () {
//     if (!teamSelect.isOpen) {
//         if (teamSelect.lastQuery === '') {
//             teamSelect.load('');
//         }
//         teamSelect.open();
//     }

// });