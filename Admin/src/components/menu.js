
export const menuItems = [
    {
        id: 1,
        label: "Tableaux de Bords",
        isTitle: true,
        roles: ['Admin', 'Client', 'Agent']
    },
    {
        id: 2,
        label: "Tableau de bord",
        icon: "bx-home-circle",
        roles: ['Admin', 'Client', 'Agent'],
        subItems: [
            {
                id: 3,
                label: "Activité",
                link: "/activite",
                parentId: 2,
                roles: ['Admin']
            },
            {
                id: 4,
                label: "Statistiques",
                link: "/activite/agent",
                parentId: 2,
                roles: ['Agent']
            },
            {
                id: 5,
                label: "Dashboard",
                link: "/activite/client",
                parentId: 2,
                roles: ['Client']
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
        id: 20,
        label: "Utilisateurs",
        icon: "bx-user-circle",
        roles: ['Admin'],
        subItems: [
            {
                id: 21,
                label: "Liste des utilisateurs",
                link: "/listuser",
                parentId: 20,
                roles: ['Admin']
            },
            {
                id: 22,
                label: "Créer un utilisateur",
                link: "/createUser",
                parentId: 20,
                roles: ['Admin']
            },
        ]
    },
    {
        id: 43,
        label: "Tickets",
        icon: "bx-receipt",
        roles: ['Admin', 'Client', 'Agent'],
        subItems: [
            {
                id: 44,
                label: "All tickets",
                link: "/listticket",
                parentId: 43,
                roles: ['Admin']
            },
            {
                id: 45,
                label: "liste des tickets",
                link: "/listTicketClient",
                parentId: 43,
                roles: ['Client']
            },
            {
                id: 46,
                label: "Tickets assignes",
                link: "/agent/tickets",
                parentId: 43,
                roles: ['Agent']
            },
            // {
            //     id: 47,
            //     label: "Tickets fermés",
            //     link: "/closedticket",
            //     parentId: 43,
            //     roles: ['Admin']
            // },
            {
                id: 48,
                label: "Creation",
                link: "/createtickets",
                parentId: 43,
                roles: ['Client']
            },
            {
                id: 49,
                label: "Historique",
                link: "/historiqueticket",
                parentId: 43,
                roles: ['Client']
            },
            {
                id: 50,
                label: "Liste des rapports",
                link: "/listeRapport",
                parentId: 43,
                roles: ['Agent', 'Admin']
            },
        ]
    },
    {
        id: 51,
        label: "Clients",
        icon: "bx-user",
        roles: ['Admin'],
        subItems: [
            {
                id: 52,
                label: "Liste des clients",
                link: "/listeClients",
                parentId: 51,
                roles: ['Admin']
            },
            // {
            //     id: 53,
            //     label: "Creer un client",
            //     link: "/openticket",
            //     parentId: 51,
            //     roles: ['Admin']
            // },
            // {
            //     id: 53,
            //     label: "Profil Client",
            //     link: "/closedticket",
            //     parentId: 50,
            //     roles: ['Admin']
            // },
        ]
    },
    {
        id: 54,
        label: "Profil Client",
        icon: "bxs-user-detail",
        link: "/profileClient",
        roles: ['Client']
    },
    {
        id: 55,
        label: "Profil Utilisateur",
        icon: "bxs-user-detail",
        link: "/profileUser",
        roles: ['Admin', 'Agent']
    },
    // {
    //     id: 55,
    //     label: "menuitems.authentication.text",
    //     icon: "bx-user-circle",
    //     subItems: [
    //         {
    //             id: 56,
    //             label: "menuitems.authentication.list.login",
    //             link: "/",
    //             parentId: 55
    //         },
    //         {
    //             id: 58,
    //             label: "menuitems.authentication.list.register",
    //             link: "/auth/register-1",
    //             parentId: 55
    //         },
    //         {
    //             id: 60,
    //             label: "menuitems.authentication.list.recoverpwd",
    //             link: "/auth-recoverpwd/:token",
    //             parentId: 55
    //         },
    //         {
    //             id: 64,
    //             label: "menuitems.authentication.list.confirm-mail",
    //             link: "/auth/confirm-mail",
    //             parentId: 55
    //         },
    //         {
    //             id: 66,
    //             label: "menuitems.authentication.list.verification",
    //             link: "/auth/email-verification",
    //             parentId: 55
    //         },
    //         {
    //             id: 68,
    //             label: "menuitems.authentication.list.verification-step",
    //             link: "/auth/two-step-verification",
    //             parentId: 55
    //         }
    //     ]
    // },

];

