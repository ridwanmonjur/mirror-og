<br>
<div class="time-line-box" id="timeline-box">
    <div class="swiper-container text-center">
        <div class="swiper-wrapper">
            <div class="swiper-slide swiper-slide__left" id="timeline-1">
                <div class="timestamp" onclick="goToNextScreen('step-1', 'timeline-1')"><span
                        class="cat">Categories</span></div>
                <div class="status__left" onclick="goToNextScreen('step-1', 'timeline-1')"><span><small></small></span></div>
            </div>
            <div class="swiper-slide" id="timeline-2">
                <div class="timestamp" onclick="goToNextScreen('step-5', 'timeline-2')"><span>Details</span></div>
                <div class="status" onclick="goToNextScreen('step-5', 'timeline-2')"><span><small></small></span></div>
            </div>
            <div class="swiper-slide" id="timeline-3">
                <div class="timestamp" onclick="goToNextScreen('step-10', 'timeline-3'); fillStepPaymentValues();"><span>Payment</span></div>
                <div class="status" onclick="goToNextScreen('step-10', 'timeline-3'); fillStepPaymentValues();"><span><small></small></span></div>
            </div>
            <div class="swiper-slide swiper-slide__right" id="timeline-4">
                <div class="timestamp" onclick="goToNextScreen('step-11', 'timeline-4')"><span
                        class="date">Launch</span></div>
                <div class="status__right" onclick="goToNextScreen('step-11', 'timeline-4')"><span><small></small></span></div>
            </div>
        </div>
    </div>
</div>
<div class="breadcrumb-top">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a onclick="goToNextScreen('step-1', 'timeline-1')">Categories</a></li>
            <li class="breadcrumb-item"><a onclick="goToNextScreen('step-5', 'timeline-2')">Details</a></li>
            <li class="breadcrumb-item"><a onclick="goToNextScreen('step-10', 'timeline-3'); fillStepPaymentValues();">Payment</a></li>
            <li class="breadcrumb-item"><a onclick="goToNextScreen('step-11', 'timeline-4')">Launch</a></li>
        </ol>
    </nav>
</div>
