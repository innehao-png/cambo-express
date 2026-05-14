<template>
    <section class="bg-white py-8 antialiased md:py-16 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 2xl:px-0">
            <div class="mx-auto max-w-5xl">

                <!-- Success Section -->
                <div v-if="isPaymentSuccess" class="mb-8">
                    <div class="bg-green-50 rounded-lg p-8 text-center">
                        <i class="fa-solid fa-circle-check text-green-500 text-6xl mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                        <p class="text-gray-600 mb-6">Your order has been paid successfully.</p>
                        <router-link :to="{ name: 'Orders' }"
                            class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2 rounded-lg transition">
                            View My Orders
                        </router-link>
                    </div>
                </div>

                <!-- Cart + Payment options -->
                <div v-if="!isPaymentSuccess" class="lg:flex gap-8">

                    <!-- Cart items -->
                    <div class="lg:w-2/3 space-y-6">
                        <h3 class="text-3xl font-semibold text-gray-900 mb-4">My Cart</h3>

                        <div v-if="loading" class="text-center py-10 text-gray-400">
                            Loading...
                        </div>
                        <div v-else-if="!items.length" class="text-center py-10 text-gray-400">
                            Your cart is empty.
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="item in items" :key="item.id"
                                class="bg-white rounded-xl shadow p-6 flex flex-col md:flex-row gap-6 items-center">
                                <img class="w-24 h-24 object-contain" :src="item.product.image"
                                    :alt="item.product.name" />
                                <div class="flex-1 text-center md:text-left">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ item.product.name }}
                                    </h3>
                                    <p class="text-sm text-gray-400">Qty: {{ item.quantity }}</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ formatCurrency(item.product.price * item.quantity) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment options -->
                    <div class="lg:w-1/3 space-y-6">
                        <h3 v-if="!qrData.qr_image" class="text-lg font-semibold text-gray-900 mb-4">Choose way to pay</h3>

                        <div v-if="!qrData.qr_image" class="space-y-3">
                            <div v-for="option in paymentOptions" :key="option.value"
                                @click="handleOptionClick(option.value)"
                                class="flex items-center justify-between p-4 rounded-lg cursor-pointer shadow bg-white hover:bg-gray-50 transition"
                                :class="{ 'ring-2 ring-blue-500': paymentMethod === option.value }">
                                <div class="flex items-center gap-3">
                                    <!-- Single or multiple logos -->
                                    <div v-if="Array.isArray(option.logo)" class="flex gap-1">
                                        <img v-for="(ln, idx) in option.logo" :key="idx" :src="ln" class="h-6" />
                                    </div>
                                    <img v-else :src="option.logo" class="h-8" />

                                    <div>
                                        <div class="font-medium text-gray-900">{{ option.label }}</div>
                                        <div class="text-sm text-gray-400">{{ option.description }}</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-300 text-sm"></i>
                            </div>
                        </div>

                        <!-- QR Code Section (for KHPAY) -->
                        <div v-else class="bg-white rounded-xl shadow p-4 text-center">
                            <h3 class="text-lg font-bold mb-4 text-gray-900">Scan to Pay</h3>

                            <!-- QR image with expired overlay -->
                            <div class="relative inline-block rounded-lg shadow-inner">
                                <img :src="qrData.qr_image" class="w-55 h-55 mx-auto block"
                                    :class="{ 'opacity-30': expired }" alt="Payment QR Code" />
                                <div v-if="expired"
                                    class="absolute inset-0 flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-clock-rotate-left text-red-500 text-3xl mb-2"></i>
                                    <p class="text-red-600 font-semibold text-sm">QR Expired</p>
                                    <button @click="handleOptionClick(paymentMethod)"
                                        class="mt-2 text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full transition">
                                        Refresh
                                    </button>
                                </div>
                            </div>

                            <!-- Countdown -->
                            <div v-if="!expired" class="mt-3 flex items-center justify-center gap-1 text-sm"
                                :class="timeLeft <= 30 ? 'text-red-500 font-semibold' : 'text-gray-500'">
                                <i class="fa-regular fa-clock"></i>
                                <span>Expires in {{ formattedTime }}</span>
                            </div>

                            <p class="text-sm text-gray-500 mt-2 mb-4">Please scan the QR code with your mobile banking app</p>
                            <div v-if="qrData.abapay_deeplink && !expired" class="mt-4">
                                <a :href="qrData.abapay_deeplink"
                                    class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                                    Open in ABA Mobile
                                </a>
                            </div>
                        </div>

                        <!-- Loading spinner while waiting for response -->
                        <div v-if="checkoutLoading" class="text-center py-4 text-gray-400 text-sm">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                            Processing payment...
                        </div>

                        <!-- Error message -->
                        <!-- <div v-if="ErrorMsg" class="text-center py-2 text-red-500 text-sm">
                            {{ ErrorMsg }}
                        </div> -->


                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="fixed top-5 right-5 z-50">
        <AlertMsg v-if="showAlert" type="error" :title="title" :message="ErrorMsg" @close="hideMessage" />
    </div>

