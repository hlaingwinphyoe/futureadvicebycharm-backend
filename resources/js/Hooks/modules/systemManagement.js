export const systemManagement = [
    {
        id: "6",
        name: "Settings",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 256 256"><path fill="currentColor" d="M237.94 107.21a8 8 0 0 0-3.89-5.4l-29.83-17l-.12-33.62a8 8 0 0 0-2.83-6.08a111.91 111.91 0 0 0-36.72-20.67a8 8 0 0 0-6.46.59L128 41.85L97.88 25a8 8 0 0 0-6.47-.6a111.92 111.92 0 0 0-36.68 20.75a8 8 0 0 0-2.83 6.07l-.15 33.65l-29.83 17a8 8 0 0 0-3.89 5.4a106.47 106.47 0 0 0 0 41.56a8 8 0 0 0 3.89 5.4l29.83 17l.12 33.63a8 8 0 0 0 2.83 6.08a111.91 111.91 0 0 0 36.72 20.67a8 8 0 0 0 6.46-.59L128 214.15L158.12 231a7.91 7.91 0 0 0 3.9 1a8.09 8.09 0 0 0 2.57-.42a112.1 112.1 0 0 0 36.68-20.73a8 8 0 0 0 2.83-6.07l.15-33.65l29.83-17a8 8 0 0 0 3.89-5.4a106.47 106.47 0 0 0-.03-41.52M128 168a40 40 0 1 1 40-40a40 40 0 0 1-40 40"/></svg>`,
        url: "/admin",
        type: "group",
        permission: true,
        children: [
            {
                id: "6-1",
                name: "Banks",
                type: "page",
                permission: true,
                url: "/admin/banks",
            },
            {
                id: "6-2",
                name: "Staff",
                type: "page",
                permission: true,
                url: "/admin/staffs",
            },
            {
                id: "6-3",
                name: "Role",
                type: "page",
                permission: true,
                url: "/admin/roles",
            },
            {
                id: "6-4",
                name: "Profile",
                type: "page",
                permission: true,
                url: "/profile",
            },
            {
                id: "6-5",
                name: "System Info",
                type: "page",
                permission: true,
                url: "/admin/system-infos",
            },
        ],
    },
];
