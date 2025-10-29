import "./bootstrap";
import "../css/app.css";
// import "laravel-datatables-vite";

import { createApp } from "vue";
import VueSweetalert2 from "vue-sweetalert2";
import { ZiggyVue } from "ziggy";
import { i18nVue } from "laravel-vue-i18n";
import { getTrans } from "@/helpers";

import PurchaseSearchList from "./components/admin/purchase/PurchaseSearchList.vue";
import AttributeItems from "./components/admin/attribute/AttributeItems.vue";
import PurchaseReceiveList from "./components/admin/purchase_receive/PurchaseReceiveList.vue";
import PurchaseReturnList from "./components/admin/purchase_return/PurchaseReturnList.vue";
import CreateOrder from "./components/admin/order/CreateOrder.vue";
import EditOrder from "./components/admin/order/EditOrder.vue";
import EditReview from "./components/admin/review/EditReview.vue";


const app = createApp({})
    .use(ZiggyVue)
    .use(i18nVue, {
        resolve: async (lang) => {
            const langs = import.meta.glob("../lang/*.json");
            const currentLng = langs[`../lang/${lang}.json`];
            return await currentLng();
        },
    });

// app.config.globalProperties.$t = trans;
app.config.globalProperties.$trans = getTrans;

Object.entries(import.meta.glob("./**/*.vue", { eager: true })).forEach(
    ([path, definition]) => {
        app.component(
            path
                .split("/")
                .pop()
                .replace(/\.\w+$/, ""),
            definition.default
        );
    }
);


app.component("purchase-search-list", PurchaseSearchList);
app.component("attribute-items", AttributeItems);
app.component("purchase-receive-list", PurchaseReceiveList);
app.component("purchase-return-list", PurchaseReturnList);
app.component("create-order", CreateOrder);
app.component("edit-order", EditOrder);
app.component("edit-review", EditReview);

app.use(VueSweetalert2);
app.mount("#vueApp");
