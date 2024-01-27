 <script>
    function toggleDropdown(id) {
        let dropdown = document.querySelector(`#${id}[data-toggle='dropdown']`);
        dropdown.parentElement.click();
    }

    document.getElementById('searchInput').addEventListener('keydown',
        debounce((e) => {
            handleInputBlur();
        }, 1000)
    );

    ["sort", "filter", "search", "sortType", ].forEach((name) => {
        localStorage.removeItem(name);
    })

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
            filter: JSON.parse(localStorage.getItem('filter')),
            sort: JSON.parse(localStorage.getItem('sort')),
            userId: Number("{{ $user->id }}"),
            search: localStorage.getItem("search")
        }

        loadByPost(ENDPOINT, body);
    }

    function setLocalStorageFilter(event) {
        let localItem = localStorage.getItem('filter') ?? null;
        let filter = null;

        if (localItem) filter = JSON.parse(localItem);
        else filter = {};

        let value = event.target.value;

        if (event.target.checked) {
            filter[event.target.name] = value;
        } else {
            delete filter[event.target.name];
        }

        localStorage.setItem('filter', JSON.stringify(filter));
    }

    function setLocalStorageSort(event) {
        let key = event.target.value;
        let localItem = localStorage.getItem('sort') ?? null;
        let sort = null;

        if (localItem) sort = JSON.parse(localItem);
        else sort = {};

        if (event.target.checked) {
            sort[key] = 'asc';
            let iconSpan = document.querySelector(`.${key}SortIcon`);
            iconSpan.innerHTML = "";
            let cloneNode = document.querySelector(`.asc-sort-icon`).cloneNode(true);
            cloneNode.classList.remove('d-none');

            cloneNode.onclick = () => {
                setLocalStorageSortIcon(key);
            }

            iconSpan.appendChild(cloneNode);
        } else {
            delete sort[key];
        }

        localStorage.setItem('sort', JSON.stringify(sort));
    }

    function setLocalStorageSortIcon(key) {
        let input = document.querySelector(`input[value=${key}][type='radio']`);
        let isChecked = input.checked;
        let localItem = localStorage.getItem('sort') ?? null;
        let sort = null;

        if (localItem) sort = JSON.parse(localItem);
        else sort = {};

        let value = 'none';

        if (isChecked) {
            if (key in sort) {
                value = sort[key];
            }

            if (value == 'asc') {
                value = 'desc';
            } else if (value == 'desc') {
                value = 'none';
            } else {
                value = 'asc';
            }

        }

        if (value == 'none') {
            input.checked = false;
        }

        if (input.checked) {
            sort[key] = value;
        } else {
            delete sort[key];
        }

        let iconSpan = document.querySelector(`.${key}SortIcon`);
        iconSpan.innerHTML = "";
        let cloneNode = document.querySelector(`.${value}-sort-icon`).cloneNode(true);
        cloneNode.classList.remove('d-none');

        cloneNode.onclick = () => {
            setLocalStorageSortIcon(key);
        }

        iconSpan.appendChild(cloneNode);
        localStorage.setItem('sort', JSON.stringify(sort));
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
            filter: JSON.parse(localStorage.getItem('filter')),
            sort: JSON.parse(localStorage.getItem('sort')),
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
                    filter: JSON.parse(localStorage.getItem('filter')),
                    sort: JSON.parse(localStorage.getItem('sort')),
                    userId: Number("{{ auth()->user()->id }}"),
                    search: localStorage.getItem("search"),
                    ...params
                }

                infinteLoadMoreByPost(ENDPOINT, body);

            }
        }, 300)
    );
</script>
