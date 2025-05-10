import TomSelect from "tom-select";

const teamSelect = new TomSelect('#team-select', {
    valueField: 'id',           
    labelField: 'teamName',    
    searchField: ['teamName'],  
    plugins: ['virtual_scroll'],
    maxOptions: null,
    firstUrl: function (query) {
        return '/api/teams/search?q=' + encodeURIComponent(query);
    },
    preload: 'focus', 
    openOnFocus: true,
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
            <div class="d-inline-block text-start text-truncate">
                    <img src="/storage/${escape(item.teamBanner)}" 
                        class="team-banner object-fit-cover rounded-circle "  
                        onerror="this.src='/assets/images/404q.png';"
                        width="40" height="40"
                    >
                    <p class="mx-3 d-inline-block my-0 py-0">${escape(item.teamName)}</p>
                </div>
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
       
    },
});

