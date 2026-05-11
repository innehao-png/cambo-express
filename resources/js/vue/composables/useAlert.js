import { ref } from 'vue';

export function useAlert() {
    const message = ref("");
    const alertType = ref("");
    const title = ref("");
    const showAlert = ref(false);

    const showMessage = (msg, type = "create", alertTitle = "Success") => {
        message.value = msg;
        alertType.value = type;
        title.value = alertTitle;
        showAlert.value = true;
    };

    const hideMessage = () => {
        showAlert.value = false;
    };

    return {
        message,
        alertType,
        title,
        showAlert,
        showMessage,
        hideMessage,
    };
}
