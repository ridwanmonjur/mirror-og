import { animate, stagger, inView,  spring } from "@motionone/dom";

const createFadeIn = (element, delay = 0) => {
    animate(element, {
        opacity: [0, 1],
        y: [2000, 0]
    }, {
        delay,
        duration: 600,
        easing: spring()
    });
};

const createStaggerChildren = (parent, staggerTime = 0.1) => {
    const children = parent.children;
    animate(children, {
        opacity: [0, 1],
        x: [-20, 0]
    }, {
        delay: stagger(staggerTime),
        duration: 0.4,
        easing: spring()
    });
};

function animateCard() {
    const cards = document.querySelectorAll('.event:not([data-animated]');
    const cardArray = Array.from(cards);
    
    const groups = [];
    for (let i = 0; i < cardArray.length; i += 6) {
        groups.push(cardArray.slice(i, i + 6));
    }
    
    groups.forEach((group, groupIndex) => {
        const groupDelay = groupIndex * 0.3; 
        
        animate(group, 
            { 
                opacity: [0, 1],
                y: [100, 0]
            }, 
            { 
                delay: stagger(0.15, { start: groupDelay }),
                duration: 0.5,
                easing: spring({ damping: 15 })
            }
        );
        
        group.forEach((card, cardIndex) => {
            const innerElements = [
                card.querySelector('.cover'),
                card.querySelector('.frame1'),
                card.querySelector('.league_name'),
                card.querySelector('.fs-7')
            ].filter(Boolean);
            
            const innerDelay = groupDelay + (cardIndex * 0.15); 
            
            animate(innerElements, 
                { 
                    opacity: [0, 1],
                    y: [20, 0]
                }, 
                {
                    delay: stagger(0.2, { start: innerDelay }),
                    duration: 0.3,
                    easing: spring({ damping: 20 }),
                    onComplete: () => {
                        card.dataset.animated = 'complete';
                    }
                }
            );
        });
    });
}



window.motion = {
    createFadeIn,
    createStaggerChildren,
    animateCard
}