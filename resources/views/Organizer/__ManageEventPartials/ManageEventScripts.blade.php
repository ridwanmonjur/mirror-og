 <script src="{{ asset('/assets/js/models/FetchVariables.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/litepicker@2.0/dist/litepicker.min.js"></script>

 <script>
    var isPickerShown = false;
    var picker = new Litepicker({ 
        element: document.getElementById('litepicker'),
        singleMode: false,
        autoApply: false,
        setup: (_picker) => {
            _picker.on('button:apply', (date1, date2) => {
                _date1 = `${date1.getDate()}/${date1.getMonth() + 1}/${date1.getFullYear()}`;
                _date2 = `${date2.getDate()}/${date2.getMonth() + 1}/${date2.getFullYear()}`;
                console.log({isPickerShown, date1, date2, _date1, _date2});
                isPickerShown = false;
                document.getElementById('startDate').value = _date1;
                document.getElementById('endDate').value = _date2;
                console.log({_date1, _date2});
                deleteTagByNameValue('startDate', _date1);
                deleteTagByNameValue('endDate', _date2);
                addFilterTags(`Date: ${_date1} - ${_date2}`, 'durationDate', `${_date1}${_date2}`);
                console.log({isPickerShown});
            });
        }
    });

    function togglePicker(event) {
        if (event) {
            picker?.show(); }
        }

    document.getElementById("dropdownFilterDate")?.addEventListener('click', togglePicker);

    const SORT_CONSTANTS = {
        'ASC' : 'asc',
        'DESC': 'desc',
        'NONE': 'none'
    };

    const STORAGE_KEYS = {
        'SORT_TYPE': 'sortType',
        "FILTER": 'filter',
        "SEARCH": 'search',
        "SORT_KEY" : 'sortKey'
    }

    function stopPropagation(event) {
        event.stopPropagation();
    }

    function toggleDropdown(id) {
        
        let dropdown = document.querySelector(`#${id}[data-bs-toggle='dropdown']`);
        dropdown.parentElement.click();
    }

    document.getElementById('searchInput').addEventListener('keydown',
        debounce((e) => {
            handleSearch();
        }, 1000)
    );

    let fetchVariables = new FetchVariables();    

    function fetchSearchSortFiter() {
        resetNoMoreElement();

        let params = convertUrlStringToQueryStringOrObject({
            isObject: true
        });

        if (!params) params = {}

        params.page = 1;

        ENDPOINT = "{{ route('event.search.view') }}";

        let body = {
            ...params,
            filter: fetchVariables.getFilter(),
            sort: { 
                [fetchVariables.getSortKey()] : fetchVariables.getSortType()
            },
            userId: Number("{{ $user->id }}"),
            search: fetchVariables.getSearch()
        }

        loadByPost(ENDPOINT, body);     
    }

    function setFilterForFetch(event, title) {
        let filter = fetchVariables.getFilter();

        let value = event.target.value;

        if (event.target.checked) {
            filter[event.target.name] = [...filter[event.target.name] ?? [], value];
            addFilterTags(title, event.target.name, event.target.value);

        } else {
            filter[event.target.name] = filter[event.target.name].filter(
                _value => _value != value
            );

            deleteTagByNameValue(event.target.name, event.target.value);
        }

        fetchVariables.setFilter(filter);
        fetchSearchSortFiter();
        fetchVariables.visualize();
    }

    function deleteTagByNameValue(name, value){
        let id = `${name}${value}tag`;
        let tagElement = document.getElementById(id);
        tagElement?.remove();
    }

    function deleteTagByParent(event, name, value) {
        let element = event.currentTarget;
        element.parentElement.remove();

        let filter = fetchVariables.getFilter();

        filter[name] = filter[name]?.filter(
            _value => _value != value
        );

        fetchVariables?.setFilter(filter);
        fetchSearchSortFiter();
        fetchVariables.visualize();
    }

    function addFilterTags(title, name, value) {
        let tagElement = document.getElementById('insertFilterTags');
        tagElement.classList.remove('d-none');
        tagElement.classList.add('d-flex');
        tagElement.innerHTML += `
            <div id='${name}${value}tag' class="me-3 px-3 d-flex justify-content-around" style="background-color: #95AEBD; color: white; border-radius: 30px;"> 
                <span class="me-3"> ${title} </span>
                <svg
                    onclick="deleteTagByParent(event, '${name}', '${value}');"
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle mt-1 cursor-pointer" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                </svg> 
            </div>

        `;
    }

    function setSortForFetch(key, title) {
        let sortKey = fetchVariables.getSortKey();
        let sortType = fetchVariables.getSortType();
        let sortByTitleId = document.getElementById('sortByTitleId');
        sortByTitleId.textContent = title;
        fetchVariables.setSortKey(key);
        fetchVariables.setSortType(sortType);  
        fetchSearchSortFiter();
        fetchVariables.visualize();
    }

    function setFetchSortType(event) {
        let sortType = fetchVariables.getSortType();
        let sortKey = fetchVariables.getSortKey();

        if (sortType && sortKey != "") {
            if (sortType == SORT_CONSTANTS['ASC']) {
                sortType = SORT_CONSTANTS['NONE'];
            } else if (sortType == SORT_CONSTANTS['DESC']) {
                sortType = SORT_CONSTANTS['ASC'];
            } else {
                sortType = SORT_CONSTANTS['DESC'];
            }
        } else { 
           toggleDropdown('dropdownSortButton');
           return;
        }

        let element = document.getElementById("insertSortTypeIcon"); 
        let cloneNode = document.querySelector(`.${sortType}-sort-icon`).cloneNode(true);
        element.insertBefore(cloneNode, element.firstChild);
        event.currentTarget.remove();
        fetchVariables.setSortType(sortType);   
        fetchSearchSortFiter();
        fetchVariables.visualize();  
    }

    const copyUrlFunction2 = (copyUrl) => {
        navigator.clipboard.writeText(copyUrl).then(function() {
        }, function(err) {
            console.error('Could not copy text to clipboard: ', err);
        });
    }

    var ENDPOINT;

    function getQueryStringValue(key) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(key);
    }

    function convertObjectToURLString(object) {
        var queryString = "";
        
        for (const [key, value] of Object.entries(object)) {
            if (Array.isArray(value)) {
                value.forEach(function(value) {
                    queryString += `${key}=${value}&`;
                });
            } else {
                queryString += `${key}=${value}&`;
            }
        }

        return queryString;
    }


    function convertUrlStringToQueryStringOrObject({
        isObject
    } = {
        isObject: false
    }) {
        var queryString = window.location.search;
        var queryString = queryString.substring(1);
        var paramsArray = queryString.split("&");
        var params = {};

        paramsArray.forEach(function(param) {
            var pair = param.split("=");
            var key = decodeURIComponent(pair[0]);
            var value = decodeURIComponent(pair[1] || '');

            if (key.trim() != "") {

                if (key in params) {
                    params[key] = [...params[key], value];
                } else {
                    params[key] = [value];
                }
            }
        });

        if (isObject) {
            return params;
        } else { 
            return convertObjectToURLString(params);
        }
    }

    ENDPOINT = "/organizer/event/?" + convertUrlStringToQueryStringOrObject({
        isObject: false
    });


    function handleSearch() {
        const inputElement = document.getElementById('searchInput');
        const inputValue = inputElement.value;
        const nextSearch = inputElement.nextElementSibling;

        if (nextSearch) {
            if (String(inputValue).trim() === '') {
                nextSearch.classList.add('d-none');
            } else {
                nextSearch.classList.remove('d-none')
            }
        }

        let params = convertUrlStringToQueryStringOrObject({
            isObject: true
        });

        if (!params) params = {}

        params.page = 1;
        ENDPOINT = "{{ route('event.search.view') }}";
        fetchVariables.setSearch(inputValue)

        let body = {
            ...params,
             filter: fetchVariables.getFilter(),
            sort: { 
                [fetchVariables.getSortKey()] : fetchVariables.getSortType()
            },
            userId: Number("{{ auth()->user()->id }}"),
            search: inputValue
        }

        loadByPost(ENDPOINT, body);
    }

    function resetUrl() {
        let url = "{{ route('event.index') }}";
        window.location.href = url;
    }

    function goToCreateScreen() {
        let url = "{{ route('event.create') }}";
        window.location.href = url;
    }

    function openElementById(id) {
        const element = document.getElementById(id);
        
        if (element) {
            element?.classList.remove("d-none");
        }
    }

    function closeElementById(id) {
        const element = document.getElementById(id);

        if (element && !(element.classList.contains("d-none"))) {
            element?.classList.add("d-none");
        }
    }

    const sortByList = ["startDate", "endDate"];

    window.addEventListener(
        "scroll",
        (e) => {

            var windowHeight = window.innerHeight;
            var documentHeight = document.documentElement.scrollHeight;
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop + windowHeight >= documentHeight - 200) {
                
                let params = convertUrlStringToQueryStringOrObject({
                    isObject: true
                });

                fetchVariables.setCurrentPage(fetchVariables.getCurrentPage() + 1);

                let body = {};

                if (!params) { 
                    params = {};
                }

                if ( fetchVariables.getCurrentPage() > fetchVariables.getFetchedPage() + 1 ) {
                    fetchVariables.setCurrentPage( fetchVariables.getFetchedPage() );
                    return;
                }

                fetchVariables.setFetchedPage(fetchVariables.getCurrentPage());

                params.page = fetchVariables.getCurrentPage();
                
                ENDPOINT = "{{ route('event.search.view') }}";

                body = {
                    filter: fetchVariables.getFilter(),
                    sort: { 
                        [fetchVariables.getSortKey()] : fetchVariables.getSortType()
                    },
                    userId: Number("{{ auth()->user()->id }}"),
                    search: fetchVariables.getSearch(),
                    ...params
                }
                try{
                    infinteLoadMoreByPost(ENDPOINT, body)
                } catch {
                    fetchVariables.setFetchedPage(fetchVariables.getCurrentPage()-1);
                };
            }
        }
    );
    
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