</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import paymentService from "../../api/services/payment/paymentService";
import { useCartStore } from "../../api/stores/cartStore";
import { formatCurrency } from "../../utils/formatters";
import { parseError } from "../../utils/errors";
import AlertMsg from "../../components/messages/AlertMsg.vue";
import { useAlert } from "../../composables/useAlert";
import { paymentOptions } from "../../constants/paymentOptions";

// ─────────────────────────────────────────────
// Alert
// ─────────────────────────────────────────────
const { title, showMessage, showAlert, hideMessage } = useAlert();

// ─────────────────────────────────────────────
// Cart
// ─────────────────────────────────────────────
const cartStore = useCartStore();
const loading = computed(() => cartStore.loading);
const items = computed(() => cartStore.carts?.data?.cart_items || []);

// ─────────────────────────────────────────────
// Payment Options
// ─────────────────────────────────────────────
const paymentMethod = ref("");

// ─────────────────────────────────────────────
// Checkout State
// ─────────────────────────────────────────────
const tranId = ref("");
const isPaymentSuccess = ref(false);
const checkoutLoading = ref(false);
const ErrorMsg = ref("");
const qrData = ref({
    qr_image: null,
    qr_string: null,
    abapay_deeplink: null,
});

// ─────────────────────────────────────────────
// QR Expiry Timer
// ─────────────────────────────────────────────
const QR_LIFETIME_SECONDS = 3 * 60; // 1 minute in seconds
const timeLeft = ref(0);
const expired = ref(false);
let countdown = null;

const formattedTime = computed(() => {
    const m = Math.floor(timeLeft.value / 60).toString().padStart(2, "0");
    const s = (timeLeft.value % 60).toString().padStart(2, "0");
    return `${m}:${s}`;
});

function startTimer() {
    if (countdown) clearInterval(countdown);
    expired.value = false;
    timeLeft.value = QR_LIFETIME_SECONDS;

    countdown = setInterval(() => {
        timeLeft.value--;
        if (timeLeft.value <= 0) {
            clearInterval(countdown);
            countdown = null;
            expired.value = true;
            stopPolling(); // stop polling when QR expires
        }
    }, 1000);
}

// ─────────────────────────────────────────────
// Polling
// ─────────────────────────────────────────────
let pollTimer = null;

function startPolling() {
    pollTimer = setInterval(async () => {
        await checkPaymentStatus();
    }, 3000);
}

function stopPolling() {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = null;
}

async function checkPaymentStatus() {
    if (!tranId.value) return;

    try {
        let res;
        if (paymentMethod.value === "payway_khpay") {
            res = await paymentService.paywayCheckStatus(tranId.value);
        }

        console.log("Check Payment Status:", res);

        const orderStatus = res.data.order.status;
        const paywayStatus = res.data.payway.status;

        if (orderStatus === "paid" || paywayStatus === "PAID") {
            onPaymentSuccess();
        } else if (paywayStatus === "FAILED" || paywayStatus === "DECLINED") {
            onPaymentFailed();
        }
    } catch (err) {
        console.error("[Payment] check status error", err);
        showMessage(parseError(err), "error", "Failed");
    }
}

