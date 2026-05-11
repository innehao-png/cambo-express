/**
 * Standardizes backend error messages from Axios responses.
 * @param {Error} error
 * @returns {string}
 */
export const parseError = (error) => {
    return (
        error.response?.data?.message ||
        error.response?.data?.detail ||
        error.message ||
        "An unexpected error occurred. Please try again later."
    );
};
