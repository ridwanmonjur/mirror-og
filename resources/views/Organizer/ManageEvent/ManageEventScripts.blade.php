 <script>
    const SORT_CONSTANTS = {
        'ASC' : 'asc',
        'DESC': 'desc',
        'NONE': 'none'
    };

    class FetchVariables () {
        
    }
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
        let dropdown = document.querySelector(`#${id}[data-toggle='dropdown']`);
        dropdown.parentElement.click();
    }

    document.getElementById('searchInput').addEventListener('keydown',
        debounce((e) => {
            handleInputBlur();
        }, 1000)
    );

    [STORAGE_KEYS['SORT_TYPE'], STORAGE_KEYS['FILTER'], STORAGE_KEYS["SEARCH"], STORAGE_KEYS["SORT_KEY"] ].forEach((name) => {
        localStorage.removeItem(name);
    })

    localStorage.setItem(STORAGE_KEYS['SORT_TYPE'], SORT_CONSTANTS['ASC']);

    var page = 1;

    function onSubmit(event) {
        
        event.preventDefault();

        let params = convertUrlStringToQueryStringOrObject({
            isObject: true
        });

        if (!params) params = {}

        params.page = 1;

        ENDPOINT = "{{ route('event.search.view') }}";

        let body = {
            ...params,
            filter: JSON.parse(localStorage.getItem(STORAGE_KEYS['FILTER'])),
            sort: { 
                [localStorage.getItem(STORAGE_KEYS['SORT_KEY'])] :
                [localStorage.getItem(STORAGE_KEYS['SORT_TYPE'])]
            },
            userId: Number("{{ $user->id }}"),
            search: localStorage.getItem("search")
        }

        loadByPost(ENDPOINT, body);
    }


    document.querySelectorAll('.sortIcon').forEach((element) => {
        let cloneNode = document.querySelector(`.none-sort-icon`).cloneNode(true);
        element.appendChild(cloneNode);
        cloneNode.classList.remove('d-none');
    })

    function setLocalStorageFilter(event) {
        let localItem = localStorage.getItem(STORAGE_KEYS['FILTER']) ?? null;
        
        let filter = null;

        if (localItem) {
            filter = JSON.parse(localItem);
        } else {
            filter = {};
        }

        let value = event.target.value;

        console.log({filter, value});

        if (event.target.checked) {
            filter[event.target.name] = [...filter[event.target.name] ?? [], value];
        } else {
            filter[event.target.name] = filter[event.target.name].filter(
                _value => _value != value
            );
        }

        console.log({filter, value});

        localStorage.setItem(STORAGE_KEYS['FILTER'], JSON.stringify(filter));
    }

    function setLocalStorageSortKey(key, title) {
        let sortKey = localStorage.getItem(STORAGE_KEYS['SORT_KEY']) ?? null;
        let sortType = localStorage.getItem(STORAGE_KEYS['SORT_TYPE']) ?? null;
        let sortByTitleId = document.getElementById('sortByTitleId');
        
        if (sortByTitleId) {
            sortByTitleId.textContent = title;
        }

        localStorage.setItem(STORAGE_KEYS['SORT_KEY'], key);        
    }

    function setLocalStorageSortType(type,) {
        let sortType = localStorage.getItem(STORAGE_KEYS['SORT_TYPE']) ?? null;
        let sortKey = localStorage.getItem(STORAGE_KEYS['SORT_KEY']) ?? null;

        if (sortType) {
            if (sortType == SORT_CONSTANTS['ASC']) {
                sortType = SORT_CONSTANTS['DESC'];
            } else  if (sortType == SORT_CONSTANTS['DESC']) {
                sortType = SORT_CONSTANTS['ASC'];
            } else {
                sortType = SORT_CONSTANTS['NONE'];
            }
        }
        else { 
            sortType = SORT_CONSTANTS['ASC'];
        }

        localStorage.setItem(STORAGE_KEYS['SORT_TYPE'], type);     
}
</script>
<script>
    const copyUrlFunction = (copyUrl) => {
        navigator.clipboard.writeText(copyUrl).then(function() {
            console.log('Copying to clipboard was successful! Copied: ' + copyUrl);
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

        if (isObject) return params;
        else return convertObjectToURLString(params);
    }

    ENDPOINT = "/organizer/event/?" + convertUrlStringToQueryStringOrObject({
        isObject: false
    });


    function handleInputBlur() {
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
        localStorage.setItem("search", inputValue)

        let body = {
            ...params,
            filter: JSON.parse(localStorage.getItem(STORAGE_KEYS['FILTER'])),
            sort: { 
                [localStorage.getItem(STORAGE_KEYS['SORT_KEY'])] :
                [localStorage.getItem(STORAGE_KEYS['SORT_TYPE'])]
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
        throttle((e) => {

            var windowHeight = window.innerHeight;
            var documentHeight = document.documentElement.scrollHeight;
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop + windowHeight >= documentHeight - 200) {
                
                let params = convertUrlStringToQueryStringOrObject({
                    isObject: true
                });

                page++;

                let body = {};

                if (!params) params = {}

                params.page = page;
                ENDPOINT = "{{ route('event.search.view') }}";

                body = {
                    filter: JSON.parse(localStorage.getItem(STORAGE_KEYS['FILTER'])),
                    sort: { 
                        [localStorage.getItem(STORAGE_KEYS['SORT_KEY'])] :
                        [localStorage.getItem(STORAGE_KEYS['SORT_TYPE'])]
                    },
                    userId: Number("{{ auth()->user()->id }}"),
                    search: localStorage.getItem("search"),
                    ...params
                }

                infinteLoadMoreByPost(ENDPOINT, body);

            }
        }, 300)
    );
</script>
