// ManageEventFetchProcessor
class FetchVariables {
    constructor() {
        this.sortType = 'desc';
        this.sortKey = '';
        this.filter = {};
        this.search = null;
        this.fetchedPage = 1;
        this.currentPage = 1;
    }

    visualize() {
        console.log({
            filter: this.filter,
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
        return this.filter;
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