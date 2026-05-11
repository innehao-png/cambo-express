/**
 * Format number to currency string ($X.XX)
 * @param {number|string} value
 * @returns {string}
 */
export const formatCurrency = (value) => {
    return `$${Number(value || 0).toFixed(2)}`;
};

/**
 * Format date to a readable string
 * @param {string|Date} date
 * @returns {string}
 */
export const formatDate = (date) => {
    if (!date) return "";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
