import { RouteInfo } from './sidebar.metadata';

export const ROUTES: RouteInfo[] = [
    {
        role: 'dashboard', path: '/dashboard', title: 'Dashboard', icon: '', submenu: []
    },
    {
        role: 'claim_refund', path: '/user/claim-refund', title: 'Claim Refund', icon: '', submenu: []
    },
    {
        role: 'track_status', path: '/user/track-status', title: 'Track Status', icon: '', submenu: []
    }
];
