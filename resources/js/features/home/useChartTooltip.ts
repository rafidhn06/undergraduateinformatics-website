import { useEffect, useRef, useState } from 'react';

import { useMediaQuery } from '@/hooks/useMediaQuery';

export type ChartTooltipTrigger = 'hover' | 'click';

export function useChartTooltip(): {
    containerRef: React.RefObject<HTMLDivElement | null>;
    trigger: ChartTooltipTrigger;
    active: boolean | undefined;
    chartProps: {
        onClick: () => void;
        onMouseMove: () => void;
        onTouchStart: () => void;
    };
} {
    const isMobile = useMediaQuery('(max-width: 1023px)');
    const [active, setActive] = useState<boolean | undefined>(undefined);
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!isMobile) {
            return;
        }

        function handleClickOutside(event: MouseEvent) {
            if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
                setActive(false);
            }
        }

        document.addEventListener('click', handleClickOutside);
        return () => document.removeEventListener('click', handleClickOutside);
    }, [isMobile]);

    const reset = () => setActive(undefined);

    return {
        containerRef,
        trigger: isMobile ? 'click' : 'hover',
        active,
        chartProps: {
            onClick: reset,
            onMouseMove: reset,
            onTouchStart: reset,
        },
    };
}
