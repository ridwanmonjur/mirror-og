const storage = document.querySelector('.team-head-storage');

        const routes = {
            signin: storage.dataset.routeSignin,
            profile: storage.dataset.routeProfile,
            teamBanner: storage.dataset.routeTeamBanner,
            backgroundApi: storage.dataset.routeBackgroundApi
        };

        const styles = {
            backgroundStyles: storage.dataset.backgroundStyles,
            fontStyles: storage.dataset.fontStyles
        };

        let teamData = JSON.parse(document.getElementById('teamData').value);
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.addEventListener('alpine:init', () => {

            Alpine.data('alpineDataComponent', () => ({
                select2: null,
                isEditMode: false, 
                ...teamData,
                country: teamData?.country,
                errorMessage: '', 
                isCountriesFetched: false,
                countries: 
                [
                    {
                        name: { en: 'No country' },
                        emoji_flag: ''
                    }
                ], 
                errorMessage: errorInput?.value, 
                changeFlagEmoji() {
                    let countryX = Alpine.raw(this.countries || []).find(elem => elem.id == this.country);
                    this.country_name = countryX?.name.en;
                    this.country_flag = countryX?.emoji_flag;
                },
                async fetchCountries () {
                    if (this.isCountriesFetched) return;
                    try {
                        const data = await storeFetchDataInLocalStorage('/countries');

                        if (data?.data) {
                            this.isCountriesFetched = true;
                            this.countries = data.data;
                            
                            const choices2 = document.getElementById('select2-country3');
                            let countriesHtml = "<option value=''>Choose a country</option>";

                            data?.data.forEach((value) => {
                                countriesHtml +=`
                                    <option value='${value.id}''>${value.emoji_flag} ${value.name.en}</option>
                                `;
                            });
                            console.log({c: this.country});
                            choices2.innerHTML = countriesHtml;
                            let option = choices2.querySelector(`option[value='${this.country}']`);
                            option.selected = true;
                        } else {
                            this.errorMessage = "Failed to get data!";
                        }
                    } catch (error) {
                        console.error('Error fetching countries:', error);
                    }
                },
                async submitEditProfile (event) {
                    try {
                        event.preventDefault(); 
                        const url = event.target.dataset.url;
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                id: this.id, 
                                teamName: this.teamName, 
                                teamDescription: this.teamDescription,
                                country: this.country,
                                country_flag: this.country_flag,
                                country_name: this.country_name
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
                            window.location.replace(currentUrl);
                        } else {
                            this.errorMessage = data.message;
                        }
                    } catch (error) {
                        this.errorMessage = error.message;
                    console.error({error});
                    } 
                },
                reset() {
                    Object.assign(this, teamData);
                },
                init() {
                    this.fetchCountries();
                    var backgroundStyles = styles.backgroundStyles;
                    var fontStyles = styles.fontStyles;
                    console.log({backgroundStyles, fontStyles})
                    var banner = document.getElementById('backgroundBanner');
                    banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
                    banner.querySelectorAll('.form-control').forEach((element) => {
                        element.style.cssText += fontStyles;
                    });
                }
            })
    )});

    let colorOrGradient = null; 
    function applyBackground(event, colorOrGradient) {
        document.querySelectorAll('.color-active').forEach(element => {
            element.classList.remove('color-active');
        });

        event.target.classList.add('color-active');
    }

     function chooseColor(event, color) {
        if (event) applyBackground(event, color);
        document.querySelector("input[name='backgroundColor']").value = color;
        document.querySelector("input[name='backgroundGradient']").value = null;
        localStorage.setItem('colorOrGradient', color);
        document.getElementById('backgroundBanner').style.backgroundImage = 'none';
        document.getElementById('backgroundBanner').style.background = color;
        document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
            cursiveElement.style.backgroundImage = 'none' ;
            cursiveElement.style.background = color ;
        });
        document.getElementById('changeBackgroundBanner').value = null;
    }

    function chooseGradient(event, gradient) {
        console.log({gradient});
        if (event) applyBackground(event, gradient);
        document.querySelector("input[name='backgroundColor']").value = null;
        document.querySelector("input[name='backgroundGradient']").value = gradient;
        localStorage.setItem('colorOrGradient', gradient);
        document.getElementById('backgroundBanner').style.backgroundImage = gradient;
        document.getElementById('backgroundBanner').style.background = 'auto';
         document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
            cursiveElement.style.backgroundImage = gradient ;
            cursiveElement.style.background = 'auto' ;
        });
        document.getElementById('changeBackgroundBanner').value = null;
    }

    let successInput = document.getElementById('successMessage');
    let errorInput = document.getElementById('errorMessage');

    function formRequestSubmitById(message, id) {
        const form = document.getElementById(id);

        if (message) {
            window.dialogOpen(message, ()=> {
                console.log({message, id})
                form?.submit();
            });
        } else {
            form?.submit();
        }
    }

    function visibleElements() {
        let elements = document.querySelectorAll('.show-first-few');

        elements.forEach((element) => element.classList.remove('d-none'));
        let element2 = document.querySelector('.show-more');
        element2.classList.add('d-none');
    }

    let newFunction = function() {
         window.setupFileInputEditor('#changeBackgroundBanner', (file) => {
            if (file) {
                var cachedImage = URL.createObjectURL(file);
                document.getElementById('backgroundBanner').style.backgroundImage = `url(${cachedImage})`;
                document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
                    cursiveElement.style.backgroundImage = `url(${cachedImage})` ;
                    cursiveElement.style.background = 'auto' ;
                });
                document.querySelector("input[name='backgroundColor']").value = null;
                document.querySelector("input[name='backgroundGradient']").value = null;
            }
        });

        localStorage.setItem('isInited', "false");
        
        if (successInput) {
            localStorage.setItem('success', 'true');
            localStorage.setItem('message', successInput.value);
        } else if (errorInput) {
            localStorage.setItem('error', 'true');
            localStorage.setItem('message', errorInput.value);
        }

        // const bgUploadPreview = window.fileUploadPreviewById('file-upload-preview-1');

          window.createGradientPicker(document.getElementById('div-gradient-picker'),
            (gradient) => {
                chooseGradient(null, gradient);
            }
        );
        

        window.createColorPicker(document.getElementById('div-color-picker'), 
            (color) => {
                chooseColor(null, color);
            }
        );

        window.createColorPicker(document.getElementById('div-font-color-picker-with-bg'), 
            (color) => {
                document.querySelector("input[name='fontColor']").value = color;
                let backgroundBanner2 = document.getElementById('backgroundBanner');
                backgroundBanner2.style.color = color;
                document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
                    cursiveElement.style.color = color;
                });

                backgroundBanner.querySelectorAll('.form-control').forEach((element) => {
                    element.style.color = color;
                });

                document.getElementById('team-name').color = color;
            }
        );

        window.createColorPicker(document.getElementById('div-font-color-picker-with-frame'), 
            (color) => {
                document.querySelectorAll('.uploaded-image').forEach((element)=> {
                    document.querySelector("input[name='frameColor']").value = color;
                    element.style.borderColor = color;
                }) 
            }
        );

        window.addEventListener(Events.IMAGE_ADDED, async (e) => {
            const { detail } = e ;
            const file = detail.files[0];
            const fileContent = await readFileAsBase64(file);
            await changeBackgroundDesignRequest({
                backgroundBanner: {
                    filename: file.name,
                    type: file.type,
                    size: file.size,
                    content: fileContent
                }
            }, (data)=> {
                backgroundBanner.style.backgroundImage = `url(/storage/${data.data.backgroundBanner})`;
                backgroundBanner.style.background = 'auto';
            }, (error)=> {
                console.error(error);
            })
        });

        window.loadMessage(); 
    }

    let oldOnLoad = window.onload;
    if (typeof window.onload !== 'function') {
        window.onload = newFunction;
    } else {
        window.onload = function() {
            if (oldOnLoad) {
                oldOnLoad();
            }
            newFunction();
        };
    }

    async function changeBackgroundDesignRequest(body, successCallback, errorCallback) {
        try {
            const response = await fetch(routes.backgroundApi, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-type': 'application/json',
                    'Accept': 'application/json',
                    ...window.loadBearerHeader()
                },
                body: JSON.stringify(body),
            });

            const data = await response.json();
            
            if (data.success) {
                successCallback(data);
            } else {
                errorCallback(data.message);
            }
        } catch (error) {
            errorCallback('There was a problem with the request: ' + error);
        }
    }
    
    let uploadButton = document.getElementById("upload-button");
    let uploadButton2 = document.getElementById("upload-button2");
    let imageUpload = document.getElementById("image-upload");
    let uploadedImageList = document.getElementsByClassName("uploaded-image");
    let uploadedImage = uploadedImageList[0];
    let backgroundBanner = document.getElementById("backgroundBanner")
   
    uploadButton2.addEventListener("click", function() {
        imageUpload.click();
    });

    imageUpload.addEventListener("change", async function(e) {
            const file = e.target.files[0];

            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                try {
                    const response = await fetch(routes.teamBanner, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData,
                    });
                    
                    const data = await response.json();
                    console.log({data})
                    if (data.success) {
                        uploadedImageList[0].style.backgroundImage = `url(/storage/${data.data.fileName})`;
                        uploadedImageList[1].style.backgroundImage = `url(/storage/${data.data.fileName})`;
                    } else {
                        console.error('Error updating member status:', data.message);
                    }
                } catch (error) {
                    console.error('Error approving member:', error);
                }
            }
        });

    async function readFileAsBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = function(event) {
                const base64Content = event.target.result.split(';base64,')[1];
                resolve(base64Content);
            };

            reader.onerror = function(error) {
                reject(error);
            };

            reader.readAsDataURL(file);
        });
    }

    function reddirectToLoginWithIntened(route) {
        route = encodeURIComponent(route);
        window.location.href = `${routes.signin}?url=${route}`;
    }

    function redirectToProfilePage(userId) {
        window.location.href = routes.profile.replace(':id', userId);
    }