// ─────────────────────────────────────────────
// Payment Option Click
// ─────────────────────────────────────────────
function handleOptionClick(value) {
    paymentMethod.value = value;
    qrData.value = { qr_image: null, qr_string: null, abapay_deeplink: null };

    if (value === "payway_khpay") {
        proceedPayWayCheckout();
    } else if (value === "khpay_qr") {
        proceedKHQRCheckout();
    } else {
        ErrorMsg.value = "This option is not available yet.";
        showMessage(parseError("Defect"), "error", "Failed");
    }
}

// ─────────────────────────────────────────────
// PayWay Checkout
// ─────────────────────────────────────────────
async function proceedPayWayCheckout() {
    ErrorMsg.value = "";
    checkoutLoading.value = true;
    isPaymentSuccess.value = false;
    qrData.value = { qr_image: null, qr_string: null, abapay_deeplink: null };
    stopPolling();

    try {
        const res = await paymentService.paywayCheckout({
            shipping_address: "PP",
            currency: "USD",
        });

        console.log("[KHPAY] checkout response", res);

        tranId.value = res.data.tran_id;
        const gatewayData = res.data.data;

        if (gatewayData.payment_url) {
            window.location.href = gatewayData.payment_url;
            return;
        }

        qrData.value = {
            qr_image: gatewayData.qr_image,
            qr_string: gatewayData.qr_string,
            abapay_deeplink: gatewayData.deeplink || gatewayData.abapay_deeplink,
        };

        startTimer();
        startPolling();

    } catch (err) {
        console.error("[KHPAY] checkout error", err);
        showMessage(parseError(err), "error", "Failed");
        ErrorMsg.value = parseError(err);
        stopPolling();
    } finally {
        checkoutLoading.value = false;
    }
}

async function proceedKHQRCheckout() {
    ErrorMsg.value = "";
    checkoutLoading.value = true;
    isPaymentSuccess.value = false;
    qrData.value = { qr_image: null, qr_string: null, abapay_deeplink: null };
    stopPolling();

    try {
        // Calculate total amount
        const total = items.value.reduce((acc, item) => acc + (item.product.price * item.quantity), 0);

        const res = await paymentService.generateKHQR({
            shipping_address: "PP",
        });

        console.log("[KHPAY QR] response", res);

        tranId.value = res.data.tran_id;
        const gatewayData = res.data.data;

        if (gatewayData.payment_url) {
            window.location.href = gatewayData.payment_url;
            return;
        }

        qrData.value = {
            qr_image: gatewayData.qr_image || gatewayData.qr_code,
            qr_string: gatewayData.qr_string || gatewayData.qr_code_string,
            abapay_deeplink: gatewayData.deeplink,
        };

        startTimer();
        startPolling();

    } catch (err) {
        console.error("[KHPAY QR] checkout error", err);
        showMessage(parseError(err), "error", "Failed");
        ErrorMsg.value = parseError(err);
    } finally {
        checkoutLoading.value = false;
    }
}

// ─────────────────────────────────────────────
// Payment Outcomes
// ─────────────────────────────────────────────
function onPaymentSuccess() {
    stopPolling();
    isPaymentSuccess.value = true;
    ErrorMsg.value = "";
    tranId.value = "";
    cartStore.fetchCarts();
}

function onPaymentFailed() {
    stopPolling();
    ErrorMsg.value = "Payment was declined. Please try again.";
    tranId.value = "";
}

// ─────────────────────────────────────────────
// Lifecycle
// ─────────────────────────────────────────────
onMounted(() => {
    cartStore.fetchCarts();
});

onUnmounted(() => {
    stopPolling();
    if (countdown) clearInterval(countdown);
    countdown = null;
});
</script>
