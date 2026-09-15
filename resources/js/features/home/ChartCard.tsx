import { useState } from 'react';

import { cn } from '@/lib/utils';

import { BarChart } from './bar-chart';
import { LineChart } from './line-chart';
import { PieChart } from './pie-chart';
import { type DashboardDataset } from './types';

interface ChartCardProps {
    dataset: DashboardDataset;
}

export function ChartCard({ dataset }: ChartCardProps) {
    const [isPointerSession, setIsPointerSession] = useState(false);

    return (
        <div className="flex flex-col">
            <h3 className="text-foreground font-heading min-w-0 truncate text-[20.25px] leading-[1.45] font-semibold md:text-[18px]">
                {dataset.title}
            </h3>
            <div
                tabIndex={0}
                onPointerDown={() => setIsPointerSession(true)}
                onBlur={() => setIsPointerSession(false)}
                className={cn(
                    'no-focus-ring-on-children mt-4.5 md:mt-4',
                    'focus-visible:ring-ring/50 focus-visible:ring-2',
                    isPointerSession && 'no-ring'
                )}
            >
                {dataset.chart_type === 'bar' && (
                    <BarChart labels={dataset.labels} values={dataset.values} />
                )}
                {dataset.chart_type === 'pie' && (
                    <PieChart labels={dataset.labels} values={dataset.values} />
                )}
                {dataset.chart_type === 'line' && (
                    <LineChart labels={dataset.labels} values={dataset.values} />
                )}
            </div>
        </div>
    );
}
