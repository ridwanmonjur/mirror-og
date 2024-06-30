var currentIndex = 0;
function carouselWork(increment = 0) {
    const eventBoxes = document.querySelectorAll('.event-carousel-works > div');
    let boxLength = eventBoxes.length;
    console.log({currentIndex, boxLength})

    let newSum = currentIndex + increment;
    console.log({currentIndex, boxLength})

    if (newSum >= boxLength || newSum < 0) {
        return;
    } else {
        currentIndex = newSum;
    }

    console.log({currentIndex, boxLength})


    // carousel top button working
    const button1 = document.querySelector('.carousel-button:nth-child(1)');
    const button2 = document.querySelector('.carousel-button:nth-child(2)');
    button1.classList.remove('carousel-button-disabled');
    button2.classList.remove('carousel-button-disabled');

    if (currentIndex <= 0) {
        button1.classList.add('carousel-button-disabled');
    }

    if (currentIndex+2 > boxLength-1) {
        button2.classList.add('carousel-button-disabled');
    }

    // carousel swing
    for (let i = 0; i < currentIndex; i++) {
        eventBoxes[i]?.classList.add('d-none');
    }

    for (let i = currentIndex; i < currentIndex + 2; i++) {
        eventBoxes[i]?.classList.remove('d-none');
    }

    for (let i = currentIndex + 2; i < boxLength; i++) {
        eventBoxes[i]?.classList.add('d-none');
    }
    console.log("ended")
}