import {
    mdiTicketPercent,
    mdiAccountCircle,
    mdiMonitor,
    mdiGithub,
    mdiLock,
    mdiAlertCircle,
    mdiSquareEditOutline,
    mdiTable,
    mdiViewList,
    mdiTelevisionGuide,
    mdiResponsive,
    mdiPalette,
    mdiHospitalBuilding,
    mdiSquareCircle,
    mdiWalletPlus,
    mdiWallet,
    mdiBookAccount,
    mdiChartBar,
    mdiWalletOutline,
    mdiInformation,
    mdiBriefcaseEditOutline,
    mdiBriefcaseClockOutline,
    mdiAccountGroup,
    mdiAccountCash,
    mdiAccountCashOutline,
    mdiCog,
    mdiViewDashboard,
    mdiCashMultiple,
    mdiDomain,
    mdiReceiptText,
    mdiSwapHorizontal,
    mdiFileChart,
} from "@mdi/js";


export default [
    {
        route: "admin.dashboard",
        icon: mdiViewDashboard,
        label: "Dashboard",
    },
    {
        label: "Businesses",
        icon: mdiDomain,
        menu: [
            {
                route: "admin.users.index",
                label: "All Businesses",
            },
            {
                route: "admin.kyc.index",
                label: "Pending KYC Verification",
            },
        ],
    },
    {
        label: "Transactions",
        icon: mdiCashMultiple,
        menu: [
            {
                route: "admin.transactions.index",
                label: "Pending Approvals",
            },
            {
                href: "/admin/transactions?status=completed",
                label: "Completed Transactions",
            },
            {
                href: "/admin/transactions?status=rejected",
                label: "Rejected Transactions",
            },
        ],
    },
    {
        label: "Invoices & Proofs",
        icon: mdiReceiptText,
        menu: [
            {
                route: "admin.invoices.index",
                label: "Submitted Invoices",
            },
            {
                href: "/admin/invoices?status=approved",
                label: "Approved Invoices",
            },
            {
                href: "/admin/invoices?status=rejected",
                label: "Rejected Invoices",
            },

        ],
    },
    {
        label: "Exchange Rates",
        icon: mdiSwapHorizontal,
        menu: [
            {
                route: "admin.rates.index",
                label: "Manage Rates",
            },


        ],
    },
    {
        label: "Reports",
        icon: mdiFileChart,
        menu: [
            {
                route: "admin.reports.index",
                label: "Daily / Weekly / Monthly Reports",
            },


        ],
    },
    {
        route: "admin.settings.index",
        label: "System Settings",
        icon: mdiCog,

    },


];
