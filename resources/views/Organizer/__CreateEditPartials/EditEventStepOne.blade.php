<div id="step-1" class="d-none">
    <div id="invisible-until-loaded">
        @if (isset($error))
            <p style="color:#EF4444;">{{ $error }}</p>
        @endif
        <h3>
            STEP 1: Choose your <span class="text-primary">event categories</span>
        </h3>
        <p class="mb-0">First, select an esport title</p>
        <div class="image-scroll-container box-width py-2">
            @foreach ($eventCategory as $category)
                @if ($category->gameIcon)
                    <div @class([
                        'scroll-images',
                        'color-border-success' =>
                            $event && $category->id == $event->event_category_id,
                    ])
                        onclick="
                        document.querySelectorAll('.scroll-images').forEach(element => {
                        element.classList.remove('color-border-success');
                        });
                        this.classList.add('color-border-success');
                        let categoryId =  {{ Js::from($category->id) }};
                        setFormValues( {'gameTitle': {{ Js::from($category->gameTitle) }} } ); 
                        goToNextScreen('step-2', 'timeline-1');
                        let gameTitleImg = this.children[0].children[0].src;
                        localStorage.setItem('gameTitleImg', gameTitleImg);
                        console.log({categoryId})
                        setFormValues( {'gameTitleId': categoryId } );
                        ">
                        <a href="#">
                            <img class="selectable-image " src="<?php echo asset("storage/$category->gameIcon"); ?>" alt=""
                                style="object-fit: cover; border-radius: 20px; height: 325px; width: 220px;"></a>
                        <h5 style="padding-top: 10px;">
                            {{ $category->gameTitle }}
                        </h5>
                    </div>
                @endif
            @endforeach
        </div>
        <div class=" d-flex justify-content-between box-width back-next">
            <div></div>
            <button onclick="goToNextScreen('step-2', 'timeline-1')" type="button"
                class="oceans-gaming-default-button"> Next&nbsp;&nbsp;  &gt; </button>
        </div>
        <br>
    </div>
</div>
