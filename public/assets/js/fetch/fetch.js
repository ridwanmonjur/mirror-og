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

async function storeFetchDataInLocalStorage(url) {
    try {
        let isValid = false;
        let data = JSON.parse(localStorage.getItem('countriesData'));
        let innerData = data?.data;
        if (innerData) {
            isValid = innerData[0] && innerData[1] && innerData[99] && innerData[100];
        } 

        if (isValid) {
            return data;
        }

        const response = await fetch(url);
        data = await response.json();
        localStorage.setItem('countriesData', JSON.stringify(data));
        return data;
    } catch (error) {
        console.error('Error storing data in localStorage:', error);
    }
}