<div id="step-1" class="">
    <div class="welcome text-center" style="margin-bottom: -25px !important;">
        @if (isset($error))
        <p style="color:red;">{{ $error }}</p>
        @endif
        <u>
            <h3>
                STEP 1: Choose your Event Categories
            </h3>
        </u>
        <p>First, select an esport title</p>
        <div class="image-scroll-container box-width">


            @foreach ($eventCategory as $category)
            @if ($category->gameIcon)
            <div class="scroll-images" onclick="
                                    setFormValues( {'gameTitle': {{Js::from($category->gameTitle)}} } ); 
                                    goToNextScreen('step-2', 'timeline-1');
                                    let gameTitleImg = this.children[0].children[0].src;
                                    localStorage.setItem('gameTitleImg', gameTitleImg);
                                    ">
                <a href="#">
                    <img class="selectable-image focused" src="<?php echo asset("storage/$category->gameIcon"); ?>" alt="" style="object-fit: cover; border-radius: 20px; height: 325px; width: 220px;"></a>
                <h5 style="padding-top: 10px;">{{ $category->gameTitle}}</h5>
            </div>
            @endif
            @endforeach
            <!-- Add more images and titles here -->
        </div>
        <div class="flexbox box-width">
            <div></div>
            <button onclick="goToNextScreen('step-2', 'timeline-1')" type="button" class="oceans-gaming-default-button"> Next > </button>
        </div>
    </div>
</div>