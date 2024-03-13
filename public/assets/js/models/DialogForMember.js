class DialogForMember {
    constructor() {
        this.actionName = null;
        this.memberId = null;
    }

    getActionName() {
        return this.actionName;
    }

    getMemberId() {
        return this.memberId;
    }

    setActionName(value) {
        this.actionName = value;
        
    }

    setMemberId(value) {
        this.memberId = value;
    }

    reset() {
        this.actionName = '';
        this.memberId = '';
    }
}