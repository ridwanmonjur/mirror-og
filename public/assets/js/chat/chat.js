window.onload = () => {
    window.loadMessage();
    const event = new CustomEvent("fetchstart"); 
    window.dispatchEvent(event);
    // document.querySelector('.add-chat')?.click();
};

function formatDateDifference(startDate) {
    if (startDate) {
        startDate = new Date(startDate);
        const endDate = new Date();

        const msInDay = 24 * 60 * 60 * 1000;
        const msInWeek = msInDay * 7;
        const msInMonth = msInDay * 30.44; // Average days in a month
        const msInYear = msInDay * 365.25; // Average days in a year

        const diffInMs = endDate - startDate;

        if (diffInMs < msInDay) {
            return 'Active today';
        } else if (diffInMs >= msInYear) {
            const years = Math.floor(diffInMs / msInYear);
            return `${years} year${years > 1 ? 's' : ''} ago`;
        } else if (diffInMs >= msInMonth) {
            const months = Math.floor(diffInMs / msInMonth);
            return `${months} month${months > 1 ? 's' : ''} ago`;
        } else if (diffInMs >= msInWeek) {
            const weeks = Math.floor(diffInMs / msInWeek);
            return `${weeks} week${weeks > 1 ? 's' : ''} ago`;
        } else {
            const days = Math.floor(diffInMs / msInDay);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        } 
    } else {
        return 'New user...';
    }
}
