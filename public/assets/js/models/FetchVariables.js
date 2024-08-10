// ManageEventFetchProcessor
class FetchVariables {
    constructor(formId = null, formKeys = []) {
        this.sortType = 'desc';
        this.sortKey = '';
        this.filter = {};
        this.search = null;
        this.fetchedPage = 1;
        this.currentPage = 1;
        this.formId = formId;
        this.formKeys = formKeys;
    }

    visualize() {
        console.log({
            filter: this.getFilter(),
            search: this.search,
            sortKey: this.sortKey,
            sortType: this.sortType,
            fetchedPage: this.fetchedPage                
        });
    }

    getSortType() {
        return this.sortType;
    }

    getSortKey() {
        return this.sortKey;
    }

    getFilter() {
        let form = document.getElementById(this.formId);
        console.log({form})
        let formData = new FormData(form);
        console.log({formData, keys: [...formData.keys()]})

        let filterValues = {};
        for (let key of this.formKeys) {
            filterValues[key] = formData.getAll(key);
        }

        return filterValues;
    }

    getFetchedPage() {
        return this.fetchedPage;
    }

    getCurrentPage() {
        return this.currentPage;
    }

    getSearch() {
        return this.search;
    }

    setSortKey(value) {
        this.sortKey = value;
    }

    setFilter(value) {
        this.filter = value;
    }

    setFilterDate(date1, date2) {
    }

    setSearch(value) {
        this.search = value;
    }

    setSortType(value) {
        this.sortType = value;
    }

    setFetchedPage(value) {
        this.fetchedPage = value;
    }

    setCurrentPage(value) {
        this.currentPage = value;
    }
}