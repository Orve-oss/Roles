
export function filterMenuByRole(menuItems, roles){
    return menuItems.filter(item => {
        if(item.roles && !item.roles.includes(roles)){
            return false;
        }
        if (item.subItems) {
            item.subItems = filterMenuByRole(item.subItems, roles);
        }
        return true;
    });
}
