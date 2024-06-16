<div class="modal fade" id="profileModal" tabindex="2" aria-labelledby="#profileModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" style="margin: auto;">
                <div class="tabs mx-0 d-flex flex-row justify-content-center px-0" style="width: 100% !important;">
                    <button class="tab-button  ms-0 px-0 modal-tab tab-button-override tab-button-active"
                        onclick="showTab(event, 'BackgroundPhoto', 'modal-tab')">Background Photo</button>
                    <button class="tab-button  ms-0 px-0 modal-tab tab-button-override"
                        onclick="showTab(event, 'BackgroundColor', 'modal-tab')">Background
                        Color</button>
                    <button class="tab-button ms-0 px-0 modal-tab tab-button-override "
                        onclick="showTab(event, 'ForeColor', 'modal-tab')">Foreground
                        Color</button>
                </div>
                <div class="tab-content pb-4 modal-tab" id="BackgroundPhoto">
                    <form id="updateBgColorRequest" class="d-inline" method="POST" action="{{ route('user.userBackground.action', ['id' => $userProfile->id] ) }}"> 
                        @csrf
                        <input type="hidden" name="backgroundColor" value="{{ $userProfile->profile->backgroundColor }}">
                        <input type="hidden" name="backgroundGradient" value="{{ $userProfile->profile->backgroundGradient }}">
                    </form>
                    <div class="mx-auto"  style="max-width: max(400px, 75%);">
                        <br>
                        <h5> Choose a background banner</h5>
                        <div class="custom-file-container" data-upload-id="file-upload-preview-1"></div>
                        <br>

                        <div class="d-flex justify-content-center" >
                            <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                                data-bs-dismiss="modal">Close
                            </button>
                        </div>
                    </div>
                </div>
                <div class="tab-content pb-4 modal-tab d-none" id="BackgroundColor">
                    <div class="mx-auto"  style="max-width: max(400px, 75%);">
                        <br>
                        <h5> Choose a solid color</h5>
                        <div class="mx-auto">
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
        '#ff4500' => 'Orange Red',
        'orange' => 'Orange',
        'yellow' => 'Yellow',
        'green' => 'Green',
        'lime' => 'Lime',
        'cyan' => 'Cyan',
        'blue' => 'Blue',
        'navy' => 'Navy',
        'purple' => 'Purple',
        'magenta' => 'Magenta',
        'pink' => 'Pink',
        '#ff00ff' => 'Fuchsia',
        'lightgray' => 'Light gray',
    ] as $color => $name)
                                <div onclick="chooseColor(event, '{{ $color }}')"
                                    class="d-inline-block rounded color-pallete"
                                    style="{{ 'background-color: ' . $color . ';' . 'width: 30px; height: 30px;' }}">
                                </div>
                            @endforeach
                        </div>
                        <br>
                        <p class="my-0"> Choose a custom color </p>
                        <button data-bs-auto-close="outside"
                            style="{{ 'background-color: #f6b73c;' . 'width: 60px; height: 30px;' }}"
                            class="btn btn-link color-pallete" type="button" id="dropdownColorButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownColorButton">
                            <div id="div-color-picker"> </div>
                        </div>
                        <br><br>
                        <h5> Choose a gradient color for background</h5>
                        <div class="mx-auto" >
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
                                    style="{{ 'background: ' . $gradient . '; width: 30px; height: 30px;' }}"></div>
                            @endforeach
                        </div>
                        <br>
                        <p class="my-0"> Choose a custom gradient </p>
                        <button data-bs-auto-close="outside"
                            style="{{ 'background: linear-gradient(#4682b4, #b0c4de, #e0ffff);' . 'width: 60px; height: 30px;' }}"
                            class="btn btn-link color-pallete" type="button" id="dropdownGradientButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownGradientButton">
                            <div id="div-gradient-picker"> </div>
                        </div>
                        <br>
                        <br>
                        <br>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button onclick="formRequestSubmitById(null, 'updateBgColorRequest')" type="button" class="oceans-gaming-default-button me-3">Save
                        </button>
                        <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                            data-bs-dismiss="modal">Close
                        </button>
                    </div>
                </div>
                <div class="tab-content pb-4 modal-tab d-none" id="ForeColor">
                    <div class="mx-auto"  style="max-width: max(400px, 75%);">
                        <br>
                        <h5> Choose a solid font color</h5>                    
                        <form id="updateForegroundColorRequest" class="d-inline" method="POST" action="{{ route('user.userBackground.action', ['id' => $userProfile->id] ) }}"> 
                            @csrf
                            <input type="hidden" name="frameColor" value="{{ $userProfile->profile->frameColor }}">
                            <input type="hidden" name="fontColor" value="{{ $userProfile->profile->fontColor }}">
                        </form>
                        <button data-bs-auto-close="outside"
                            style="{{ 'background-color: gray;' . 'width: 60px; height: 30px;' }}"
                            class="btn btn-link color-pallete" type="button" id="dropdownFontColorBgButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownFontColorBgButton">
                            <div id="div-font-color-picker-with-bg"> </div>
                        </div>
                        <br> <br>
                        <h5> Choose a solid color for your profile frame</h5>
                        <button data-bs-auto-close="outside"
                            style="{{ 'background-color: green;' . 'width: 60px; height: 30px;' }}"
                            class="btn btn-link color-pallete" type="button" id="dropdownFrameColorBgButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownFrameColorBgButton">
                            <div id="div-font-color-picker-with-frame"> </div>
                        </div>
                        <div class="upload-container">
                            <label for="image-upload" class="upload-label">
                                <div class="circle-container">
                                    <div class="uploaded-image"
                                         style="background-image: url({{ '/storage' . '/'. $userProfile->userBanner }} ); background-size: cover; 
                                            background-repeat: no-repeat; background-position: center; {{$frameStyles}}"
                                    >
                                    </div>
                                </div>
                            </label>
                        </div>
                        <br> <br>
                        <div class="d-flex justify-content-center">
                            <button onclick="formRequestSubmitById(null, 'updateForegroundColorRequest')" class="oceans-gaming-default-button me-3">Save</button>
                             <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                                data-bs-dismiss="modal">Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
