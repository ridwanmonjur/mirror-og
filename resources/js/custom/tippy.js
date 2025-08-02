import tippy from 'tippy.js';
import { generateWarningHtml } from './brackets';


let specialTippy = [];
window.popoverIdToPopover = window.activePopovers || {};
window.ourIdToPopoverId = window.ourIdToPopoverId || {};

function getPopover(element) {
  let popverId = window.ourIdToPopoverId[element];
  if (popverId) {
    let popover = window.popoverIdToPopover[popverId];
    return popover;
  }

  return null;
}

window.hideAll = () => {
  for (let element of specialTippy) {
    let popover = getPopover(element);
    popover.hide();
  }
}

window.showAll = () => {
  for (let element of specialTippy) {
    let popover = getPopover(element);
    popover?.show();
  }
}


function createTippy(parent, html, trigger, options) {
    return tippy(parent, {
        content: html,
        allowHTML: true,
        placement: 'top',
        trigger,
        triggerTarget: parent,
        // hideOnClick: false,
        // trigger: 'click',
        interactive: true,
        hideOnClick: false,
        delay: [50, 0],
        theme: 'light',
        zIndex: 9999,
        appendTo: document.body,
        ...options,        
    });
}

window.addPopoverWithIdAndHtml = function (parent, html, trigger="click", options = {}, ourId = null) {
    if (!parent || !html) return null;

    if (ourId) {
        
        let popoverId = window.ourIdToPopoverId[ourId];
        if (popoverId) {
          if (popoverId in window.popoverIdToPopover) {
                window.popoverIdToPopover[popoverId].destroy();
                const { [popoverId]: removed, ...rest } = window.popoverIdToPopover;
                window.popoverIdToPopover = rest;
            }
        }
    }
    
    const tippyInstance = createTippy(
        parent, 
        html, 
        trigger, 
        { ...options }
    );

    window.ourIdToPopoverId[ourId] = tippyInstance.id;
    window.popoverIdToPopover[tippyInstance.id] = tippyInstance;
        
    return tippyInstance;
}

window.addPopoverWithIdAndChild = function (parent, child, trigger="click", options = {}, ourId) {
    if (!parent || !child || !child.innerHTML) return null;
    
    return window.addPopoverWithIdAndHtml(parent, child.innerHTML, trigger, options, ourId);
}

const parentElements = document.querySelectorAll(".first-item .popover-parent");
parentElements?.forEach(parent => {
  const contentElement = parent.querySelector(".popover-content");
  const parentElement = parent.querySelector(".popover-button");
  if (contentElement) {
    window.addPopover(parentElement, contentElement, 'mouseenter', {
      interactive: false
    });
  }
});


function addAllTippy() {
  const parentSecondElements = document.querySelectorAll(".middle-item");

  parentSecondElements?.forEach(parent => {
    let dataset = parent.dataset;
    let {stage_name: stageName, inner_stage_name: innerStageName} = dataset;
    const triggers = parent.querySelectorAll(".popover-button");
    triggers.forEach((trigger) => {
      let triggerPositionId = trigger.dataset.position;
      let triggerParentsPositionIds = previousValues[triggerPositionId];
      if (triggerParentsPositionIds && Array.isArray(triggerParentsPositionIds)) {
        let classNamesJoined = triggerParentsPositionIds.join(".");
        let triggerClassName = '.popover-middle-content.' + classNamesJoined;
        let contentElement = document.querySelector(triggerClassName);
        if (contentElement) {
       
          if (contentElement.classList.contains('warning') && !(
            stageName == 'L' && ['e1', 'e3', 'e5'].includes(innerStageName)
          )) {
            let {
              diffDate, position
            } = contentElement.dataset;

            if (triggerParentsPositionIds.includes(position)) {
              let tippyId = position + '+' + triggerPositionId;
              let popover = window.addPopoverWithIdAndHtml(trigger, generateWarningHtml(diffDate, triggerPositionId), 'manual', {
                  onShow(instance) {
                    const tippyBox = instance.popper;
                    tippyBox.addEventListener('click', () => {
                      instance.hide();
                  });
                  }
                }, tippyId);
              specialTippy = [...specialTippy, tippyId] 
            }
          }

          window.addPopoverWithIdAndChild(trigger, contentElement, 'mouseenter', {
            interactive: false
          }, classNamesJoined + '/' + triggerPositionId);
        }
      }
    })
  });
}


function addTippyToClass(classAndPositionList) {
  console.log({classAndPositionList});
  console.log({classAndPositionList});
  for (let classX of classAndPositionList) {
    let [triggerClass, prevClass] = classX;
    const triggers = document.querySelectorAll(`.popover-button.data-position-${prevClass}`);
    console.log({triggers, triggerClass, prevClass});
    triggers?.forEach((trigger) => {
      let triggerClassName = '.popover-middle-content.' + triggerClass;
      let contentElement = document.querySelector(triggerClassName);
      console.log({tippy: specialTippy});
      window.addPopoverWithIdAndHtml(trigger, contentElement, 'mouseenter', {
        interactive: false
      }, prevClass + '/' + triggerClassName);
    });
    console.log({tippy: specialTippy});
  }
}

window.addTippyToClass = addTippyToClass;

function addDotsToContainer(key, value) {
  console.log({key, value});
  console.log({key, value});
  console.log({key, value});
  let parent = document.querySelector(`.${key}.popover-middle-content`);
  let table = document.querySelector(`.${key}.tournament-bracket__table`);
  let dottedScoreContainer = parent?.querySelectorAll('.dotted-score-container');
  let dottedScoreBox = parent?.querySelectorAll('.dotted-score-box');
  let statusBox = parent?.querySelectorAll('.status-box');
  let dottedScoreTable = table?.querySelectorAll('.dotted-score-box');
  console.log({table, dottedScoreTable});

  dottedScoreContainer?.forEach((element, index) => {
    element.querySelectorAll('.dotted-score')?.forEach((dottedElement, dottedElementIndex) => {
      if (value.realWinners[dottedElementIndex]) {
        if (value.realWinners[dottedElementIndex] == index) {
          dottedElement.classList.remove('bg-secondary', 'bg-red', 'd-none');
          dottedElement.classList.add("bg-success");
        } else {
          dottedElement.classList.remove('bg-secondary', 'bg-success', 'd-none');
          dottedElement.classList.add("bg-red");
        }
      } else {
        dottedElement.classList.remove('bg-success', 'bg-red', 'd-none');
        dottedElement.classList.add('bg-secondary');
      }
    })
  });

  dottedScoreBox?.forEach((element, index) => {
    element.innerHTML = value['score'][index];
  });

  dottedScoreTable?.forEach((element, index) => {
    console.log({element});
    element.innerHTML = value['score'][index];
  });

  statusBox?.forEach((element, index) => {
    if ('completeMatchStatus' in value) {
      element.innerHTML = value['completeMatchStatus'];
    }
  });

}




export {
  addAllTippy,
  addDotsToContainer,
  addTippyToClass
}