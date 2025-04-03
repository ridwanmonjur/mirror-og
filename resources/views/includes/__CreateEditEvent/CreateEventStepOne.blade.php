<div id="step-1" class="d-none">
    <div class="welcome text-center" >
        @if (isset($error))
            <p style="color:#EF4444;">{{ $error }}</p>
        @endif
        <h3 class="mt-2">
            STEP 1: Choose your <span class="text-primary">event categories</span>
        </h3>
        <p >First, select an esport title</p>
 
        <div class="image-scroll-container d-flex justify-content-center flex-wrap">
            @foreach ($eventCategory as $category)
                @if ($category->gameIcon)
                    <div 
                        @class([
                            'scroll-images game-events',
                            'color-border-success' =>
                                $event && $category->id == $event->event_category_id,
                        ])
                        data-category-id="{{ $category->id }}"
                        data-game-title="{{ $category->gameTitle }}"
                        data-game-icon="{{ asset("storage/$category->gameIcon") }}"
                    >
                        <a href="javascript:void(0)">
                            <img 
                                class="border border-dark selectable-image" 
                                src="{{ asset("storage/$category->gameIcon") }}" 
                                alt="{{ $category->gameTitle }}" 
                                style="object-fit: cover; border-radius: 20px; height: 325px; width: 220px;"
                            >
                        </a>
                        <h5 style="padding-top: 10px;">{{ $category->gameTitle }}</h5>
                    </div>
                @endif
            @endforeach
            <div class='scroll-images '>
                <a href="javascript:void(0)" style="pointer-events: none;cursor: not-allowed;">
                    <img 
                        class="border border-dark selectable-image" 
                        src="{{ asset("/storage/images/event_details/valorant.png") }}" 
                        alt="Valorant" 
                        style="object-fit: cover; border-radius: 20px; height: 325px; width: 220px; cursor: not-allowed;"
                    >
                </a>
                <h5 style="padding-top: 10px;">Valorant</h5>
            </div>
            <div class=" scroll-images ">
                <a href="javascript:void(0)"  style="pointer-events: none;cursor: not-allowed;"> 
                    <img 
                        class="border border-dark selectable-image" 
                        src="{{ asset("/storage/images/event_details/more.png") }}" 
                        alt="More titles to come" 
                        style="object-fit: cover; border-radius: 20px; height: 325px; width: 220px;  cursor: not-allowed;"
                    >
                </a>
                <h5 style="padding-top: 10px;">and more...</h5>
            </div>
        </div>
        <div class=" d-flex justify-content-between box-width back-next">
            <button onclick="goToNextScreen('step-0', 'none')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-2', 'timeline-1')" type="button" class="oceans-gaming-default-button"> Next&nbsp;&nbsp;  &gt; </button>
        </div>
        <br>
    </div>
</div>

