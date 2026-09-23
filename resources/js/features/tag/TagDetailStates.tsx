import { MainAsideLayout } from '@/components/MainAsideLayout';
import { PostCardSkeleton } from '@/components/PostCardStates';
import { TableOfContentsSkeleton } from '@/components/TableOfContentsStates';
import { Skeleton } from '@/components/ui/skeleton';

export function TagDetailSkeleton() {
    return (
        <MainAsideLayout
            role="status"
            aria-label="Memuat detail topik"
            mainContent={
                <>
                    <h1>
                        <Skeleton className="h-9 w-1/2" />
                    </h1>
                    <div className="mt-[22.5px] md:mt-5">
                        <Skeleton className="h-7 w-full" />
                    </div>
                    <div className="mt-[39.375px] md:mt-8.75 lg:hidden">
                        <TableOfContentsSkeleton />
                    </div>
                    {[0, 1, 2, 3, 4].map((sectionIndex) => (
                        <section
                            key={sectionIndex}
                            className={sectionIndex > 0 ? 'mt-8 md:mt-7' : undefined}
                        >
                            <h2>
                                <Skeleton className="h-7 w-1/2" />
                            </h2>
                            <div className="mt-[22.5px] flex flex-col gap-4 md:mt-5">
                                <PostCardSkeleton />
                                <PostCardSkeleton />
                            </div>
                        </section>
                    ))}
                </>
            }
            asideContent={<TableOfContentsSkeleton />}
        />
    );
}
