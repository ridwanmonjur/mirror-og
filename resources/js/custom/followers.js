function alpineProfileData(userOrTeamId, loggedUserId, isUserSame, role) {
    return () => ({
        connections: [],
        page: null,
        next_page: null,
        loggedUserId,
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

        async followRequest(e) {
            const button = e.currentTarget;
            if (!button) return;

            const action = button.dataset.action;
            const route = button.dataset.route;
            const role = button.dataset.role ;
            const inputId = button.dataset.inputs ;
        
            try {
                const courseMap = {
                    'unfollow': {
                        swalText: "Are you sure you want to unfollow this user?",
                        swalAction: 'Unfollow!'
                    },
                    'follow': {
                        swalText: "Are you sure you want to follow this user?",
                        swalAction: 'Follow!'
                    }
                };
                
                const { swalText, swalAction } = courseMap[action] || {};

                const result = await window.Swal.fire({
                    title: 'Are you sure?',
                    text: swalText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#43a4d7',
                    cancelButtonColor: '#d33',
                    confirmButtonText: swalAction,
                    cancelButtonText: 'No'
                });
                
                if (!result.isConfirmed) {
                    return;
                }

                
                const actionMap = {
                    'PARTICIPANT': {
                        property: 'participant_id',
                    },
                    'ORGANIZER': {
                        property: 'organizer_id',
                    },
                    
                };

                const { property: inputPropertyName } = actionMap[role];
                let inputObject = {
                    [inputPropertyName]: inputId,
                }
                const data = await makeRequest(route, 'POST', inputObject);
                
                if (isUserSame) {
                    const followerSpanAlt = document.querySelector('span[data-following-stats]');
                    const statsData = followerSpanAlt.dataset.followingStats;
                    let followingCount = parseInt(statsData);

                    if (data.isFollowing) {
                        followingCount+=1;
                    } else {
                        followingCount-=1;
                    }

                    followerSpanAlt.dataset.followerStats = followingCount;
                    followerSpanAlt.textContent = `${followingCount} following`;
                    followerSpanAlt.dataset.followingStats = followingCount;
                    if (this.currentTab == 'following') {
                        this.connections = this.connections.filter((user) => {
                            return user.id != inputId
                        });
                        }
                }

                if (!isUserSame || this.currentTab != 'following') {
                    this.connections = this.connections.map((user)=> {
                        return user.id == inputId ? {
                            ...user,
                            logged_follow_status: data.isFollowing 
                        } : user;
                    })
                }
                     
            } catch (error) {
                // Handle errors (you might want to show a notification to the user)
                console.error('Operation failed:', error);
                alert('Failed to process your request. Please try again later.');
            }
        },

        async friendRequest(e) {
            const button = e.currentTarget;
            if (!button) return;

            const action = button.dataset.action;
            const route = button.dataset.route;
            const inputId = button.dataset.inputs;
        
            try {
              
                const actionMap = {
                    'reject-request': {
                        status: 'rejected',
                        property: 'updateUserId',
                        swalText: "Are you sure you want to reject this friend request?",
                        swalAction: 'Reject!'
                    },
                    'friend-request': {
                        status: 'pending',
                        property: 'addUserId',
                        swalText: "Are you sure you want to send this friend request?",
                        swalAction: 'Send!'
                    },
                    'cancel-request': {
                        status: null,
                        property: 'deleteUserId',
                        swalText: "Are you sure you want to delete this friend request?",
                        swalAction: 'Delete!'
                    },
                    'unfriend': {
                        status:  'left',
                        property: 'updateUserId',
                        swalText: "Are you sure you want to unfriend this person?",
                        swalAction: 'Unfriend!'
                    },
                    'acceptt-pending-request': {
                        status:  'accepted',
                        property: 'updateUserId',
                        swalText: "Are you sure you want to accept this friend request?",
                        swalAction: 'Accept!'
                    },
                    'acceptt-rejected-request': {
                        status:  'accepted',
                        property: 'updateUserId',
                        swalText: "Are you sure you want to accept this rejected request?",
                        swalAction: 'Accept!'
                    },
                    'acceptt-left-request': {
                        status:  'accepted',
                        property: 'updateUserId',
                        swalText: "Are you sure you want to re-friend this person?",
                        swalAction: 'Accept!'
                    }
                };                        

                const { status: newStatus, property: inputPropertyName, swalText, swalAction } = actionMap[action] || {};

                console.log({swalAction})

                const result = await window.Swal.fire({
                    title: 'Are you sure?',
                    text: swalText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#43a4d7',
                    cancelButtonColor: '#d33',
                    confirmButtonText: swalAction,
                    cancelButtonText: 'No'
                });

                if (!result.isConfirmed) {
                    return
                }

                let inputObject = {
                    [inputPropertyName]: inputId,
                    ...(newStatus && { updateStatus: newStatus })

                }

                await makeRequest(route, 'POST', inputObject);

                if (isUserSame) {
                    if (action == 'unfriend' || newStatus == 'accepted') {
                        const friendSpanAlt = document.querySelector('span[data-friends-stats]');
                        const statsData = friendSpanAlt.dataset.friendsStats;
                        let freindCount = parseInt(statsData);

                        if (action == 'unfriend') {
                            freindCount-=1;
                        } else {
                            freindCount+=1;

                        }
                        if (freindCount  < 0) {
                            window.location.reload();
                        }
                        
                        friendSpanAlt.dataset.followerStats = freindCount;
                        friendSpanAlt.textContent = `${freindCount} friends`;
                        friendSpanAlt.dataset.followingStats = freindCount;
                    }

                    if (this.currentTab == 'friends') {
                        this.connections = this.connections.filter((user) => {
                            return user.id != inputId
                        });
                    }
                }
                

                if (!isUserSame || this.currentTab != 'friends') {
                    this.connections = this.connections.map((user)=> {
                        return user.id == inputId ? {
                            ...user,
                            logged_friendship_status: newStatus,
                            logged_friendship_actor: loggedUserId 
                        } : user;
                    })
                }
                    
            } catch (error) {
                // Handle errors (you might want to show a notification to the user)
                console.error('Operation failed:', error);
                alert('Failed to process your request. Please try again later.');
            }
        },

        async blockRequest(e) {
            const button = e.currentTarget;
            if (!button) return;

            const action = button.dataset.action;
            const route = button.dataset.route;
            const role = button.dataset.role ;
            const inputId = button.dataset.inputs ;
        
            try {
                const data = await makeRequest(route, 'POST', JSON.stringify({}));
                
                if (isUserSame) {
                     if (this.currentTab == 'following') {
                        this.connections = this.connections.filter((user) => {
                            return user.id != inputId
                        });
                        }
                }

                if (!isUserSame || this.currentTab != 'following') {
                    this.connections = this.connections.map((user)=> {
                        return user.id == inputId ? {
                            ...user,
                            logged_follow_status: data.isFollowing 
                        } : user;
                    })
                }
                     
            } catch (error) {
                // Handle errors (you might want to show a notification to the user)
                console.error('Operation failed:', error);
                alert('Failed to process your request. Please try again later.');
            }
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