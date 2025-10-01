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
        route: "admin.users.index",
        icon: mdiAccountGroup,
        label: "All Users",
    },
    {
        label: "Businesses",
        icon: mdiDomain,
        menu: [
            {
                route: "admin.kyc.index",
                label: "All Businesses",
            },
            {
                href: "/admin/kyc?status=pending",
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
                label: "All Transactions",
            },
            {
                href: "/admin/transactions?status=pending_request",
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
                label: "All Invoices",
            },
            {
                route: "admin.receipts.index",
                label: "All Receipts",
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
    // {
    //     label: "Reports",
    //     icon: mdiFileChart,
    //     menu: [
    //         {
    //             route: "admin.reports.index",
    //             label: "Daily / Weekly / Monthly Reports",
    //         },


    //     ],
    // },
    {
        route: "admin.settings.edit",
        label: "System Settings",
        icon: mdiCog,

    },


];
