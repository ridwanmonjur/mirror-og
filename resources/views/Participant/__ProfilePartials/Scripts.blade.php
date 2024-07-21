<script src="{{ asset('/assets/js/participant/carousel.js') }}"></script>

<script>
    var backgroundBanner = document.getElementById("backgroundBanner")

    var mediaQueryList = window.matchMedia("(min-width: 600px)");

    function handleMediaChange(e) {
        if (e.matches) {
            var elementWidth = backgroundBanner.clientWidth;
            var elementHeight = elementWidth / 3;
            backgroundBanner.style.backgroundSize = `${elementWidth}px ${elementHeight}px`;
            backgroundBanner.style.backgroundRepeat = 'no-repeat';
            backgroundBanner.style.backgroundPosition = 'center';
        } else {
            backgroundBanner.style.backgroundSize = 'cover';
        }
    }

    mediaQueryList.addListener(handleMediaChange);
    handleMediaChange(mediaQueryList);
    
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

    const currentDate = new Date();
    const formattedDate = currentDate.toISOString().split('T')[0];
    document.getElementById('birthdate').setAttribute('max', formattedDate);
    let birthday = '{{$userProfile->participant->birthday}}';
    if (birthday) {
        birthday = new Date(birthday).toISOString().split('T')[0]
    }

    function visibleElements() {
        let elements = document.querySelectorAll('.show-first-few');

        elements.forEach((element) => element.classList.remove('d-none'));
        let element2 = document.querySelector('.show-more');
        element2.classList.add('d-none');
    }

    // const games_data = {{ $userProfile->participant->games_data }};
    window.onload = () => { 
        window.setupFileInputEditor('#changeBackgroundBanner', (file) => {
            if (file) {
                var cachedImage = URL.createObjectURL(file);
                backgroundBanner.style.backgroundImage = `url(${cachedImage})`;
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
                document.getElementById('backgroundBanner').style.color = color;
                document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
                    cursiveElement.style.color = color;
                });
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

        window.loadMessage(); 
    }

    async function changeBackgroundDesignRequest(body, successCallback, errorCallback) {
        try {
            const url = "{{ route('user.userBackgroundApi.action', ['id' => $userProfile->id] ) }}";
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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

    document.addEventListener('alpine:init', () => {
        let gamesDataInput = document.getElementById('games_data_input');
        let regionDataInput = document.getElementById('region_details_input');
        let regionSelectInput = document.getElementById('region_select_input');

        let gamesData = JSON.parse(gamesDataInput.value.trim()); 
        let regionData = JSON.parse(regionDataInput.value.trim()); 
        let userData = JSON.parse(document.getElementById('initialUserData').value);
        let participantData = JSON.parse(document.getElementById('initialParticipantData').value);

        
        Alpine.data('alpineDataComponent', () => {
        return  {
            select2: null,
            isEditMode: false, 
            countries: 
            [
                {
                    name: { en: 'No country' },
                    emoji_flag: ''
                }
            ], 
            user : { ...userData },
            participant: { ...participantData },
            errorMessage: errorInput?.value, 
            isCountriesFetched: false ,
            changeFlagEmoji() {
                let region = this.participant.region.value ?? this.participant.region;
                if (region) {
                    let countryX = Alpine.raw(this.countries || []).find(elem => elem.id == region);
                    this.participant.region_name = countryX?.name.en;
                    this.participant.region_flag = countryX?.emoji_flag;
                }
            },
            reset() {
                this.user = userData;
                this.participant = participantData;
            },
            async fetchCountries () {
                if (this.isCountriesFetched) return;
                try {
                    const data = await storeFetchDataInLocalStorage('/countries');
                    if (data?.data) {
                        this.isCountriesFetched = true;
                        this.countries = data.data;
                        const choices2 = new Choices(document.getElementById('select2-country'), {
                            itemSelectText: "",
                            allowHTML: "",
                            choices: data.data.map((value) => ({
                                label: `${value.emoji_flag} ${value.name.en}`,
                                value: value.id,
                                disabled: false,
                                selected: value.id === this.participant.region,
                            })),
                        });

                        const choicesContainer = document.querySelector('.choices');
                        choicesContainer.style.width = "150px";
                    } else {
                        this.errorMessage = "Failed to get data!";
                    }
                } catch (error) {
                    console.error('Error fetching countries:', error);
                }
            },
            startMessage(){
                Livewire.emit('chatStarted');
            },
            async submitEditProfile (event) {
                try {
                    event.preventDefault(); 
                    const url = event.target.dataset.url; 
                    this.participant.age = Number(this.participant.age);
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: window.loadBearerCompleteHeader(),
                        body: JSON.stringify({
                            participant: Alpine.raw(this.participant),
                            user: Alpine.raw(this.user)
                        }),
                    });             
                    const data = await response.json();
                        
                    if (data.success) {
                        Toast.fire({
                            icon: 'success',
                            text: 'Updated the player successfully!'
                        })
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
         
            init() {
                this.fetchCountries();
                var backgroundStyles = "<?php echo $backgroundStyles; ?>";
                var fontStyles = "<?php echo $fontStyles; ?>";
                var banner = document.getElementById('backgroundBanner');
                banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
               
                
                this.$watch('participant.birthday', value => {
                    const today = new Date();
                    const birthDate = new Date(value);
                    this.participant.age = today.getFullYear() - birthDate.getFullYear();
                    const monthDifference = today.getMonth() - birthDate.getMonth();
                    
                    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                       this.participant.age--;
                    }
                });
            }

        }})
    })
</script>
@if ($isUserSame)
<script>
    const uploadButton = document.getElementById("upload-button");
    const uploadButton2 = document.getElementById("upload-button2");

    const imageUpload = document.getElementById("image-upload");
    const uploadedImageList = document.getElementsByClassName("uploaded-image");
    const uploadedImage = uploadedImageList[0];    
    uploadButton2?.addEventListener("click", function() {
        imageUpload.click();
    });

     imageUpload?.addEventListener("change", async function(e) {
        const file = e.target.files[0];

        try {
            const fileContent = await readFileAsBase64(file);
            const url = "{{ route('participant.userBanner.action', ['id' => $userProfile->id] ) }}";
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-type': 'application/json',
                    'Accept': 'application/json',
                    ...window.loadBearerHeader()
                },
                body: JSON.stringify({
                    file: {
                        filename: file.name,
                        type: file.type,
                        size: file.size,
                        content: fileContent
                        }
                    }),
                });

                const data = await response.json();
                if (data.success) {
                    uploadedImageList[0].style.backgroundImage = `url(${data.data.fileName})`;
                    uploadedImageList[1].style.backgroundImage = `url(${data.data.fileName})`;
                    document.querySelectorAll(".hyperlink-lightbox").forEach((hyperLinkElement) => {
                        hyperLinkElement.setAttribute('href', data.data.fileName);
                    });
                    window.refreshFsLightbox();

                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('There was a problem with the file upload:', error);
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
</script>
@endif
<script>

    function reddirectToLoginWithIntened(route) {
        route = encodeURIComponent(route);
        let url = "{{ route('participant.signin.view') }}";
        url += `?url=${route}`;
        window.location.href = url;
    }

    function showTab(event, tabName, extraClassNameToFilter = "outer-tab") {
        const tabContents = document.querySelectorAll(`.tab-content.${extraClassNameToFilter}`);
        tabContents.forEach(content => {
            content.classList.add("d-none");
        });
        console.log({
            tabContents
        });

        const selectedTab = document.getElementById(tabName);
        selectedTab.classList.remove('d-none');
        selectedTab.classList.add('tab-button-active');

        const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
        tabButtons.forEach(button => {
            button.classList.remove("tab-button-active");
        });
       
        let target = event.currentTarget;
        target.classList.add('tab-button-active');
    }


    carouselWork();
    window.addEventListener('resize', debounce((e) => {
        carouselWork();
    }, 250));

    function redirectToProfilePage(userId) {
        window.location.href = "{{ route('public.participant.view', ['id' => ':id']) }}"
            .replace(':id', userId);
    }

   
</script>
