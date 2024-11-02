<div class="offcanvas offcanvas-start fade" id="profileDrawer" tabindex="2" aria-labelledby="#profileDrawer"
    aria-hidden="true">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Change your profile's background</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        
        <form id="updateBgColorRequest" class="d-inline" method="POST" enctype="multipart/form-data"
            action="{{ route('user.userBackground.action', ['id' => $userProfile->id]) }}">
            @csrf
            <div class="d-flex justify-content-between text-justify pb-2">
                <div>
                    <p class="m-0 px-0" > 
                        <svg xmlns="http://www.w3.org/2000/svg" style="marin-left: -2px;" width="22" height="22" fill="#5fb2dd" class="bi bi-check2-circle me-2 " viewBox="0 0 16 16">
                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                        </svg>
                        Save these changes
                        
                    </p>
                    <small class="mt-2"> 
                        {{-- check fill --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-exclamation-octagon ms-1 me-2" viewBox="0 0 16 16">
                            <path d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1z"/>
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                    
                        <span> Please save to persist changes in database. </span>
                    </small>
                </div>
                <button type="submit" 
                    style="flex-basis: 80px; height: 40px;"
                    class="btn btn-primary text-light rounded-pill"
                >Save</button>
            </div>
            
            <br>
            <input type="hidden" name="backgroundColor" value="{{ $userProfile->profile?->backgroundColor }}">
            <input type="hidden" name="backgroundGradient" value="{{ $userProfile->profile?->backgroundGradient }}">
             <input type="hidden" name="frameColor" value="{{ $userProfile->profile?->frameColor }}">
            <input type="hidden" name="fontColor" value="{{ $userProfile->profile?->fontColor }}">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button ps-1 " type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne">
                            Change background
                        </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse show py-2 px-2"
                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionExample">
                        <span class="mt-3 mb-2"> Background upload</span>
                       
                        <div class="input-group">
                            <input type="file" class="form-control"
                                id="changeBackgroundBanner" name="backgroundBanner" 
                                aria-describedby="inputGroupFileAddon03" aria-label="Upload"
                                style="font-size: 0.9375rem;"
                            >
                        </div>
                         <small class="d-block mb-4 mt-2"> 
                            {{-- check fill --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5fb2dd" class="bi bi-check-circle-fill mt-1" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                            <span> Please chose a 3:1 to 4:1 ratio and adjust your image accordingly. </span>
                        </small>
                        <div class="mx-auto">
                            <span class="my-2">Solid color</span>
                            <div class="mx-auto">
                                <div data-bs-auto-close="outside" class="d-inline-block rainbow-hue"
                                    id="dropdownColorButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                </div>
                                <div class="dropdown-menu" aria-labelledby="dropdownColorButton">
                                    <div id="div-color-picker"> </div>
                                </div>
                                @foreach ([
                                    'black' => 'Black',
                                    '#545454' => '#545454',
                                    '#737373' => '#737373',
                                    'gray' => 'Gray',
                                    'lightgray' => 'Light gray',
                                    'white' => 'White',
                                    '#e0ffff' => 'Light Cyan',
                                    '#add8e6' => 'Light Blue',
                                    '#b0c4de' => 'Light Steel Blue',
                                    '#ffe4b5' => 'Moccasin',
                                    '#ffffe0' => 'Light Yellow',
                                    '#f0e68c' => 'Khaki',
                                    '#dda0dd' => 'Plum',
                                    '#cd5c5c' => 'Indian Red',
                                    '#ffa07a' => 'Light Salmon',
                                    '#ff7f50' => 'Coral',
                                    '#ff6347' => 'Tomato',
                                    '#f08080' => 'Light Coral',
                                    '#800080' => '#800080',
                                    '#00ff7f' => 'Spring Green',
                                    '#4682b4' => 'Steel Blue',
                                    '#e9967a' => 'Dark Salmon',
                                    '#8fbc8f' => 'Dark Sea Green',
                                    'brown' => 'Brown',
                                    'maroon' => 'Maroon',
                                    'red' => 'Red',
                                ] as $color => $name)
                                    <div onclick="chooseColor(event, '{{ $color }}')"
                                        class="d-inline-block rounded color-pallete"
                                        style="{{ 'background-color: ' . $color . ';' }}">
                                    </div>
                                @endforeach
                            </div>
                            <br>
                            <span class="my-2"> Background gradient</span>
                            <div class="mx-auto">

                                <div data-bs-auto-close="outside" class="rainbow-hue d-inline-block" type="button"
                                    id="dropdownGradientButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                </div>
                                <div class="dropdown-menu" aria-labelledby="dropdownGradientButton">
                                    <div id="div-gradient-picker"> </div>
                                </div>
                                @php
                                    $colors = [
                                        ['#000000', '#545454', '#737373'], // Black to Gray
                                        ['#800080', '#4682b4', '#add8e6'], // Purple to Light Blue
                                        ['#800080', '#ff00ff', '#dda0dd'], // Purple to Plum
                                        ['#4682b4', '#b0c4de', '#e0ffff'], // Steel Blue to Light Cyan
                                        ['#cd5c5c', '#e9967a', '#ffa07a'], // Indian Red to Light Salmon
                                        ['#ff4500', '#ff7f50', '#ffe4b5'], // Orange Red to Moccasin
                                        ['#ff6347', '#ffa07a', '#f08080'], // Tomato to Light Coral
                                        ['#ff4500', '#ff7f50', '#ffe4b5'], // Orange Red to Moccasin
                                        ['#ff00ff', '#dda0dd', '#e0ffff'], // Fuchsia to Light Cyan
                                        ['#dda0dd', '#f08080', '#f0e68c'], // Plum to Khaki
                                        ['#ff6347', '#ffa07a', '#ffe4b5'], // Tomato to Moccasin
                                        ['#8fbc8f', '#00ff7f', '#ffffe0'], // Dark Sea Green to Light Yellow
                                        ['#ff4500', '#ff7f50', '#ffe4b5'], // Orange Red to Moccasin
                                        ['#4682b4', '#b0c4de', '#e0ffff'], // Steel Blue to Light Cyan
                                        ['#f08080', '#ffa07a', '#ffe4b5'], // Light Coral to Moccasin
                                        ['#ff00ff', '#dda0dd', '#f08080'], // Fuchsia to Light Coral
                                        ['#e9967a', '#ffa07a', '#ffe4b5'], // Dark Salmon to Moccasin
                                        ['#4682b4', '#b0c4de', '#e0ffff'], // Steel Blue to Light Cyan
                                        ['#ff00ff', '#dda0dd', '#f0e68c'], // Fuchsia to Khaki
                                        ['#ff4500', '#ff7f50', '#ffe4b5'], // Orange Red to Moccasin
                                    ];
                                @endphp
                                @foreach ($colors as $colorGroup)
                                    @php
                                        $gradient = 'linear-gradient(' . implode(', ', $colorGroup) . ')';
                                    @endphp

                                    <div onclick="chooseGradient(event, '{{ $gradient }}')"
                                        class="d-inline-block rounded color-pallete"
                                        style="{{ 'background: ' . $gradient . '; width: 30px; height: 30px;' }}">
                                    </div>
                                @endforeach
                            </div>
                            <br>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-heading3">
                        <button class="accordion-button ps-1  collapsed ps-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapse3" aria-expanded="false" aria-controls="flush-collapse3">
                            Change your font and frame
                        </button>
                    </h2>
                    <div id="flush-collapse3" class="accordion-collapse collapse py-2 px-2"
                        aria-labelledby="flush-heading3" data-bs-parent="#accordionExample">
                        <span class="mt-3 mb-2"> Font color</span>
                        <div data-bs-auto-close="outside" class="rainbow-hue" type="button"
                            id="dropdownFontColorBgButton" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        </div>
                        <div class="dropdown-menu" aria-labelledby="dropdownFontColorBgButton">
                            <div id="div-font-color-picker-with-bg"> </div>
                        </div>
                         <p class="my-2 py-2 fs-4 cursive-font" style="{{ 
                            $backgroundStyles .
                            $fontStyles
                        }}"
                        >Write in cursive
                        </p>
                        <span class="my-2">Modify frame</span>
                        <div data-bs-auto-close="outside" class="rainbow-hue" type="button"
                            id="dropdownFrameColorBgButton" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        </div>
                        <div class="dropdown-menu" aria-labelledby="dropdownFrameColorBgButton">
                            <div id="div-font-color-picker-with-frame"> </div>
                        </div>
                        <div style="{{ 
                            $backgroundStyles.
                            $fontStyles
                        }}" class="upload-container cursive-font px-0 mx-0 mt-2 py-2">
                            <label class="upload-label">
                                <div class="circle-container">
                                    <div class="uploaded-image"
                                        style="background-image: url({{ '/storage' . '/' . $userProfile->userBanner }} ); background-size: cover; 
                                            background-repeat: no-repeat; background-position: center; {{ $frameStyles }}">
                                    </div>
                                </div>
                            </label>
                        </div>
                        <br> <br>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
<script>
    function uploadImageToBanner(event) {
        var file = event.target.files[0];
        if (file) {
            var cachedImage = URL.createObjectURL(file);
            backgroundBanner.style.backgroundImage = `url(${cachedImage})`;
        }
    }
</script>