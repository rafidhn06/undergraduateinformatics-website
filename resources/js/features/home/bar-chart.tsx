import {
    Bar,
    CartesianGrid,
    Cell,
    BarChart as RechartsBarChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

import { chartColor } from './chart-color';
import { ChartTooltip } from './chart-tooltip';
import { useChartTooltip } from './useChartTooltip';

interface BarChartProps {
    labels: string[];
    values: number[];
}

export function BarChart({ labels, values }: BarChartProps) {
    const { containerRef, trigger, active, chartProps } = useChartTooltip();

    const data = labels.map((label, index) => ({
        label,
        value: values[index] ?? 0,
    }));

    return (
        <div ref={containerRef} className="aspect-[4/3] w-full">
            <ResponsiveContainer width="100%" height="100%">
                <RechartsBarChart
                    data={data}
                    margin={{ top: 8, right: 8, bottom: 12, left: 8 }}
                    {...chartProps}
                >
                    <CartesianGrid vertical={false} stroke="var(--border)" />
                    <XAxis
                        dataKey="label"
                        tickLine={false}
                        axisLine={false}
                        tick={{ fill: 'var(--muted-foreground)', fontSize: 12 }}
                        tickMargin={16}
                        height={38}
                        interval={0}
                    />
                    <YAxis
                        tickLine={false}
                        axisLine={false}
                        tick={{ fill: 'var(--muted-foreground)', fontSize: 12 }}
                        width={48}
                        tickMargin={16}
                    />
                    <Tooltip
                        trigger={trigger}
                        active={active}
                        cursor={{ fill: 'var(--muted)' }}
                        content={<ChartTooltip />}
                    />
                    <Bar dataKey="value" radius={[4, 4, 0, 0]}>
                        {data.map((entry, index) => (
                            <Cell key={entry.label} fill={chartColor(index)} />
                        ))}
                    </Bar>
                </RechartsBarChart>
            </ResponsiveContainer>
        </div>
    );
}
