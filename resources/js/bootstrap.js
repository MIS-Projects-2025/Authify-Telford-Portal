import axios from "axios";
import { toast } from "sonner";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// ✅ Global Response Interceptor
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 429) {
            // This triggers the Toaster you added in app.jsx
            toast.error("Whoa! Slow down a bit.", {
                description:
                    "You've made too many requests. Please wait a few seconds.",
                duration: 5000,
            });
        }
        return Promise.reject(error);
    },
);
