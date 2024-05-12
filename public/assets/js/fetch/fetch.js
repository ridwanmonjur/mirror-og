async function fetchData(url, callback, errorCallback, options = {}) {
    const defaultOptions = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            
        },
    };
    
    const mergedOptions = { ...defaultOptions, ...options };

    try {
        const response = await fetch(url, mergedOptions);
        const responseData = await response.json();
        callback(responseData);
    } catch (error) {
        if (errorCallback) {
            errorCallback(error);
        } else {
            console.error('Error fetching data:', error);
        }
    }
}