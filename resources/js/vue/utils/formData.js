/**
 * Converts a plain object into a FormData object.
 * Automatically handles appending values and skipping null/undefined.
 * @param {Object} payload 
 * @returns {FormData}
 */
export const toFormData = (payload) => {
    const formData = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
        if (value !== null && value !== undefined) {
            formData.append(key, value);
        }
    });
    return formData;
};
