function toggleNavbar() {
    const x = document.querySelector("nav.mobile-navbar");
    console.log({ x })
    x.classList.toggle("d-none");
}

function goToNextScreen(nextId, nextTimeline) {
    const allIDs = [
        'step-0',
        'step-1', 'step-2', 'step-3', 'step-4', 'step-5', 'step-6', 'step-7', 'step-8', 'step-9', 'step-10', 'step-11', 'step-12'
    ];
    const allTimelines = [
        'timeline-1', 'timeline-2', 'timeline-3', 'timeline-4'
    ];
    allIDs.forEach(id => {
        const element = document.querySelector(`#${id}`);
        console.log({ id, element })
        if (id === nextId) element.classList.remove("d-none");
        else if (!element.classList.contains("d-none")) {
            element.classList.add("d-none");
        }
    })
    allTimelines.forEach((timeline, index) => {

        const paragraph = document.querySelector(`#${timeline} .timestamp span`);
        const cicle = document.querySelector(`#${timeline} small`);
        // const border = document.querySelector(`#${timeline} div:nth-child(1)`);
        if (timeline === nextTimeline) {
            if (!paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.add("font-color-active-timeline");
            if (!cicle.classList.contains("background-active-timeline")) cicle.classList.add("background-active-timeline");
            if (index === 0) {
                border.style.borderTop = "2px solid red";
            }
        } else {
            if (paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.remove("font-color-active-timeline");
            if (cicle.classList.contains("background-active-timeline")) cicle.classList.remove("background-active-timeline");
        }

    })


}
