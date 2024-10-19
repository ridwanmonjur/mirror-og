function validateAmount(event) {
    event.preventDefault();
    let minimumAmount = 5;
    const form = event.target;
    
    const amountInput = form.querySelector('input[name="amount"]');
    const pendingAmount = parseFloat(amountInput.dataset.pendingAmount);
    const totalAmount = parseFloat(amountInput.dataset.totalAmount);
    const existingAmount = parseFloat(amountInput.dataset.existingAmount);
    let inputAmount = parseFloat(amountInput.value);
    if (inputAmount < minimumAmount) {
        toastError("Minmum amount must be greater than 5 RM.");
        return;
    }

    inputAmount = Math.max(inputAmount, 4);

    const newPendingAmount = pendingAmount - (inputAmount - existingAmount);
     if (amountInput > totalAmount) {
        toastError("This fee is too much.");
        return;
    }
    if (newPendingAmount < minimumAmount) {
        inputAmount = existingAmount + (pendingAmount - 4);
    }

    if (newPendingAmount <= minimumAmount && parseFloat(newPendingAmount) !== 0.0) {
        toastError("You need to pay either the complete remaining fee, or 5RM less than the remaining fee.");
        return;
    }

    form.submit();
}


function approveTeam(memberId) {
    window.dialogOpen('Continue with approval?', ()=>  {

    }, null)
}