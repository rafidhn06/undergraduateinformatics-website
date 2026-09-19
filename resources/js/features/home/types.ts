import { type PostSearchMeta } from '@/features/search/types';
import { type ApiSuccessResponse } from '@/types/api';
import { type LinkSummary } from '@/types/link';
import { type PostSummary } from '@/types/post';

export type ChartType = 'bar' | 'pie' | 'line';

export interface DashboardDataset {
    id: number;
    title: string;
    chart_type: ChartType;
    x_label: string | null;
    y_label: string | null;
    labels: string[];
    values: number[];
}

export interface HomeData {
    latest_posts: PostSummary[];
    latest_links: LinkSummary[];
    dashboard: DashboardDataset[];
}

export type ImportantLinksPayload = ApiSuccessResponse<LinkSummary[]> & {
    meta: PostSearchMeta;
};

export type DatasetsPayload = ApiSuccessResponse<DashboardDataset[]>;
