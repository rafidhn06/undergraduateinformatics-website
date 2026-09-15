import {
    Cell,
    Legend,
    type LegendPayload,
    Pie,
    PieChart as RechartsPieChart,
    ResponsiveContainer,
    Tooltip,
} from 'recharts';

import { chartColor } from './chart-color';
import { ChartTooltip } from './chart-tooltip';
import { useChartTooltip } from './use-chart-tooltip';

interface PieChartProps {
    labels: string[];
    values: number[];
}

export function PieChart({ labels, values }: PieChartProps) {
    const { containerRef, trigger, active, chartProps } = useChartTooltip();

    const data = labels.map((label, index) => ({
        label,
        value: values[index] ?? 0,
    }));

    return (
        <div ref={containerRef} className="aspect-[4/3] w-full">
            <ResponsiveContainer width="100%" height="100%">
                <RechartsPieChart {...chartProps}>
                    <Tooltip trigger={trigger} active={active} content={<ChartTooltip />} />
                    <Pie
                        data={data}
                        dataKey="value"
                        nameKey="label"
                        outerRadius="80%"
                        stroke="none"
                    >
                        {data.map((entry, index) => {
                            const fill = chartColor(index);

                            return (
                                <Cell key={entry.label} fill={fill} stroke={fill} strokeWidth={1} />
                            );
                        })}
                    </Pie>
                    <Legend
                        content={(props) => <PieLegend payload={props.payload} />}
                        wrapperStyle={{ fontSize: 12 }}
                    />
                </RechartsPieChart>
            </ResponsiveContainer>
        </div>
    );
}

function PieLegend({ payload }: { payload?: ReadonlyArray<LegendPayload> }) {
    return (
        <ul className="text-muted-foreground flex flex-wrap justify-center gap-x-4 gap-y-1 pt-2">
            {(payload ?? []).map((entry: LegendPayload) => (
                <li key={entry.value} className="flex items-center gap-1.5">
                    <span
                        className="size-2.5 rounded-none"
                        style={{ backgroundColor: entry.color }}
                    />
                    {entry.value}
                </li>
            ))}
        </ul>
    );
}
