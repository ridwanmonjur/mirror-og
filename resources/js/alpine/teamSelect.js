import TomSelect from "tom-select";

let bankSelectElement = document.getElementById('bank-select');
let teamSelectElement = document.getElementById('team-select');
let countrySelect = document.getElementById('select2-country2');

if (teamSelectElement) {
    new TomSelect('#team-select', {
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
                    console.log({ json });
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
            </div>`;
            },

        },
    });
} else if (bankSelectElement) {
    let banksInput = document.getElementById('malay-banks');
    let banksInputValue = JSON.parse(banksInput.value ?? []);
    const logoBasePath = '/assets/images/logo/';
    console.log({banksInputValue});
    new TomSelect('#bank-select', {
        labelField: 'name',
        searchField: 'name',
        maxItems: null,
        maxItems:1,
        valueField: 'name',
        options: banksInputValue,
        create: false,
        render: {
            option: function(data, escape) {
                const logo = data.logo;
                let logoHtml = '';
                
                if (logo && logo !== 'null') {
                    logoHtml = `<img width="50" height="30" class="me-2 object-fit-cover rounded rounded-3  border border-secondary" src="${logoBasePath}${escape(logo)}" alt="${escape(data.name)} logo" class="bank-logo" onerror="this.style.display='none'">`;
                } else {
                    logoHtml = `<img width="50" height="30" class="me-2 border border-primary" src="/assets/404q./png" alt="${escape(data.name)} logo" class="bank-logo" onerror="this.style.display='none'">`;

                }
                
                return `<div class="bank-option">
                    ${logoHtml}
                    <span class="bank-name" style="font-size: 1rem;">${escape(data.name)}</span>
                </div>`;
            },
            item: function(data, escape) {
                const logo = data.logo;
                let logoHtml = '';
                
                if (logo && logo !== 'null') {
                    logoHtml = `<img width="50"  class="me-2 border object-fit-cover rounded rounded-3  border border-secondary" height="30" src="${logoBasePath}${escape(logo)}" alt="${escape(data.name)} logo" class="bank-logo" onerror="this.style.display='none'">`;
                }
                
                return `<div class="bank-option">
                    ${logoHtml}
                    <span class="bank-name" style="font-size: 1rem;">${escape(data.name)}</span>
                </div>`;
            }
        },
    });
} else if (countrySelect) {
    async function fetchCountries() {
        try {
            const data = await storeFetchDataInLocalStorage('/countries');
            if (data?.data) {
                countries = data.data;
                
                let tomSelectOptions = [
                    { 
                        value: '', text: '',
                    }
                ];
                
                let regions = [];
                let countryOptions = [];
    
                countries.forEach((value) => {
                    if (value.type === 'region') {
                        regions.push({
                            value: value.name,
                            text: `${value.emoji_flag} ${value.name}`,
                            optgroup: 'Regions'
                        });
                    } else if (value.type === 'country') {
                        countryOptions.push({
                            value: value.name,
                            text: `${value.emoji_flag} ${value.name}`,
                            optgroup: 'Countries'
                        });
                    }
                });
    
                tomSelectOptions = tomSelectOptions.concat(regions, countryOptions);
    
                const selectElement = document.getElementById('select2-country2');
                if (selectElement) {
                    if (selectElement.tomselect) {
                        selectElement.tomselect.destroy();
                    }
    
                    let tomSelectInstance = new TomSelect(selectElement, {
                        optgroups: [
                            { value: 'Regions', label: 'Regions' },
                            { value: 'Countries', label: 'Countries' }
                        ],
                        options: tomSelectOptions,
                        maxOptions: 300,
                        closeAfterSelect: true,
                        maxItems: 1, 
                        valueField: 'value',
                        placeholder: 'Select a country or region...',
                        onItemAdd: function(value, item) {
                            const self = this;
                            
                            // Close and reset input state
                            this.close();
                            
                            // Clear the search input
                            this.setTextboxValue('');
                            
                            // Remove focus to return to inline state
                            setTimeout(() => {
                                self.blur();
                                self.control_input.blur();
                            }, 50);
                        }
                    });

                    if (selectElement?.dataset?.value) {
                        tomSelectInstance.setValue(selectElement.dataset.value);
                    }
                }
            } else {
                errorMessage = "Failed to get data!";
            }
        } catch (error) {
            console.error('Error fetching countries:', error);
        }
    }

    fetchCountries();
}

