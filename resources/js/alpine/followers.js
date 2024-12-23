function alpineProfileData(userOrTeamId, role) {
    return () => ({
        connections: [],
        page: null,
        next_page: {},
        currentTab: 'followers',
        role,
        
        async loadNextPage(){
            if (this.next_page) {
                await this.loadPage(this.page + 1);
            }
        },

        async resetSearch(tab) {
            this.currentTab = tab;

            const searchInput = document.getElementById('search-connections');
            searchInput.value = "";

            await this.loadSearch();
        },

        async blockUser(connectionId) {

        },

        async starUnstarUser(connectionId) {

        },

        async reportUser(connectionId) {

        },

        async searchUser(connectionId) {

        },

        async loadSearch () {
            let page = 0;
            const searchInput = document.getElementById('search-connections');
            let tab = this.currentTab;
            page = this.page;

            let url = `/api/user/${userOrTeamId}/connections?type=${tab}&page=${page}&role=${role}`;
            let searchValue = searchInput.value.trim();
            if (searchValue != "") {
                url += `&search=${searchValue}`;
            }

            const response = await fetch( url );
            const data = await response.json();
            if (tab in data.connections)  {
                
                this.page = 1;

                this.connections = data.connections[tab].data;

                this.next_page = data.connections[tab]?.next_page_url != null ?
                    true: false
            }

        },

        async loadPage(page) {
            const searchInput = document.getElementById('search-connections');
            try {
                let tab = this.currentTab;
                let url = `/api/user/${userOrTeamId}/connections?type=${tab}&page=${page}&role=${role}`;
                let searchValue = searchInput.value.trim();
                if (searchValue != "") {
                    url += `&search=${searchValue}`;
                }

                const response = await fetch( url );
                const data = await response.json();
                console.log(data);
                if (tab in data.connections)  {
                    console.log(data.connections)
                    if (this.page) {
                        let newTab = this.page;
                        newTab++;
                        this.page = newTab;

                        this.connections = [
                            ...this.connections,
                            ...data.connections[tab].data
                         ] ;
                       
                       
                    } else {
                        this.page = 1;

                        this.connections = [
                            ...data.connections[tab].data
                         ] ;
                       
                    }
                  

                    this.next_page =  data.connections[tab]?.next_page_url != null ? true: false; 

               
                }
            } catch (error) {
                console.error('Failed to load page:', error);
            }
        },
        formatDate(date) {
            return  DateTime
                .fromISO(date)
                .toRelative();
        },

        init() {
            window.addEventListener('tabChange', 
                (event)=> {
                    let { tab } = event.detail;
                    this.currentTab = tab;
                    this.loadPage(1);
                }
            )

        },

    });
}

const openModal = (type) => {
    window.dispatchEvent(new CustomEvent('tabChange', {
        detail: {
            message: 'New tab added!',
            tab: type
        }
    }))
    
    let modalElement = document.getElementById("connectionModal");
    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
}

export {
    alpineProfileData,
    openModal
};