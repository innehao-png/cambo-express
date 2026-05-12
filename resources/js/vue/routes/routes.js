import { createRouter, createWebHistory } from "vue-router";
import Home from "../pages/Home.vue";
import Navbar from "../components/includes/Navbar.vue";
import Sidebar from "../components/includes/Sidebar.vue";
import Login from "../pages/auth/Login.vue";
import Register from "../pages/auth/Register.vue";
import MyAccount from "../pages/MyAccount.vue";
import Cart from "../pages/cart/Cart.vue";
import Checkout from "../pages/payment/Checkout.vue";
import ForgotPassword from "../pages/auth/ForgotPassword.vue";
import ResetPassword from "../pages/auth/ResetPassword.vue";
import Dashboard from "../pages/admin/Dashboard.vue";
import ProductList from "../pages/admin/product/ProductList.vue";
import UserList from "../pages/admin/user/UserList.vue";
import AdminNavbar from "../components/includes/AdminNavbar.vue";

const includes = {
    AdminNavbar: AdminNavbar,
    Sidebar: Sidebar,
};

const routes = [
    {
        path: "/",
        name: "Home",
        components: {
            default: Home,
            Navbar: Navbar,
        },
    },
    {
        path: "/admin/dashboard",
        name: "Dashboard",
        components: {
            default: Dashboard,
            ...includes,
        },
        meta: { requiresAdmin: true },
    },
    {
        path: "/admin/product",
        name: "ProductList",
        components: {
            default: ProductList,
            ...includes,
        },
        meta: { requiresAdmin: true },
    },
    {
        path: "/admin/user",
        name: "UserList",
        components: {
            default: UserList,
            ...includes,
        },
        meta: { requiresAdmin: true },
    },
    {
        path: "/login",
        name: "Login",
        component: Login,
        meta: { requiresGuest: true },
    },
    {
        path: "/register",
        name: "Register",
        component: Register,
        meta: { requiresGuest: true },
    },
    {
        path: "/account",
        name: "MyAccount",
        components: {
            default: MyAccount,
            Navbar: Navbar,
        },
        meta: { requiresAuth: true },
    },
    {
        path: "/cart",
        name: "Cart",
        components: {
            default: Cart,
            Navbar: Navbar,
        },
    },
    {
        path: "/orders",
        name: "Orders",
        components: {
            default: OrderList,
            Navbar: Navbar,
        },
        meta: { requiresAuth: true },
    },
    {
        path: "/cart/checkout",
        name: "Checkout",
        components: {
            default: Checkout,
            Navbar: Navbar,
        },
        meta: { requiresAuth: true },
    },
    {
        path: "/forgot-password",
        name: "ForgotPassword",
        component: ForgotPassword,
        meta: { requiresGuest: true },
    },
    {
        path: "/reset-password",
        name: "ResetPassword",
        component: ResetPassword,
        meta: { requiresGuest: true },
    },
    {
        path: "/payment/success",
        component: () => import("../pages/payment/SuccessPage.vue"),
    },
    {
        path: "/payment/failed",
        component: () => import("../pages/payment/FailedPage.vue"),
    },
    {
        path: "/payment/cancel",
        component: () => import("../pages/payment/FailedPage.vue"), // reuse same page
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to) {
        if (to.hash) {
            return {
                el: to.hash,
                behavior: 'smooth'
            }
        }
    }
});

import { useAuthStore } from "../api/stores/authStore";
import OrderList from "../pages/order/OrderList.vue";

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Ensure user data is loaded if we have a token but no user object
    if (authStore.token && !authStore.user) {
        try {
            await authStore.getProfile();
        } catch (error) {
            authStore.logout();
            return next({ name: "Login" });
        }
    }

    const isAuthenticated = authStore.isAuthenticated;
    const isAdmin = authStore.isAdmin;

    // Handle guest-only routes
    if (to.meta.requiresGuest && isAuthenticated) {
        return next(isAdmin ? { name: "Dashboard" } : { name: "Home" });
    }

    // Handle admin-only routes
    if (to.meta.requiresAdmin) {
        if (!isAuthenticated) {
            localStorage.setItem("intended_url", to.fullPath);
            return next({ name: "Login" });
        }
        if (!isAdmin) {
            return next({ name: "Home" });
        }
    }

    // Handle authenticated-only routes
    if (to.meta.requiresAuth && !isAuthenticated) {
        localStorage.setItem("intended_url", to.fullPath);
        return next({ name: "Login" });
    }

    next();
});

export default router;
