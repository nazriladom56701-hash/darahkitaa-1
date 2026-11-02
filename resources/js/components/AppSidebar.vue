<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { 
    LayoutGrid, 
    Users, 
    CalendarDays, 
    Droplet, 
    Building2, 
    FileSpreadsheet,
    ShieldCheck
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup,
    SidebarGroupLabel,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';

const { isCurrentUrl } = useCurrentUrl();

const generalItems = [
    {
        title: 'Dashboard',
        subtitle: 'Ringkasan stok & aktivitas',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];

const mainModules = [
    {
        title: 'Data Donor',
        actionLabel: 'Verifikasi',
        href: '/donors',
        icon: Users,
    },
    {
        title: 'Jadwal Donor',
        actionLabel: 'Absensi donor',
        href: '/events',
        icon: CalendarDays,
    },
    {
        title: 'Stok Darah',
        actionLabel: 'Update stok',
        href: '/stocks',
        icon: Droplet,
    },
    {
        title: 'Permintaan RS',
        actionLabel: 'Setujui / tolak',
        href: '/requests',
        icon: Building2,
    },
];

const reportItems = [
    {
        title: 'Laporan & Notifikasi',
        subtitle: 'Ekspor data, kirim pengingat',
        href: '/reports',
        icon: FileSpreadsheet,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="space-y-2">
            <!-- UMUM -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                    Umum
                </SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in generalItems" :key="item.title">
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(item.href)"
                            :tooltip="item.title"
                        >
                            <Link :href="item.href" class="flex items-center gap-2">
                                <component :is="item.icon" class="size-4 text-red-500" />
                                <span class="font-medium">{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- MODUL UTAMA & AKSI ADMIN -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider flex items-center justify-between">
                    <span>Modul Utama</span>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-normal">Aksi Admin</span>
                </SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in mainModules" :key="item.title">
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(item.href)"
                            :tooltip="item.title"
                        >
                            <Link :href="item.href" class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-2">
                                    <component :is="item.icon" class="size-4 text-amber-600 dark:text-amber-500" />
                                    <span class="font-medium">{{ item.title }}</span>
                                </div>
                                <span class="text-[10px] bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 px-1.5 py-0.5 rounded font-mono font-medium border border-emerald-500/20">
                                    {{ item.actionLabel }}
                                </span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- LAPORAN & NOTIFIKASI -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                    Output & Layanan
                </SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in reportItems" :key="item.title">
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(item.href)"
                            :tooltip="item.title"
                        >
                            <Link :href="item.href" class="flex items-center gap-2">
                                <component :is="item.icon" class="size-4 text-blue-500" />
                                <span class="font-medium">{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <div class="px-3 py-2 mx-2 mb-2 rounded-lg bg-red-500/10 border border-red-500/20 text-[11px] text-red-700 dark:text-red-400 flex items-center gap-2">
                <ShieldCheck class="size-4 shrink-0 text-red-600" />
                <div class="truncate">
                    <p class="font-semibold leading-tight">DarahKitaa PMI</p>
                    <p class="text-[10px] text-muted-foreground truncate">Unit Transfusi Darah</p>
                </div>
            </div>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
