import abaBankLogo from "../../../assets/logos/ABA BANK.png";
import visaLogo from "../../../assets/logos/VISA.png";
import masterLogo from "../../../assets/logos/Master.png";
import jcbLogo from "../../../assets/logos/JCB.png";
import WeChatLogo from "../../../assets/logos/Wechat.png";
import AlipayLogo from "../../../assets/logos/Alipay.png";

export const paymentOptions = [
    // {
    //     value: "aba_khqr",
    //     label: "ABA KHQR",
    //     description: "Scan to pay with any banking app",
    //     logo: abaBankLogo,
    // },
    {
        value: "payway_khpay",
        label: "ABA Pay (KHPAY)",
        description: "Direct QR payment via KHPAY",
        logo: abaBankLogo,
    },
    {
        value: "khpay_qr",
        label: "KHPAY QR",
        description: "Generate dynamic QR code",
        logo: abaBankLogo,
    },
    {
        value: "card",
        label: "Credit / Debit Card",
        description: "Visa, Mastercard, JCB",
        logo: [visaLogo, masterLogo, jcbLogo],
    },
    {
        value: "alipay",
        label: "Alipay",
        description: "Scan to pay with Alipay",
        logo: AlipayLogo,
    },
    {
        value: "wechat",
        label: "WeChat Pay",
        description: "Scan to pay with WeChat",
        logo: WeChatLogo,
    },
];
