export interface RouteInfo {
    path: string;
    title: string;
    icon: string;
    submenu: RouteInfo[];
    role: any;
}