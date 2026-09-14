import { Suspense, lazy } from 'react';

import { DashboardChartsSkeleton } from './DashboardChartsStates';
import { LatestLinks } from './LatestLinks';
import { LatestPosts } from './LatestPosts';
import { type HomeData } from './types';

const DashboardCharts = lazy(() =>
    import('./DashboardCharts').then((module) => ({ default: module.DashboardCharts }))
);

export function HomeContent({ data }: { data: HomeData }) {
    const { latest_posts, latest_links, dashboard } = data;

    return (
        <div className="mx-auto flex w-full max-w-2xl flex-col gap-[39.375px] py-[39.375px] md:gap-8.75 md:py-8.75 lg:max-w-4xl">
            <section className="flex flex-col gap-4">
                <h1 className="text-foreground text-4xl font-bold tracking-tight md:text-4xl">
                    Portal Informasi Sarjana Informatika
                </h1>
                <p className="text-muted-foreground max-w-xl text-lg md:text-base">
                    Sumber informasi resmi Program Studi Sarjana Informatika Telkom University untuk
                    perkuliahan peserta didik.
                </p>
            </section>

            <div className="grid gap-y-10 md:gap-y-9 lg:grid-cols-[minmax(0,1.8fr)_minmax(0,1fr)] lg:gap-x-12">
                <LatestPosts posts={latest_posts} />
                <LatestLinks links={latest_links} />
            </div>

            <Suspense fallback={<DashboardChartsSkeleton />}>
                <DashboardCharts datasets={dashboard} />
            </Suspense>
        </div>
    );
}
