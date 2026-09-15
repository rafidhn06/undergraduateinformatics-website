import { Skeleton } from '@/components/ui/skeleton';

export const skeletonSectionCount = 12;

export function TableOfContentsSkeleton() {
    return (
        <div className="typeset typeset-article lg:sticky lg:top-27">
            <h3 className="lg:bg-background lg:sticky lg:top-0 lg:z-10">
                <Skeleton className="h-7 w-1/2" />
            </h3>
            <div className="border-border max-h-[12.25rem] scrollbar-none overflow-y-auto border-l [direction:rtl] md:max-h-[10.75rem] lg:max-h-[calc(100vh-13rem)] [&>ul]:mt-0">
                <ul className="pl-3 [direction:ltr] [&>li]:mt-0 [&>li+li]:mt-[0.5em]">
                    {Array.from({ length: skeletonSectionCount }, (_, sectionIndex) => (
                        <li key={sectionIndex} className="list-none">
                            <Skeleton className="h-6 w-full" />
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
}
