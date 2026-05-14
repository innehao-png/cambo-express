import { ref, computed, onUnmounted } from "vue";

/**
 * A reusable timer composable
 * @param {number} durationSeconds - Default duration in seconds
 * @param {Function} onExpired - Callback function when timer reaches zero
 */
export function useTimer(durationSeconds = 60, onExpired = null) {
    const timeLeft = ref(0);
    const expired = ref(false);
    let countdown = null;

    const formattedTime = computed(() => {
        const m = Math.floor(timeLeft.value / 60).toString().padStart(2, "0");
        const s = (timeLeft.value % 60).toString().padStart(2, "0");
        return `${m}:${s}`;
    });

    function startTimer(customDuration = null) {
        stopTimer();
        expired.value = false;
        timeLeft.value = customDuration || durationSeconds;

        countdown = setInterval(() => {
            timeLeft.value--;
            if (timeLeft.value <= 0) {
                stopTimer();
                expired.value = true;
                if (onExpired) onExpired();
            }
        }, 1000);
    }

    function stopTimer() {
        if (countdown) {
            clearInterval(countdown);
            countdown = null;
        }
    }

    onUnmounted(() => {
        stopTimer();
    });

    return {
        timeLeft,
        expired,
        formattedTime,
        startTimer,
        stopTimer,
    };
}
