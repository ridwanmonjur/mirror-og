function submitConfirmCancelForm(event, text, id) {
    let form = event.target.dataset.form;
    window.dialogOpen(text, ()=> {
        document.querySelector(`#${id}.${form}`).submit();
    }, null)
}

let registrationPaymentModalMap = {}; 

function updateInput(input) {
    let ogTotal = Number(input.dataset.totalAmount);
    let total = ogTotal;
    let pending = Number(input.dataset.pendingAmount);
    let modalId = input.dataset.modalId;

    if (!(registrationPaymentModalMap.hasOwnProperty(modalId))) {
        registrationPaymentModalMap[modalId] = 0;
    } 

    let index = registrationPaymentModalMap[modalId];
    let totalLetters = 4;
    let newValue = input.value.replace(/[^\d]/g, '');
    let lettersToTake = index - totalLetters;
    let isMoreThanTotalLetters = lettersToTake >= 0;
    if (isMoreThanTotalLetters) {
        let length = newValue.length;
        newValue = newValue.substr(0, lettersToTake + 3) + '.' + newValue.substr(lettersToTake + 3, 2);
    } else { 
        newValue = newValue.substr(1, 2) + '.' + newValue.substr(3, 2);
    }
   

    if (+newValue >= +pending) {
        newValue = pending.toFixed(2);
    }

    registrationPaymentModalMap[modalId] ++;
    
    input.value = newValue;
    putAmount(input.dataset.modalId, newValue, ogTotal, pending, Number(input.dataset.existingAmount));
}

function keydown(input) {
    if (event.key === "Backspace" || event.key === "Delete") { 
        event.preventDefault();
        document.getElementById('currencyResetInput').click();
    }

    if (event.key.length === 1 && !/\d/.test(event.key)) {
        event.preventDefault();
    }
}

function moveCursorToEnd(input) {
    input.focus(); 
    input.setSelectionRange(input.value.length, input.value.length);
}

function putAmount(modalId, inputValue, total, pending, existing) {
    let putAmountTextSpan = document.querySelector('#payModal' + modalId + ' .putAmountClass');
    let pieChart = document.querySelector('#payModal' + modalId + ' .pie');
    inputValue = Number(inputValue);
    let percent = ((existing + inputValue) * 100) / total; 
    let color = 'red';
    if (percent === 0) {
        color = 'gray';  
    } else if (percent > 50 && percent < 100) {
        color = 'orange';
    } else if (percent >= 100) {
        color = 'green';
    }

    pieChart.style.setProperty('--c', color);
    pieChart.style.setProperty('--p', percent ? percent : 0);
    pieChart.innerText = percent.toFixed(0) + "%" ;
    putAmountTextSpan.innerText = inputValue.toFixed(2);
}

function resetInput(button) {
    let input = document.querySelector('#payModal' + button.dataset.modalId + " input[name='amount']");
    registrationPaymentModalMap[button.dataset.modalId] = 0;
    input.value = input.defaultValue;
    putAmount(button.dataset.modalId, 0.00, Number(button.dataset.totalAmount), Number(button.dataset.pendingAmount), Number(button.dataset.existingAmount));
}

function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}


document.addEventListener("DOMContentLoaded", function() {
    const searchInputs = document.querySelectorAll('.search_box input');
    const memberTables = document.querySelectorAll('.member-table');

    searchInputs.forEach((searchInput, index) => {
        searchInput.addEventListener("input", function() {
            const searchTerm = searchInput.value.toLowerCase();
            const memberRows = memberTables[index].querySelectorAll('tbody tr');

            memberRows.forEach(row => {
                const playerName = row.querySelector('.player-info span')
                    .textContent.toLowerCase();

                if (playerName.includes(searchTerm)) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

 

});

addOnLoad(()=> {
    const parents = document.querySelectorAll('.popover-parent');
    parents.forEach((parent) => {
        const contentElement = parent.querySelector(".popover-content");
        const parentElement = parent.querySelector(".popover-button");
        if (contentElement) {
            window.addPopover(parentElement, contentElement, 'mouseenter');
        }
    });
});
