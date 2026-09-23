export interface ChartTooltipItem {
    name?: string;
    value?: number | string;
}

export interface ChartTooltipProps {
    active?: boolean;
    label?: string | number;
    payload?: ChartTooltipItem[];
    unit?: string;
}

export function ChartTooltip({ active, label, payload, unit }: ChartTooltipProps) {
    if (!active || !payload?.length) {
        return null;
    }

    const item = payload[0];

    return (
        <div className="border-border bg-popover text-popover-foreground flex flex-col gap-0.5 rounded-md border px-3 py-2 text-xs shadow-md">
            {label !== undefined && <span className="font-medium">{label}</span>}
            <span>
                {item.name ? `${item.name}: ` : ''}
                {item.value}
                {unit ?? ''}
            </span>
        </div>
    );
}
