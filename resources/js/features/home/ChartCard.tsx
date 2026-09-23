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
            <p className="text-foreground min-w-0 truncate text-lg leading-6 font-medium md:text-base">
                {dataset.title}
            </p>
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
                {dataset.chartType === 'bar' && (
                    <BarChart labels={dataset.labels} values={dataset.values} />
                )}
                {dataset.chartType === 'pie' && (
                    <PieChart labels={dataset.labels} values={dataset.values} />
                )}
                {dataset.chartType === 'line' && (
                    <LineChart labels={dataset.labels} values={dataset.values} />
                )}
            </div>
        </div>
    );
}
