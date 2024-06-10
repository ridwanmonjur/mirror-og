<script>
    let colorOrGradient = null; 
    function applyBackground(event, colorOrGradient) {
        document.querySelectorAll('.color-active').forEach(element => {
            element.classList.remove('color-active');
        });

        event.target.classList.add('color-active');
    }

    function chooseColor(event, color) {
        applyBackground(event, color);
        localStorage.setItem('colorOrGradient', color);
        document.getElementById('backgroundBanner').style.backgroundImage = 'none';
        document.getElementById('backgroundBanner').style.background = color;
    }

    function chooseGradient(event, gradient) {
        console.log({gradient});
        applyBackground(event, gradient);
        localStorage.setItem('colorOrGradient', gradient);
        document.getElementById('backgroundBanner').style.backgroundImage = gradient;
        document.getElementById('backgroundBanner').style.background = 'auto';
    }

    let successInput = document.getElementById('successMessage');
    let errorInput = document.getElementById('errorMessage');

    function formRequestSubmitById(message, id) {
        window.dialogOpen(message, ()=> {
            console.log({message, id})
            const form = document.getElementById(id);
            form?.submit();
        });
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
        localStorage.setItem('isInited', false);
        
        if (successInput) {
            localStorage.setItem('success', 'true');
            localStorage.setItem('message', successInput.value);
        } else if (errorInput) {
            localStorage.setItem('error', 'true');
            localStorage.setItem('message', errorInput.value);
        }

        const bgUploadPreview = window.fileUploadPreviewById('file-upload-preview-1');

        window.createGradientPicker(document.getElementById('div-gradient-picker'),
            (gradient) => {
                localStorage.setItem('colorOrGradient', gradient);
                document.getElementById('backgroundBanner').style.background = 'auto';
                document.getElementById('backgroundBanner').style.backgroundImage = gradient;
            }
        );
        

        window.createColorPicker(document.getElementById('div-color-picker'), 
            (color) => {
                localStorage.setItem('colorOrGradient', color);
                document.getElementById('backgroundBanner').style.backgroundImage = 'auto';
                document.getElementById('backgroundBanner').style.background = color;
            }
        );

        window.createColorPicker(document.getElementById('div-font-color-picker-with-colors'), 
            (color) => {
                document.getElementById('backgroundBanner').style.color = color;
            }
        );

        window.createColorPicker(document.getElementById('div-font-color-picker-with-bg'), 
            (color) => {
                document.getElementById('backgroundBanner').style.color = color;
            }
        );

        window.addEventListener(Events.IMAGE_ADDED, async (e) => {
            const { detail } = e ;

            console.log('detail', detail);
            const file = detail.files[0];
            try {
            const fileContent = await readFileAsBase64(file);
            const url = "{{ route('participant.userBackground.action', ['id' => $userProfile->id] ) }}";
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
                    backgroundBanner.style.backgroundImage = `url(${data.data.fileName})`;
                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('There was a problem with the request:', error);
            }
        });

        window.loadMessage(); 
    }
    document.addEventListener('alpine:init', () => {
        let gamesDataInput = document.getElementById('games_data_input');
        let regionDataInput = document.getElementById('region_details_input');
        let regionSelectInput = document.getElementById('region_select_input');

        let gamesData = JSON.parse(gamesDataInput.value.trim()); 
        let regionData = JSON.parse(regionDataInput.value.trim()); 
        Alpine.data('alpineDataComponent', () => {
        return  {
            isEditMode: false, 
            selectedGame: null,
            isAddGamesMode: true,
            games_data: gamesData,
            countries: 
            [
                {
                    name: { en: 'No country' },
                    emoji_flag: '𓆝'
                }
            ], 
            games: [
                {
                    id: null,
                    name: 'No game',
                    image: null
                }
            ],
            participant: {
                id: {{ $userProfile->participant->id }},
                nickname : '{{$userProfile->participant->nickname}}',
                bio: '{{$userProfile->participant->bio}}',
                age: '{{$userProfile->participant->age}}',
                birthday,
                domain: '{{$userProfile->participant->domain}}',
                region: '{{$userProfile->participant->region}}',
                region_name: regionData?.name.en,
                region_flag: regionData?.emoji_flag
            },
            errorMessage: errorInput?.value, 
            isCountriesFetched: false ,
            async fetchCountries () {
                if (this.isCountriesFetched) return;
                return fetch('/countries')
                    .then(response => response.json())
                    .then(data => {
                        if (data?.data) {
                            this.isCountriesFetched = true;
                            this.countries = data?.data;
                        } else {
                            this.errorMessage = "Failed to get data!"
                            this.countries = [{
                                name: {
                                    en: 'No country'
                                },
                                emoji_flag: ''
                            }];
                        }
                    })
                    .catch(error => console.error('Error fetching countries:', error));
            },
            changeFlagEmoji() {
                let countryX = this.countries.find(elem => elem.id == this.participant.region);
                this.participant.region_name = countryX.name.en;
                this.participant.region_flag = countryX.emoji_flag;
            },
            async fetchGames () {
                if (this.isCountriesFetched) return;
                return fetch('/games')
                    .then(response => response.json())
                    .then(data => {
                        if (data?.data) {
                            this.isCountriesFetched = true;
                            this.games = data?.data;
                            this.select2 = $(this.$refs.select).select2({
                                // minimumResultsForSearch: Infinity,
                                data: data.data,
                                templateResult: function (_game) {
                                    return $("<span><img class='object-fit-cover' width='25' height='25' src='/storage/" + _game.image +"'/> " + _game.name + "</span>");
                                },
                                templateSelection: function (_game) {
                                    return $("<span><img class='object-fit-cover' width='25' height='25' src='/storage/" + _game.image +"'/> " + _game.name + "</span>");
                                },
                                theme: "bootstrap-5",
                            }); 

                            this.select2[0].classList.add('mb-2');
                            
                            this.select2.on('select2:select', (event) => {
                                this.selectedGame = event.target.value;
                                const gameIndex = this.games.findIndex(game => game.id == this.selectedGame);
                                console.log({gameIndex, games: this.games, games_data: this.games_data})
                                const existingIndex = this.games_data.findIndex(game => game.id == this.selectedGame);

                                if (gameIndex !== -1) {
                                    if (existingIndex !== -1) {
                                        Toast.fire({
                                            'icon': 'error',
                                            'text': 'Game already exists!'
                                        })
                                        return;
                                    }
                                    this.games_data = [...this.games_data, this.games[gameIndex]];
                                } else {
                                    Toast.fire({
                                        'icon': 'error',
                                        'text': "Game doesn't exist!"
                                    })
                                }

                                this.isAddGamesMode = false;
                                this.select2[0].classList.add('d-none');
                            });
                             this.$watch("isAddGamesMode", (value) => {
                                console.log({value})
                            });
                            this.$watch("selectedGame", (value) => {
                                this.select2.val(value).trigger("change");
                            });
                        } else {
                            this.errorMessage = "Failed to get data!"
                        }
                    })
                    .catch(error => console.error('Error fetching countries:', error));
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
                            ...Alpine.raw(this.participant),
                            games_data: JSON.stringify(this.games_data),
                        }),
                    });             
                    const data = await response.json();
                        
                    if (data.success) {
                        Toast.fire({
                            icon: 'success',
                            text: 'Updated the player successfully!'
                        })
                        this.isEditMode = false;
                        this.age = data.age;
                    } else {
                        this.errorMessage = data.message;
                    }
                } catch (error) {
                    this.errorMessage = error.message;
                    console.error({error});
                } 
            },
            deleteGames (id) {
                
                const existingIndex = this.games_data.findIndex(game => game.id == id);
                if (existingIndex !== -1) {
                    this.games_data.splice(existingIndex, 1); // Remove 1 element at the found index
                }
            },
            init() {
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
    const imageUpload = document.getElementById("image-upload");
    const uploadedImage = document.getElementById("uploaded-image");
    const backgroundBanner = document.getElementById("backgroundBanner")
    uploadButton?.addEventListener("click", function() {
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
                    uploadedImage.style.backgroundImage = `url(${data.data.fileName})`;
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

    let currentIndex = 0;

    function carouselWork(increment = 0) {
        const eventBoxes = document.querySelectorAll('.event-carousel-works > div');
        let boxLength = eventBoxes?.length || 0;
        let newSum = currentIndex + increment;
        if (newSum >= boxLength || newSum < 0) {
            return;
        } else {
            currentIndex = newSum;
        }

        // carousel top button working
        const button1 = document.querySelector('.carousel-button:nth-child(1)');
        const button2 = document.querySelector('.carousel-button:nth-child(2)');
        if (button1 && button2) {
            button1.style.opacity = (currentIndex <= 2) ? '0.4' : '1';
            button2.style.opacity = (currentIndex >= boxLength - 2) ? '0.4' : '1';

            // carousel swing
            for (let i = 0; i < currentIndex; i++) {
                eventBoxes[i]?.classList.add('d-none');
            }

            for (let i = currentIndex; i < currentIndex + 2; i++) {
                eventBoxes[i]?.classList.remove('d-none');
            }

            for (let i = currentIndex + 2; i < boxLength; i++) {
                eventBoxes[i]?.classList.add('d-none');
            }
        }
    }

    carouselWork();


    function redirectToProfilePage(userId) {
        window.location.href = "{{ route('public.participant.view', ['id' => ':id']) }}"
            .replace(':id', userId);
    }

   
</script>
