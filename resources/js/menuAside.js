import {
    mdiTicketPercent,
    mdiAccountCircle,
    mdiViewDashboard,
    mdiGithub,
    mdiLock,
    mdiAlertCircle,
    mdiSquareEditOutline,
    mdiTable,
    mdiViewList,

    mdiResponsive,
    mdiPalette,
    mdiHospitalBuilding,
    mdiSquareCircle,
    mdiAlphaRBox,
    mdiInformation,
    mdiWallet,
    mdiCashFast,
    mdiAccountCash,
    mdiWalletOutline,
    mdiAccountCashOutline,
    mdiCellphoneWireless,
    mdiCellphoneDock,
    mdiTelevisionClassic,
    mdiLightningBoltOutline,
    mdiRouterWireless,
    mdiSchoolOutline,
    mdiAccountGroup,
    mdiAlphaACircle,
    mdiCog,
    mdiCashMultiple,
    mdiReceiptText,
    mdiSwapHorizontal
} from "@mdi/js";


export default [

    {
        route: "client.dashboard",
        icon: mdiViewDashboard,
        label: "Dashboard",
    },
    {
        label: "Profile",
        icon: mdiAccountCircle,
        menu: [
            {
                route: "client.profile.index",
                label: "Business Info",
            },
            {
                route: "client.profile.kyc.index",
                label: "KYC Details",
            },
        ],
    },
    {
        label: "Transactions",
        icon: mdiCashMultiple,
        menu: [
            {
                href: "/client/transactions?create=true",
                label: "New Transaction",
            },
            {
                route: "client.transactions.index",
                label: "My Transactions",
            },
        ],
    },
    {
        label: "Invoices & Receipts",
        icon: mdiReceiptText,
        menu: [
            {
                route: "client.invoices.index",
                label: "My Invoices",
            },
            {
                route: "client.receipts.index",
                label: "My Receipts",
            },
        ],
    },
    {
        route: "client.rates.index",
        icon: mdiSwapHorizontal,
        label: "Rates",
    },
    {
        label: "Settings",
        icon: mdiCog,
        menu: [
            {
                route: "client.change_password",
                label: "Change Password",
            },
            {
                route: "client.receipts.index",
                label: "General Settings",
            },
        ],
    },



];
