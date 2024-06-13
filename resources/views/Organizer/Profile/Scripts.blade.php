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
        localStorage.setItem('isInited', "false");
        
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
                document.getElementById('backgroundBanner').style.backgroundImage = gradient;
                document.getElementById('backgroundBanner').style.background = 'auto';
            }
        );
        

        window.createColorPicker(document.getElementById('div-color-picker'), 
            (color) => {
                localStorage.setItem('colorOrGradient', color);
                document.getElementById('backgroundBanner').style.backgroundImage = 'auto';
                document.getElementById('backgroundBanner').style.background = color;
            }
        );

        window.createColorPicker(document.getElementById('div-font-color-picker-with-bg'), 
            (color) => {
                document.getElementById('backgroundBanner').style.color = color;
            }
        );

         window.createColorPicker(document.getElementById('div-font-color-picker-with-frame'), 
            (color) => {
                document.querySelectorAll('.uploaded-image').forEach((element)=> {
                    element.style.borderColor = color;
                }) 
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


   
</script>
