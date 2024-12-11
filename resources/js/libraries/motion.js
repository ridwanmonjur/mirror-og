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
    const children = Array.from(parent.children);
    
    inView(parent, () => {
        animate(children, {
            opacity: [0, 1],
            x: [-20, 0]
        }, {
            delay: stagger(staggerTime),
            duration: 0.4,
            easing: spring()
        });
        
        return false;
    });
};

function animateCard(cardClassName, cardComponentsClassNameList) {
    const cards = document.querySelectorAll(`.${cardClassName}:not([data-animated])`);
    const cardArray = Array.from(cards);
    
    cardArray.forEach((card, index) => {
        card.dataset.animated = 'true';
        animate(card, 
            { 
                opacity: [0, 1],
                y: [100, 0]
            }, 
            { 
                delay: index * 0.15,
                duration: 0.5,
                easing: spring({ damping: 15 }),
                
            }
        );

        const innerElements = 
            cardComponentsClassNameList.map(className => {
                return card.querySelector('.' + className);
            }).filter(Boolean);

        animate(innerElements, 
            { 
                opacity: [0, 1],
                y: [20, 0]
            }, 
            {
                delay: stagger(0.2, { start: index * 0.15 }),
                duration: 0.3,
                easing: spring({ damping: 20 })
            }
        );
    });
}

function animateGlow(glowEffectElement) {
    animate(glowEffectElement, {
        boxShadow: [
            '0 0 0px rgba(0, 123, 255, 0)',
            '0 0 20px rgba(0, 123, 255, 0.8)',
            '0 0 0px rgba(0, 123, 255, 0)'
        ]
    }, {
        duration: 3,
        repeat: 4,
        easing: 'ease-in-out'
    });
}

function slideInLeftRight() {
    const createSlideAnimation = (direction) => ({
        keyframes: {
            opacity: [0, 1],
            transform: [`translateX(${direction === 'left' ? '-50px' : '50px'})`, 'translateX(0)']
        },
        options: {
            duration: 0.8,
            easing: 'ease-out',
            delay: 0.2
        }
    });

    const slideInLeftElements = document.querySelectorAll('.slideInLeft') ;
    if (Array.isArray(slideInLeftElements) && slideInLeftElements[0]) {
        inView(slideInLeftElements, ({ target }) => {
            try {
                animate(
                    target,
                    createSlideAnimation('left').keyframes,
                    createSlideAnimation('left').options
                );
            } catch (error) {
                console.error('Error animating slideInLeft:', error);
            }
            return false;
        }, {
            margin: "0px 0px -100px 0px",
            amount: 0.2
        });
    }

    const slideInRightElements = document.querySelectorAll('.slideInRight') ;

    if (Array.isArray(slideInRightElements) && slideInRightElements[0]) {
        inView(slideInRightElements, ({ target }) => {
            try {
                animate(
                    target,
                    createSlideAnimation('right').keyframes,
                    createSlideAnimation('right').options
                );
            } catch (error) {
                console.error('Error animating slideInRight:', error);
            }
            return false;
        }, {
            margin: "0px 0px -100px 0px",
            amount: 0.2
        });
    }

}




window.motion = {
    createFadeIn,
    createStaggerChildren,
    animateCard,
    animateGlow,
    slideInLeftRight,

}