<script src="{{ asset('/assets/js/participant/carousel.js') }}"></script>

<script>
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const {
        userProfileId,
        userProfileBirthday: birthday,
        backgroundApiUrl,
        signinUrl,
        publicProfileUrl,
        backgroundStyles,
        fontStyles,
        userBannerUrl,
        assetCarouselJs
    } = document.querySelector('.laravel-data-storage').dataset;
    
    var backgroundBanner = document.getElementById("backgroundBanner")
    let backgroundColorInputValue = document.getElementById('backgroundColorInput')?.value;
    let fontColorInputValue = document.getElementById('fontColorInput')?.value;

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
    if (birthday) {
        birthday = new Date(birthday).toISOString().split('T')[0]
    }

    function visibleElements() {
        let elements = document.querySelectorAll('.show-first-few');

        elements.forEach((element) => element.classList.remove('d-none'));
        let element2 = document.querySelector('.show-more');
        element2.classList.add('d-none');
    }

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
            const response = await fetch(backgroundApiUrl, {
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
            const response = await fetch(publicProfileUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
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
        window.location.href = `${signinUrl}?url=${route}`;
    }

    carouselWork();
    window.addEventListener('resize', debounce((e) => {
        carouselWork();
    }, 250));

    function redirectToProfilePage(userId) {
        window.location.href = publicProfileUrl.replace(':id', userId);
    }

   
</script>
