function alpineProfileData(userOrTeamId, loggedUserId, role) {
    return () => ({
        connections: [],
        page: null,
        next_page: null,
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

        async makeFormRequest(e) {
            const button = e.currentTarget;
            if (!button) return;
        
            const action = button.dataset.action;
            const route = button.dataset.route;
            const inputs = JSON.parse(button.dataset.inputs || '{}');
        
            try {
                switch (action) {
                    case 'friend-request':
                        await makeRequest(route, 'POST', { addUserId: inputs.addUserId });
                        this.connections = this.connections.map((friend)=> {
                            return friend.id == inputs.addUserId ? friend : {
                                ...friend,
                                logged_friendship_status: 'pending' 
                            };
                        })

                        break;
        
                    case 'cancel-request':
                        await makeRequest(route, 'POST', { deleteUserId: inputs.deleteUserId });
                        this.connections = this.connections.map((friend)=> {
                            return friend.id == inputs.addUserId ? friend : {
                                ...friend,
                                logged_friendship_status: null 
                            };
                        })

                        break;
        
                    case 'unfriend':
                        await makeRequest(route, 'POST', { 
                            updateUserId: inputs.updateUserId, 
                            updateStatus: inputs.updateStatus 
                        });
                       
                        this.connections = this.connections.map((friend)=> {
                            return friend.id == inputs.addUserId ? friend : {
                                ...friend,
                                logged_friendship_status: null 
                            };
                        })

                        break;
        
                    case 'follow':
                    case 'unfollow':
                        await makeRequest(route, 'POST', { participant_id: inputs.participant_id });
                        // Toggle follow button state
                        const isFollowing = action === 'unfollow';
                        button.dataset.action = isFollowing ? 'follow' : 'unfollow';
                        button.classList.toggle('btn-primary', !isFollowing);
                        button.classList.toggle('btn-success', isFollowing);
                        button.classList.toggle('text-light', !isFollowing);
                        button.classList.toggle('text-dark', isFollowing);
                        button.textContent = isFollowing ? 'Following' : 'Follow';
                        break;
                }
            } catch (error) {
                // Handle errors (you might want to show a notification to the user)
                console.error('Operation failed:', error);
                alert('Failed to process your request. Please try again later.');
            }
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

            if (loggedUserId) {
                url += `&loggedUserId=${loggedUserId}`;
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

                if (loggedUserId) {
                    url += `&loggedUserId=${loggedUserId}`;
                }

                const response = await fetch( url );
                const data = await response.json();
                console.log(data);
                if (tab in data.connections)  {
                    console.log(data.connections)
                    if (this.page && this.page !== 1) {
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

async function makeRequest(url, method, data) {
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Error making request:', error);
        throw error;
    }
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