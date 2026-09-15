import { LinkCardSkeleton } from '@/components/LinkCardStates';
import { PostCardSkeleton } from '@/components/PostCardStates';
import { Skeleton } from '@/components/ui/skeleton';

import { DashboardChartsSkeleton } from './DashboardChartsStates';

export function HomeSkeleton() {
    return (
        <div
            role="status"
            aria-label="Memuat beranda"
            className="mx-auto flex w-full max-w-2xl flex-col gap-[39.375px] py-[39.375px] md:max-w-3xl md:gap-8.75 md:py-8.75 lg:max-w-4xl"
        >
            <div className="flex flex-col gap-4">
                <h1 className="flex flex-col gap-1">
                    <Skeleton className="h-9 w-full max-w-xl" />
                </h1>
                <div className="flex flex-col gap-1">
                    <Skeleton className="h-5 w-full max-w-xl" />
                    <Skeleton className="h-5 w-full max-w-xl" />
                </div>
            </div>
            <div className="grid gap-y-10 md:grid-cols-[minmax(0,1.8fr)_minmax(0,1fr)] md:gap-x-12 md:gap-y-9">
                <div className="flex flex-col">
                    <Skeleton className="h-7 w-48" />
                    <div className="mt-[22.5px] flex flex-col gap-4 md:mt-5">
                        {Array.from({ length: 5 }, (_, index) => (
                            <PostCardSkeleton key={index} />
                        ))}
                    </div>
                </div>
                <div className="flex flex-col">
                    <Skeleton className="h-7 w-40" />
                    <div className="mt-[22.5px] flex flex-col gap-4 md:mt-5">
                        {Array.from({ length: 5 }, (_, index) => (
                            <LinkCardSkeleton key={index} />
                        ))}
                    </div>
                </div>
            </div>
            <DashboardChartsSkeleton />
        </div>
    );
}
