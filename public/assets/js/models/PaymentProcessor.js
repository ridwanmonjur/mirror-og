class PaymentProcessor {
    constructor() {
        this.isPaymentSelected = false;
        this.paymentType = null;
        this.paymentElement = null;
        this.nextStepId = null;
    }

    getIsPaymentSelected() {
        // use this to enable "Confirm and Pay"
        return this.isPaymentSelected;
    }

    getPaymentElement() {
        return this.paymentElement;
    }

    getPaymentType() {
        return this.paymentType;
    }

    getNextStepId() {
        // use this to toggle the correct payment accordion view 
        return this.nextStepId;
    }

    setIsPaymentSelected(value) {
        if (typeof value === 'boolean') {
            this.isPaymentSelected = value;
        } else {
            throw new Error('Invalid value for isPaymentSelected. Expected a boolean.');
        }
    }

    setPaymentElement(value) {
        if (typeof value === 'string') {
            this.paymentElement = value;
        } else {
            throw new Error('Invalid value for paymentType. Expected a string.');
        }
    }

    setPaymentType(value) {
        if (typeof value === 'string') {
            this.paymentType = value;
            this.setNextStepId();
        } else {
            throw new Error('Invalid value for paymentType. Expected a string.');
        }
    }

    setNextStepId() {
        const stepList = {
            'bank': 'bankLogoId',
            'eWallet': 'eWalletLogoId',
            'otherEWallet': 'otherEWalletLogoId',
            'card': 'cardLogoId',
        };

        this.nextStepId = stepList[this.paymentType];
    }

    reset() {
        this.isPaymentSelected = false;
        this.paymentType = null;
        this.paymentElement = null;
        this.nextStepId = null;
    }
}