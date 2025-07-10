import { DateTime } from "luxon";
import { initOffCanvasListeners, resetBg } from "../custom/resetBg";
import { ProfileData, openModal, ReportFormData } from "../custom/followers";
import { createApp, reactive } from "petite-vue";
import TomSelect from "tom-select";

const myOffcanvas = document.getElementById('profileDrawer');
myOffcanvas.addEventListener('hidden.bs.offcanvas', event => {
    resetBg(teamData?.profile ?? null);
})

const storage = document.querySelector('.team-head-storage');

const routes = {
    allCategories: storage.dataset.allCategories
};

let allCategories = JSON.parse(routes.allCategories);
let allCategoryArray = Object.values(allCategories);

// External state management for categories
const CategoryState = {

    initialized: false,
    _state: reactive({
        defaultCategory: '',
        userCategories: '',
        userCategoriesArr: [],
    }),

    init() {
        if (!this.initialized) {
            if (teamData){
                this._state.defaultCategory = teamData?.default_category_id || '';
                let userCategories = teamData.all_categories || '';
                this._state.userCategories = userCategories;
                this._state.userCategoriesArr = [...this.parseAllCategories(userCategories)];
                this.initialized = true;
                console.log(userCategories, this.parseAllCategories(userCategories));
            }

            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
                const existingTooltip = window.bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (existingTooltip) {
                    existingTooltip.dispose();
                }
                return new window.bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        
    },

    stringifyCategories(categoriesArray) {
        let newString = '';
        if (!categoriesArray || !categoriesArray[0]) return newString;
        categoriesArray.forEach(element => {
            newString += `|${element.id}|`;
        });
        return newString;
    },

    parseAllCategories(userCategories) {
        if (!userCategories || typeof userCategories !== 'string') return [];
    
        const ids = userCategories
            .split('|')
            .filter(id => id !== '') // Removes leading/trailing empty strings
            .map(id => parseInt(id))
            .filter(id => !isNaN(id));
    
        return ids
            .map(id => allCategories[id] ?? allCategories[id.toString()] ?? null)
            .filter(Boolean);
    },
    

    addCategory(categoryId) {
        let checkExists = allCategories[categoryId];
        if (!checkExists) {
            window.Swal.fire({
                icon: 'error',
                text: 'The chosen game no longer exists!'
            })
            return; 
        }
        
        let foundElement = this._state.userCategoriesArr?.find(element => element.id == categoryId);
        if (foundElement) {
            window.Swal.fire({
                icon: 'error',
                text: 'You have already added this game!',
                confirmButtonColor: '#43a4d7'
            })
            
            return; 
        } else {
            let isDefaultInCurrent = this._state.userCategoriesArr?.find(element => element.id == this._state.defaultCategory);
            if (!isDefaultInCurrent) {
                this._state.defaultCategory = categoryId;
            }

            this._state.userCategoriesArr.push(allCategories[categoryId]);
            this._state.userCategories = this.stringifyCategories(this._state.userCategoriesArr);
        }
    },

    removeCategory(event, categoryId) {
        if (!event) return;

        this._state.userCategoriesArr = this._state.userCategoriesArr?.filter(
            element => element.id != categoryId
        );       

        this._state.userCategories = this.stringifyCategories(this._state.userCategoriesArr);
    },

    makeDefaultCategory(event, categoryId) {
        if (!event) return;
        this._state.defaultCategory = categoryId;
        let category = allCategories[categoryId];
        if (!category) {
            window.Swal.fire({
                icon: 'error',
                text: 'The chosen game no longer exists!'
            })
            return; 
        }

        window.Swal.fire({
            title: 'Default Category Updated',
            html: `
                <p>You have made ${category.gameTitle} your default category! Please save to persist this change.</p>
                <img src="/storage/${category.gameIcon}" onerror="this.src='/images/default-fallback.png'" width="64" height="64">
            `,
            confirmButtonColor: '#43a4d7'
        });
    },
    
};

function CategoryManager() {
   
    return {
    init() {
        CategoryState.init();
        this.initializeTomSelect();
        console.log(CategoryState._state);
    },

    get userCategoriesArr() {
        return CategoryState._state.userCategoriesArr;
    },

    get defaultCategory() {
        return CategoryState._state.defaultCategory;
    },
    initializeTomSelect() {
        const categoryEl = document.querySelector('#default-category');

        if (categoryEl && !categoryEl.tomselect) {
            new TomSelect(categoryEl, {
                valueField: 'id',
                labelField: 'gameTitle',
                searchField: 'gameTitle',
                options: allCategoryArray,
                items: [],
                onItemAdd: function(value, item) {
                    const self = this;
                    this.close();
                    this.setTextboxValue('');
                    setTimeout(() => {
                        self.blur();
                        self.control_input.blur();
                    }, 50);
                },
                render: {
                    option: function(data, escape) {
                        return `<div class="category-input text-truncate">
                            ${data.gameIcon ? `<img src="${escape('/storage/' + data.gameIcon)}" class="category-icon me-2">` : ''}
                            <span>${escape(data.gameTitle)}</span>
                        </div>`;
                    },
                    item: function(data, escape) {
                        return `<div class="category-input text-truncate">
                            ${data.gameIcon ? `<img src="${escape('/storage/' + data.gameIcon)}" class="category-icon me-2">` : ''}
                            <span>${escape(data.gameTitle)}</span>
                        </div>`;
                    }
                },
                onChange: function(value) {
                    CategoryState.addCategory(value);
                }
            });
        }
    },
   
    removeCategory: (event, categoryId) => CategoryState.removeCategory(event, categoryId),
    makeDefaultCategory: (event, categoryId) => CategoryState.makeDefaultCategory(event, categoryId),
   
    };
}



initOffCanvasListeners();
teamData.fontColor = teamData?.profile?.fontColor ?? '#2e4b59';
teamData.backgroundColor = teamData?.profile?.backgroundColor ?? '#f4f2f2';

let imageUpload = document.getElementById("image-upload");

function TeamHead() {
    return {
        get gamesTeam() {
            return [CategoryState._state.defaultCategory, CategoryState._state.userCategories];
        },

        get teamStatus () {
            return TeamState.getTeamStatus()
        },

        select2: null,
        isEditMode: false,
        ...teamData,
        country: teamData?.country,
        errorMessage: '',
        isCountriesFetched: false,
        countries:
            [
                {
                    name:  'No country' ,
                    emoji_flag: ''
                }
            ],
        errorMessage: errorInput?.value,
        changeFlagEmoji(event) {
            this.country = event.target.value;
            const countryX = this.countries?.find(c => c.id == this.country);
            this.country_name = countryX?.name || null
            this.country_flag = countryX?.emoji_flag || null
        },
        async fetchCountries() {
            if (this.isCountriesFetched) return;
            try {
                const data = await storeFetchDataInLocalStorage('/countries');
               
                if (data?.data) {
                    this.isCountriesFetched = true;
                    this.countries = data.data;
                    const choices2 = document.getElementById('select2-country3');
                    
                    let countriesHtml = "<option value=''>Choose a region</option>";

                    let regionsHtml = "";
                    let countriesOptionsHtml = "";

                    // Single loop through all data
                    data?.data.forEach((value) => {
                        if (value.type === 'region') {
                            regionsHtml += `
                                <option value='${value.id}'>${value.emoji_flag} ${value.name}</option>
                            `;
                        } else if (value.type === 'country') {
                            countriesOptionsHtml += `
                                <option value='${value.id}'>${value.emoji_flag} ${value.name}</option>
                            `;
                        }
                    });

                    // Add regions optgroup if there are regions
                    if (regionsHtml) {
                        countriesHtml += "<optgroup label='Regions'>";
                        countriesHtml += regionsHtml;
                        countriesHtml += "</optgroup>";
                    }

                    // Add countries optgroup if there are countries
                    if (countriesOptionsHtml) {
                        countriesHtml += "<optgroup label='Countries'>";
                        countriesHtml += countriesOptionsHtml;
                        countriesHtml += "</optgroup>";
                    }

                    if (choices2) {
                        choices2.innerHTML = countriesHtml;
                        let option = choices2.querySelector(`option[value='${this.country}']`);
                        if (option) option.selected = true;
                    }
                } else {
                    this.errorMessage = "Failed to get data!";
                }
            } catch (error) {
                console.error('Error fetching countries:', error);
            }
        },
        async removeProfile(event) {
            event.stopPropagation();
            event.preventDefault();
                imageUpload.value = "";
                uploadedImageList[0].style.backgroundImage = `none`;
                uploadedImageList[1].style.backgroundImage = `none`;
        },
        async submitEditProfile(event) {
            try {
                window.showLoading();
                event.preventDefault();
                let file = imageUpload.files[0];
                let fileFetch = null;
                if (file) {
                    const fileContent = await readFileAsBase64(file);

                    fileFetch = {
                        filename: file.name,
                        type: file.type,
                        size: file.size  / (1024 * 1024),
                        content: fileContent
                    };
                }
                

                const url = event?.target?.dataset?.url;
                if (!url) {
                    console.error('No URL found');
                    console.log({url});
                    return;
                }

                let [defaultCategory, userCategories] = this.gamesTeam;

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken77,
                        'Content-type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        id: this.id,
                        teamName: this.teamName,
                        teamDescription: this.teamDescription,
                        country: this.country,
                        country_flag: this.country_flag,
                        member_limit: this.member_limit,
                        country_name: this.country_name,
                        default_category_id: defaultCategory,
                        all_categories: userCategories,
                        status: String(this.teamStatus[0])?.toLowerCase(),
                        ...(fileFetch && { file: fileFetch })
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    let currentUrl = window.location.href;
                    if (currentUrl.includes('?')) {
                        currentUrl = currentUrl.split('?')[0];
                    }

                    localStorage.setItem('success', true);
                    localStorage.setItem('message', data.message);
                    window.closeLoading();
                    window.location.replace(currentUrl);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                let errorMessage = error.response?.data?.message || error.message || 'Failed to process your request. Please try again later.';

                this.errorMessage = errorMessage;
                window.closeLoading();
            }
        },
        reset() {
            Object.assign(this, teamData);
            document.querySelectorAll('.uploaded-image').forEach((element) => {
                element.style.backgroundImage = `url(/storage/${teamData.teamBanner})`;
            })
        },
        init() {
            this.fetchCountries();
            var backgroundStyles = styles.backgroundStyles;
            var fontStyles = styles.fontStyles;
            var banner = document.getElementById('backgroundBanner');
            banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
            banner.querySelectorAll('.form-control').forEach((element) => {
                element.style.cssText += fontStyles;
            });


            banner.querySelectorAll(".form-color").forEach((element) => {
                element.style.cssText += fontStyles;
                if (teamData?.profile?.borderColor) {
                    element.style.border = `1px solid ${teamData.profile.borderColor}`;
                }
            });
           
        }
    }
}

const TeamState = reactive({
    receiveInvites: teamData.status != 'private',
    needsPermission: teamData.status != 'private' && teamData.status == 'public',

    setReceiveInvites(value) {
        this.receiveInvites = value;
        if (!value) {
            this.needsPermission = false;
        }
    },

    getTeamStatus() {
        if (!this.receiveInvites) {
            return ['Private', 'Team is private & receives no applications to join from participants'];
        } else if (this.needsPermission) {
            return ['Public', 'Participants can request to join but need approval'];
        } else {
            return ['Open', 'Anyone can join without approval'];
        }
    },
});

function TeamSettings() {
    return {
        settings: TeamState,
        getTeamStatus: () => TeamState.getTeamStatus(),
    }
}


window.formatDateLuxon = (date) => {
    if (!date) return 'Not available';
    return  DateTime
        .fromISO(date)
        .toRelative();
}

window.formatDateMySqlLuxon = (mysqlDate, mysqlTime) => {
    const dateTime = DateTime.fromSQL(`${mysqlDate} ${mysqlTime}`);
    const formattedDate = dateTime.toFormat("d MMM yyyy");
    const formattedTime = dateTime.toFormat("h:mma").toUpperCase();
    
    return `${formattedDate} at ${formattedTime}`;
}




window.openModal = openModal;
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    createApp({
        TeamHead,
        ProfileData,
        ReportFormData,
        CategoryManager,
        TeamSettings
    }).mount('.teamhead');
});

