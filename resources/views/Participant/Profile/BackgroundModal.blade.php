<div class="offcanvas offcanvas-start fade" id="profileDrawer" tabindex="2" aria-labelledby="#profileDrawer"
    aria-hidden="true">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Change your profile's look</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        
        <form id="updateBgColorRequest" class="d-inline" method="POST" enctype="multipart/form-data"
            action="{{ route('user.userBackground.action', ['id' => $userProfile->id]) }}">
            @csrf
            <div class="d-flex justify-content-between pb-2">
                <span>Save these changes?</span>
                <button type="submit" class="btn btn-primary text-light rounded-pill">Save</button>
            </div>
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
                        <p class="my-2"> Upload a background photo</p>
                        <div class="input-group mb-4">
                            <input type="file" class="form-control" name="backgroundBanner" 
                                aria-describedby="inputGroupFileAddon03" aria-label="Upload"
                                onchange="uploadImageToBanner(event)"
                            >
                        </div>
                        <div class="mx-auto">
                            <p class="my-2">Or, choose a solid color</p>
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
        'orange' => 'Orange',
    ] as $color => $name)
                                    <div onclick="chooseColor(event, '{{ $color }}')"
                                        class="d-inline-block rounded color-pallete"
                                        style="{{ 'background-color: ' . $color . ';' }}">
                                    </div>
                                @endforeach
                            </div>

                            <p class="my-2"> Or, choose a gradient for background</p>
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
                        <p class="my-2"> Change your font color</p>
                        <div data-bs-auto-close="outside" class="rainbow-hue" type="button"
                            id="dropdownFontColorBgButton" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        </div>
                        <div class="dropdown-menu" aria-labelledby="dropdownFontColorBgButton">
                            <div id="div-font-color-picker-with-bg"> </div>
                        </div>
                         <p class="my-2 fs-4 cursive-font" style="{{ 
                            'color:' . $userProfile->profile->fontColor . ';' }}">Write in cursive
                        </p>
                        <p class="my-2">Modify frame</p>
                        <div data-bs-auto-close="outside" class="rainbow-hue" type="button"
                            id="dropdownFrameColorBgButton" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        </div>
                        <div class="dropdown-menu" aria-labelledby="dropdownFrameColorBgButton">
                            <div id="div-font-color-picker-with-frame"> </div>
                        </div>
                        <div class="upload-container">
                            <label for="image-upload" class="upload-label">
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