const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_URL, // adjust as needed
    headers: {
        "Content-Type": "application/json",
    },
});

// request interceptor to attach token
apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error),
);

// optional response interceptor for global error handling
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            // you could emit an event, redirect to login, or clear storage
            localStorage.removeItem("token");
            // window.location.href = "/login"; // uncomment if desired
        }
        return Promise.reject(error);
    },
);

export default apiClient;
