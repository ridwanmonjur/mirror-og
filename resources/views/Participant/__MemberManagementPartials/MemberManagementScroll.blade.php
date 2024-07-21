<table class="member-table" id="member-table-body">
    <tbody>
       
    </tbody>
</table>

<div class="tab-size mt-4"> 
    <ul class="pagination cursor-pointer py-3" id="member-table-links">
    </ul>
</div>
{{-- <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script> --}}
<script>
    let newMembersForm = document.getElementById('newMembersForm');
    let newMembersFormKeys = ['sortKeys', 'bod', 'region', 'status'];
    let sortKeysInput = document.getElementById("sortKeys")
    function setSortForFetch(value) {
        console.log({value, sortKeysInput});
        console.log({value, sortKeysInput});
        console.log({value, sortKeysInput});
        document.getElementById("sortKeys").value = value;
    }

    let countries = [];
    newMembersForm.addEventListener('change',
        debounce((event) => {
            let target = event.target;
            let {name, value} = target;
            console.log({newMembersForm: newMembersForm})
            let formData = new FormData(newMembersForm);

            let isAppend = true;
            let targetElemnets = document.querySelectorAll(`small[data-form-name="${name}"]`);

            for (let targetElemnet of targetElemnets) {
                targetElemnet.destroy();
            }

            for (let element of newMembersFormKeys) {
                console.log({key, values: formData.getAll(key)});
            }
            
            targetElemnet = document.createElement('small');
            targetElemnet.classList.add('btn', 'btn-primary', 'text-light', 'px-2', 'py-0');
            targetElemnet.setAttribute('data-form-name', name);
            let filterSearchResultsElem = document.getElementById('filter-search-results');
            filterSearchResultsElem.classList.remove('d-none');
            if (name == 'sortKeys') {
                filterSearchResultsElem.children[1].classList.remove('d-none');
            } else {
                filterSearchResultsElem.children[0].classList.remove('d-none');
            }

            targetElemnet.innerHTML +

            fetchMembers();
        }, 300)
    );

    async function fetchCountries () {
        try {
            const data = await storeFetchDataInLocalStorage('/countries');
            console.log({data})
            if (data?.data) {
                countries = data.data;
                const choices2 = document.getElementById('select2-country2');
                let countriesHtml = '';
                countries.forEach((value) => {
                    countriesHtml +=`
                        <option value='${value.id}''>${value.emoji_flag} ${value.name.en}</option>
                    `;
                });

                choices2.innerHTML = countriesHtml;
                console.log({choices2, countriesHtml});
                /*
                const choices2 = new Choices(document.getElementById('select2-country2'), {
                    itemSelectText: "",
                    allowHTML: "",
                    choices: data.data.map((value) => ({
                        label: `${value.emoji_flag} ${value.name.en}`,
                        value: value.id,
                        disabled: false,
                        selected: false,
                    })),
                });

                const choicesContainer = document.querySelector('.choices');
                choicesContainer.style.width = "9.375rem";
                */
            } else {
                errorMessage = "Failed to get data!";
            }
        } catch (error) {
            console.error('Error fetching countries:', error);
        }
    }

    async function fetchMembers(event = null) {
        let route;
        let bodyHtml = '', pageHtml = '';
        let teamId = document.getElementById('teamId')?.value;
        if (event?.target && event.target?.dataset?.url) {
                route = event.target.dataset.url;
        } else {
            route = document.getElementById('membersUrl')?.value;
        }
        
        console.log({route});
        console.log({route});
        let formData = new FormData(newMembersForm);
        let jsonObject = {}
        for (let [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }

        let links = [];
        data = await fetch(route, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                teamId,
                ...jsonObject
            })
        });

        data = await data.json();
        
        if (data.success && 'data' in data) {
            users = data?.data?.data;
            links = data?.data?.links;
            console.log({users});
            for (user of users) {
                console.log({user});
                bodyHtml+=`
                    <tr class="st">
                        <td class="colorless-col px-0 mx-0">
                            <svg 
                                onclick="redirectToProfilePage(${user.id});"
                                class="gear-icon-btn"
                                xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path
                                    d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>
                        </td>
                        <td class="coloured-cell px-1">
                            <div class="player-info">
                                <img 
                                    onerror="this.onerror=null;this.src='/assets/images/404.png';"
                                    width="45" height="45" 
                                    src="/storage/${user.userBanner}"
                                    class="mx-2 random-color-circle object-fit-cover rounded-circle"
                                >
                                <span>${user.name}</span>
                            </div>
                        </td>
                        <td class="flag-cell coloured-cell px-3 fs-4">
                            <span>${user.participant.region_flag}</span>
                        </td>
                         <td class="coloured-cell px-3">
                            ${user.is_in_team ?
                                'Team status ' + user.members[0].status
                            :
                                'Not in team'
                            }
                        </td>
                        <td class="colorless-col" style="min-width: 1.875rem;">
                            <div class="gear-icon-btn ${user.is_in_team && 'd-none'}" onclick="inviteMember('${user.id}', '${teamId}')">
                                    <img src="/assets/images/add.png" height="1.5625rem" width="1.5625rem">
                            </div>
                        </td>
                      
                    </tr>
                `;
            }

            for (let link of links) {
                pageHtml += `
                    <li
                        data-url='${link.url}'
                        onclick="{ fetchMembers(event); }"  
                        class="page-item ${link.active && 'active'} ${link.url && 'disabled'}" 
                    > 
                        <a 
                            onclick="event.preventDefault()"
                            class="page-link ${link.active && 'text-light'}"
                        > 
                            ${link.label}
                        </a>
                    </li>
                `;
            }

        }

        let tbodyElement = document.querySelector('#member-table-body tbody');
        console.log({tbodyElement, bodyHtml, pageHtml});
        tbodyElement.innerHTML = bodyHtml;  
        let pageLinks = document.querySelector('#member-table-links');
        console.log({tbodyElement, bodyHtml, pageHtml});
        pageLinks.innerHTML = pageHtml; 
    };

    fetchMembers();
    fetchCountries();
</script>  
