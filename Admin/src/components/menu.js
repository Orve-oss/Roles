export const menuItems = [
    {
        id: 1,
        label: "Tableaux de Bords",
        isTitle: true
    },
    {
        id: 2,
        label: "Tableau de bord",
        icon: "bx-home-circle",
        subItems: [
            {
                id: 3,
                label: "Activité",
                link: "/",
                parentId: 2
            },
            {
                id: 4,
                label: "Statistiques",
                link: "/",
                parentId: 2
            },
        ]
    },
    {
        id: 7,
        isLayout: true
    },
    {
        id: 54,
        label: "menuitems.pages.text",
        isTitle: true
    },
    {
        id: 43,
        label: "Tickets",
        icon: "bx-receipt",
        subItems: [
            {
                id: 44,
                label: "All tickets",
                link: "/listticket",
                parentId: 43
            },
            {
                id: 45,
                label: "Tickets ouverts",
                link: "/openticket",
                parentId: 43
            },
            {
                id: 46,
                label: "Tickets fermés",
                link: "/closedticket",
                parentId: 43
            },
            {
                id: 47,
                label: "Creation",
                link: "/createtickets",
                parentId: 43
            },
            {
                id: 48,
                label: "Historique",
                link: "/historiqueticket",
                parentId: 43
            },
        ]
    },
    {
        id: 50,
        label: "Clients",
        icon: "bx-user",
        subItems: [
            {
                id: 51,
                label: "Liste des clients",
                link: "/listticket",
                parentId: 50
            },
            {
                id: 52,
                label: "Creer un client",
                link: "/openticket",
                parentId: 50
            },
            {
                id: 53,
                label: "Profil Client",
                link: "/closedticket",
                parentId: 50
            },
        ]
    },
    {
        id: 55,
        label: "menuitems.authentication.text",
        icon: "bx-user-circle",
        subItems: [
            {
                id: 56,
                label: "menuitems.authentication.list.login",
                link: "/auth/login-1",
                parentId: 55
            },
            {
                id: 58,
                label: "menuitems.authentication.list.register",
                link: "/auth/register-1",
                parentId: 55
            },
            {
                id: 60,
                label: "menuitems.authentication.list.recoverpwd",
                link: "/auth/recoverpwd",
                parentId: 55
            },
            {
                id: 62,
                label: "menuitems.authentication.list.lockscreen",
                link: "/auth/lock-screen",
                parentId: 55
            },
            {
                id: 64,
                label: "menuitems.authentication.list.confirm-mail",
                link: "/auth/confirm-mail",
                parentId: 55
            },
            {
                id: 66,
                label: "menuitems.authentication.list.verification",
                link: "/auth/email-verification",
                parentId: 55
            },
            {
                id: 68,
                label: "menuitems.authentication.list.verification-step",
                link: "/auth/two-step-verification",
                parentId: 55
            }
        ]
    },

];
