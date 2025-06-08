<div id="step-1" class="d-none">
    <div class="welcome text-center" >
        @if (isset($error))
            <p style="color:#EF4444;">{{ $error }}</p>
        @endif
        <h3 class="mt-2">
            STEP 1: Choose your <span class="text-primary">event categories</span>
        </h3>
        <p >First, select an esport title</p>
        <div 
            @class(["mx-auto  custom-scrollbar2 w-100  py-0", 
                isset($eventCategory[3]) ? "grid-5 " : 'justify-content-center'
            ])
        
            style="overflow-x: auto;">
                @foreach ($eventCategory as $category)
                    @if ($category->gameIcon)
                        <div 
                            @class([
                                ' cursor-pointer game-events my-3',
                                'color-border-success mx-auto px-0 d-inline-block' =>
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
                                >
                            </a>
                        </div>
                    @endif
                @endforeach
            
                <div class=" scroll-images game-events" style=" pointer-events: none !important;">
                    <a href="javascript:void(0)"  > 
                        <img 
                            class="border border-dark selectable-image" 
                            src="{{ asset("/storage/images/event_details/more.png") }}" 
                            alt="More titles to come" 
                                                  >
                    </a>
                </div>

        </div>
        <div class=" d-flex justify-content-between box-width back-next--create">
            <button onclick="goToNextScreen('step-0', 'none')" type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-2', 'timeline-1')" type="button" class="oceans-gaming-default-button"> Next&nbsp;&nbsp;  &gt; </button>
        </div>
        <br>
    </div>
</div>

