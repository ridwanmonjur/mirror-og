class DialogForMember {
    constructor() {
        this.actionName = null;
        this.memberId = null;
        this.teamId = null
    }

    getActionName() {
        return this.actionName;
    }

    getMemberId() {
        return this.memberId;
    }

    getTeamId() {
        return this.teamId;
    }

    setActionName(value) {
        this.actionName = value;
    }

    setMemberId(value) {
        this.memberId = value;
    }

    setTeamId(value) {
        this.teamId = value;
    }

    reset() {
        this.actionName = null;
        this.memberId = null;
        this.teamId = null
    }
}