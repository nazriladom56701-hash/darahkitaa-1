<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Users,
    CalendarDays,
    Droplet,
    Building2,
    FileSpreadsheet,
    ShieldCheck,
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
                <SidebarGroupLabel
                    class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    Umum
                </SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem
                        v-for="item in generalItems"
                        :key="item.title"
                    >
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(item.href)"
                            :tooltip="item.title"
                        >
                            <Link
                                :href="item.href"
                                class="flex items-center gap-2"
                            >
                                <component
                                    :is="item.icon"
                                    class="size-4 text-red-500"
                                />
                                <span class="font-medium">{{
                                    item.title
                                }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- MODUL UTAMA & AKSI ADMIN -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel
                    class="flex items-center justify-between text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    <span>Modul Utama</span>
                    <span
                        class="text-[10px] font-normal text-emerald-600 dark:text-emerald-400"
                        >Aksi Admin</span
                    >
                </SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem
                        v-for="item in mainModules"
                        :key="item.title"
                    >
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(item.href)"
                            :tooltip="item.title"
                        >
                            <Link
                                :href="item.href"
                                class="flex w-full items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <component
                                        :is="item.icon"
                                        class="size-4 text-amber-600 dark:text-amber-500"
                                    />
                                    <span class="font-medium">{{
                                        item.title
                                    }}</span>
                                </div>
                                <span
                                    class="rounded border border-emerald-500/20 bg-emerald-500/10 px-1.5 py-0.5 font-mono text-[10px] font-medium text-emerald-700 dark:text-emerald-400"
                                >
                                    {{ item.actionLabel }}
                                </span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- LAPORAN & NOTIFIKASI -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel
                    class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    Output & Layanan
                </SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem
                        v-for="item in reportItems"
                        :key="item.title"
                    >
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(item.href)"
                            :tooltip="item.title"
                        >
                            <Link
                                :href="item.href"
                                class="flex items-center gap-2"
                            >
                                <component
                                    :is="item.icon"
                                    class="size-4 text-blue-500"
                                />
                                <span class="font-medium">{{
                                    item.title
                                }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <div
                class="mx-2 mb-2 flex items-center gap-2 rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-2 text-[11px] text-red-700 dark:text-red-400"
            >
                <ShieldCheck class="size-4 shrink-0 text-red-600" />
                <div class="truncate">
                    <p class="leading-tight font-semibold">DarahKitaa PMI</p>
                    <p class="truncate text-[10px] text-muted-foreground">
                        Unit Transfusi Darah
                    </p>
                </div>
            </div>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
