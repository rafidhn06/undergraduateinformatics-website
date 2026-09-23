import { type ReactNode, useCallback, useEffect, useState } from 'react';

import useEmblaCarousel from 'embla-carousel-react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { useMediaQuery } from '@/hooks/useMediaQuery';
import { cn } from '@/lib/utils';

import { ChartCard } from './ChartCard';
import { type DashboardDataset } from './types';

interface DashboardChartsProps {
    datasets: DashboardDataset[];
}

const GRID_BREAKPOINT = '(min-width: 640px)';

export function DashboardCharts({ datasets }: DashboardChartsProps) {
    const showGrid = useMediaQuery(GRID_BREAKPOINT);
    const [emblaRef, emblaApi] = useEmblaCarousel({ watchDrag: false });
    const [selectedIndex, setSelectedIndex] = useState(0);

    useEffect(() => {
        if (!emblaApi) {
            return;
        }

        const onSelect = () => setSelectedIndex(emblaApi.selectedScrollSnap());

        emblaApi.on('select', onSelect);
        emblaApi.on('reInit', onSelect);
        return () => {
            emblaApi.off('select', onSelect);
            emblaApi.off('reInit', onSelect);
        };
    }, [emblaApi]);

    const canScrollPrev = emblaApi?.canScrollPrev() ?? false;
    const canScrollNext = emblaApi?.canScrollNext() ?? false;

    const scrollTo = useCallback((index: number) => emblaApi?.scrollTo(index), [emblaApi]);

    let content: ReactNode;

    if (datasets.length === 0) {
        content = (
            <p role="status" className="text-muted-foreground mt-[22.5px] md:mt-5">
                Belum ada data statistik.
            </p>
        );
    } else if (showGrid) {
        content = (
            <div className="mt-[22.5px] grid grid-cols-2 gap-4 sm:gap-5 md:mt-5 md:gap-6 lg:grid-cols-3 lg:gap-8">
                {datasets.map((dataset) => (
                    <ChartCard key={dataset.id} dataset={dataset} />
                ))}
            </div>
        );
    } else {
        content = (
            <div className="mt-[22.5px] flex flex-col md:mt-5">
                <div ref={emblaRef} className="w-full overflow-hidden">
                    <div className="flex">
                        {datasets.map((dataset) => (
                            <div key={dataset.id} className="min-w-0 flex-[0_0_100%]">
                                <ChartCard dataset={dataset} />
                            </div>
                        ))}
                    </div>
                </div>
                <div className="mt-1 flex w-full items-center justify-between">
                    <div className="flex flex-1 justify-start gap-2">
                        {datasets.map((dataset, index) => (
                            <button
                                key={dataset.id}
                                type="button"
                                className="flex h-10 flex-1 items-center"
                                onClick={() => scrollTo(index)}
                                aria-label={`Tampilkan grafik ${dataset.title}`}
                                aria-current={index === selectedIndex ? 'true' : 'false'}
                            >
                                <span
                                    className={cn(
                                        'h-0.5 w-full transition-colors duration-300',
                                        index === selectedIndex
                                            ? 'bg-muted-foreground'
                                            : 'bg-muted-foreground/30'
                                    )}
                                />
                            </button>
                        ))}
                    </div>
                    <div className="flex flex-1 items-center justify-end">
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => emblaApi?.scrollPrev()}
                            disabled={!canScrollPrev}
                            className="text-muted-foreground size-10 transition-opacity duration-300 disabled:opacity-30"
                            aria-label="Grafik sebelumnya"
                        >
                            <ChevronLeft className="size-6" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => emblaApi?.scrollNext()}
                            disabled={!canScrollNext}
                            className="text-muted-foreground size-10 transition-opacity duration-300 disabled:opacity-30"
                            aria-label="Grafik berikutnya"
                        >
                            <ChevronRight className="size-6" />
                        </Button>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <section className="flex flex-col">
            <h2 className="text-foreground font-heading text-[22.5px] leading-[1.4] font-semibold md:text-[20px]">
                Statistik Mahasiswa
            </h2>
            {content}
        </section>
    );
}
