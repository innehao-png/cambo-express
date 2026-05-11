<template>
    <div class="checkout-page">
        <div class="bg-orb bg-orb--1" />
        <div class="bg-orb bg-orb--2" />

        <div class="checkout-card">
            <!-- Header -->
            <div class="card-header">
                <div class="brand">
                    <span class="brand-dot" />
                    <span class="brand-name">Secure Checkout</span>
                </div>
                <div class="lock-icon">
                    <svg
                        width="14"
                        height="14"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <rect
                            x="3"
                            y="11"
                            width="18"
                            height="11"
                            rx="2"
                            ry="2"
                        />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    <span>SSL Secured</span>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-label">Order total</div>
                <div class="summary-amount">
                    <span class="currency">{{ currency }}</span>
                    <span class="amount-value">{{ formattedAmount }}</span>
                </div>
            </div>

            <!-- Divider -->
            <div class="divider" />

            <!-- Payment Method Tabs -->
            <div class="method-tabs">
                <button
                    v-for="method in paymentMethods"
                    :key="method.value"
                    class="method-tab"
                    :class="{ active: selectedMethod === method.value }"
                    @click="selectedMethod = method.value"
                >
                    <span class="method-icon" v-html="method.icon" />
                    <span>{{ method.label }}</span>
                </button>
            </div>

            <!-- Hidden PayWay Form -->
            <form
                method="POST"
                :action="actionUrl"
                target="aba_webservice"
                id="aba_merchant_request"
                ref="payForm"
            >
                <input type="hidden" name="req_time" :value="reqTime" />
                <input type="hidden" name="merchant_id" :value="merchantId" />
                <input type="hidden" name="tran_id" :value="tranId" />
                <input type="hidden" name="amount" :value="amount" />
                <input type="hidden" name="currency" :value="currency" />
                <input
                    type="hidden"
                    name="payment_option"
                    :value="selectedMethod"
                />
                <input type="hidden" name="firstname" :value="firstname" />
                <input type="hidden" name="lastname" :value="lastname" />
                <input type="hidden" name="email" :value="email" />
                <input type="hidden" name="phone" :value="phone" />
                <input type="hidden" name="items" :value="items" />
                <input type="hidden" name="return_url" :value="returnUrl" />
                <input type="hidden" name="cancel_url" :value="cancelUrl" />
                <input
                    type="hidden"
                    name="continue_success_url"
                    :value="continueSuccessUrl"
                />
                <input
                    type="hidden"
                    name="return_params"
                    :value="returnParams"
                />
                <input type="hidden" name="hash" :value="hash" />
            </form>

            <!-- Pay Button -->
            <button
                class="pay-btn"
                :class="{ loading: isLoading }"
                :disabled="isLoading"
                @click="handlePay"
            >
                <span v-if="!isLoading" class="pay-btn__content">
                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    Pay {{ currency }} {{ formattedAmount }}
                </span>
                <span v-else class="pay-btn__spinner">
                    <svg
                        class="spinner"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="3"
                            fill="none"
                            stroke-dasharray="60"
                            stroke-dashoffset="20"
                        />
                    </svg>
                    Processing…
                </span>
            </button>

            <!-- Footer -->
            <div class="card-footer">
                <span>Powered by</span>
                <strong>ABA PayWay</strong>
                <span class="separator">·</span>
                <span>Encrypted &amp; Secure</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PaywayCheckout",

    props: {
        // Required PayWay fields — pass these from your Laravel controller via Inertia or a data object
        reqTime: { type: String, required: true },
        merchantId: { type: String, required: true },
        tranId: { type: String, required: true },
        amount: { type: String, required: true },
        currency: { type: String, default: "USD" },
        firstname: { type: String, default: "" },
        lastname: { type: String, default: "" },
        email: { type: String, default: "" },
        phone: { type: String, default: "" },
        items: { type: String, default: "" },
        returnUrl: { type: String, default: "" },
        cancelUrl: { type: String, default: "" },
        continueSuccessUrl: { type: String, default: "" },
        returnParams: { type: String, default: "" },
        hash: { type: String, required: true },
        sandbox: { type: Boolean, default: true },
    },

    data() {
        return {
            isLoading: false,
            selectedMethod: "cards",
            paymentMethods: [
                {
                    value: "cards",
                    label: "Card",
                    icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>`,
                },
                {
                    value: "abapay",
                    label: "ABA PAY",
                    icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>`,
                },
                {
                    value: "bakong",
                    label: "Bakong",
                    icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>`,
                },
            ],
        };
    },

    computed: {
        actionUrl() {
            return this.sandbox
                ? "https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase"
                : "https://checkout.payway.com.kh/api/payment-gateway/v1/payments/purchase";
        },
        sdkUrl() {
            return this.sandbox
                ? "https://checkout-sandbox.payway.com.kh/plugins/checkout2-0.js"
                : "https://checkout.payway.com.kh/plugins/checkout2-0.js";
        },
        formattedAmount() {
            return parseFloat(this.amount).toFixed(2);
        },
    },

    mounted() {
        this.loadPayWaySDK();
    },

    methods: {
        loadPayWaySDK() {
            if (document.getElementById("payway-sdk")) return;
            const script = document.createElement("script");
            script.id = "payway-sdk";
            script.src = this.sdkUrl;
            document.head.appendChild(script);
        },

        handlePay() {
            this.isLoading = true;
            // ABA_checkout() is injected by the PayWay SDK script
            if (typeof ABA_checkout === "function") {
                ABA_checkout();
            } else {
                console.error("PayWay SDK not loaded yet.");
            }
            // Reset after a short delay in case modal is dismissed
            setTimeout(() => {
                this.isLoading = false;
            }, 3000);
        },
    },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap");

/* ── Page ─────────────────────────────────────────── */
.checkout-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0b0f1a;
    font-family: "DM Sans", sans-serif;
    position: relative;
    overflow: hidden;
    padding: 24px;
}

/* Ambient background orbs */
.bg-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.18;
    pointer-events: none;
}
.bg-orb--1 {
    width: 500px;
    height: 500px;
    background: #2563eb;
    top: -120px;
    left: -100px;
    animation: drift 12s ease-in-out infinite alternate;
}
.bg-orb--2 {
    width: 400px;
    height: 400px;
    background: #7c3aed;
    bottom: -80px;
    right: -80px;
    animation: drift 16s ease-in-out infinite alternate-reverse;
}
@keyframes drift {
    from {
        transform: translate(0, 0) scale(1);
    }
    to {
        transform: translate(30px, 20px) scale(1.08);
    }
}

/* ── Card ─────────────────────────────────────────── */
.checkout-card {
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 32px;
    width: 100%;
    max-width: 420px;
    box-shadow:
        0 32px 64px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
    animation: cardIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
}
@keyframes cardIn {
    from {
        opacity: 0;
        transform: translateY(24px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* ── Header ───────────────────────────────────────── */
.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
}
.brand {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: "Syne", sans-serif;
    font-weight: 600;
    font-size: 0.95rem;
    color: #e2e8f0;
}
.brand-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #3b82f6;
    box-shadow: 0 0 10px #3b82f6;
}
.lock-icon {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.72rem;
    color: #64748b;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 4px 10px;
    border-radius: 100px;
}

/* ── Order Summary ────────────────────────────────── */
.order-summary {
    margin-bottom: 24px;
}
.summary-label {
    font-size: 0.78rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
}
.summary-amount {
    display: flex;
    align-items: baseline;
    gap: 6px;
}
.currency {
    font-family: "Syne", sans-serif;
    font-size: 1.1rem;
    font-weight: 500;
    color: #94a3b8;
}
.amount-value {
    font-family: "Syne", sans-serif;
    font-size: 2.6rem;
    font-weight: 700;
    color: #f1f5f9;
    line-height: 1;
    letter-spacing: -0.02em;
}

/* ── Divider ──────────────────────────────────────── */
.divider {
    height: 1px;
    background: linear-gradient(
        to right,
        transparent,
        rgba(255, 255, 255, 0.08),
        transparent
    );
    margin-bottom: 24px;
}

/* ── Method Tabs ──────────────────────────────────── */
.method-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
}
.method-tab {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 12px 8px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    color: #64748b;
    font-family: "DM Sans", sans-serif;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.method-tab:hover {
    background: rgba(255, 255, 255, 0.07);
    color: #94a3b8;
}
.method-tab.active {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.4);
    color: #93c5fd;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.12);
}
.method-icon {
    display: flex;
    align-items: center;
}

/* ── Pay Button ───────────────────────────────────── */
.pay-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-family: "Syne", sans-serif;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition:
        transform 0.15s ease,
        box-shadow 0.15s ease,
        opacity 0.15s ease;
    box-shadow:
        0 4px 24px rgba(37, 99, 235, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    margin-bottom: 20px;
}
.pay-btn::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.12) 0%,
        transparent 60%
    );
    pointer-events: none;
}
.pay-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow:
        0 8px 32px rgba(37, 99, 235, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
}
.pay-btn:active:not(:disabled) {
    transform: translateY(0);
}
.pay-btn.loading,
.pay-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
.pay-btn__content,
.pay-btn__spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.spinner {
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* ── Footer ───────────────────────────────────────── */
.card-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 0.72rem;
    color: #475569;
}
.card-footer strong {
    color: #64748b;
    font-weight: 600;
}
.separator {
    color: #334155;
}
</style>
