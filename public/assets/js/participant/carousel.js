var currentIndex = 0;
function carouselWork(increment = 0) {
    const eventBoxes = document.querySelectorAll('.event-carousel-works > div');
    let boxLength = eventBoxes.length;
    let numberOfBoxes = 1;
    if (window.matchMedia("(min-width: 1100px)").matches) {
        numberOfBoxes = 2;
    }

    let incrementSignMultipier = increment >= 0 ? 1 : -1;
    if (increment !== 0) increment = numberOfBoxes * incrementSignMultipier;

    let newSum = currentIndex + increment;

    if (newSum >= boxLength || newSum < 0) {
        return;
    } else {
        currentIndex = newSum;
    }

    console.log({ currentIndex, boxLength, numberOfBoxes })


    // carousel top button working
    const button1 = document.querySelector('.carousel-button:nth-child(1)');
    const button2 = document.querySelector('.carousel-button:nth-child(2)');
    button1.classList.remove('carousel-button-disabled');
    button2.classList.remove('carousel-button-disabled');

    if (currentIndex <= 0) {
        button1.classList.add('carousel-button-disabled');
    }

    if (currentIndex + numberOfBoxes > boxLength - 1) {
        button2.classList.add('carousel-button-disabled');
    }

    // carousel swing
    for (let i = 0; i < currentIndex; i++) {
        eventBoxes[i]?.classList.add('d-none');
    }

    for (let i = currentIndex; i < currentIndex + numberOfBoxes; i++) {
        eventBoxes[i]?.classList.remove('d-none');
    }

    for (let i = currentIndex + numberOfBoxes; i < boxLength; i++) {
        eventBoxes[i]?.classList.add('d-none');
    }
    console.log("ended")
}