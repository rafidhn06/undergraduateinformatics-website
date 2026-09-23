import { Skeleton } from '@/components/ui/skeleton';

export function PostSkeleton() {
    return (
        <div
            role="status"
            aria-label="Memuat detail informasi"
            className="mx-auto w-full max-w-[37em] py-[39.375px] md:py-8.75"
        >
            <article className="typeset typeset-article">
                <h1>
                    <Skeleton className="h-9 w-full" />
                </h1>
                <h2 className="mt-2">
                    <Skeleton className="h-7 w-full" />
                </h2>
                <div className="mt-6">
                    <Skeleton className="aspect-4/3 w-full" />
                </div>
                <div className="mt-[39.375px] space-y-2 md:mt-8.75">
                    <Skeleton className="h-5 w-full" />
                    <Skeleton className="h-5 w-full" />
                    <Skeleton className="h-5 w-full" />
                </div>
                <div className="mt-[22.5px] flex flex-wrap items-center gap-2 md:mt-5">
                    <Skeleton className="h-5 w-1/2" />
                </div>
            </article>
        </div>
    );
}
