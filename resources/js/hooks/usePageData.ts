import {
    type QueryClient,
    type UseSuspenseQueryOptions,
    type UseSuspenseQueryResult,
    useSuspenseQuery,
} from '@tanstack/react-query';

import { httpGet } from '../lib/http';

export type QueryParams = Record<string, string | number | undefined>;

export function normalizeQueryParams(params?: QueryParams | unknown): QueryParams | undefined {
    if (!params || typeof params !== 'object' || Array.isArray(params)) {
        return undefined;
    }

    const entries = Object.entries(params as Record<string, unknown>).filter(
        ([, value]) => value !== undefined
    );

    if (entries.length === 0) {
        return undefined;
    }

    return Object.fromEntries(entries) as QueryParams;
}

export function pageQueryKey(apiEndpoint: string, params?: QueryParams | unknown) {
    return [apiEndpoint, normalizeQueryParams(params)];
}

export function fetchPageData<TData>(apiEndpoint: string, params?: QueryParams): Promise<TData> {
    return httpGet<TData>(apiEndpoint, params);
}

function readInitialData(): unknown {
    return (window as { __INITIAL_DATA__?: unknown }).__INITIAL_DATA__;
}

function clearInitialData(): void {
    (window as { __INITIAL_DATA__?: unknown }).__INITIAL_DATA__ = null;
}

export function isSeedEnvelope(value: unknown): value is { seeds: SeedEntry[] } {
    return (
        typeof value === 'object' &&
        value !== null &&
        'seeds' in value &&
        Array.isArray((value as { seeds?: unknown }).seeds)
    );
}

export interface SeedEntry {
    endpoint: string;
    params?: QueryParams;
    payload: unknown;
}

export function seedInitialQueries(queryClient: QueryClient): void {
    const raw = readInitialData();
    clearInitialData();

    if (!isSeedEnvelope(raw)) {
        return;
    }

    for (const seed of raw.seeds) {
        if (!seed || typeof seed.endpoint !== 'string') {
            continue;
        }
        queryClient.setQueryData(pageQueryKey(seed.endpoint, seed.params), seed.payload);
    }
}

export function useSuspensePageData<TQueryFnData = unknown, TData = TQueryFnData>(
    apiEndpoint: string,
    queryOptions: Omit<UseSuspenseQueryOptions<TQueryFnData, Error, TData>, 'queryKey'> = {},
    params?: QueryParams
): UseSuspenseQueryResult<TData, Error> {
    return useSuspenseQuery<TQueryFnData, Error, TData>({
        queryKey: pageQueryKey(apiEndpoint, params),
        queryFn: () => fetchPageData<TQueryFnData>(apiEndpoint, params),
        ...queryOptions,
    });
}

export function ensurePageData<TData = unknown>(
    queryClient: QueryClient,
    apiEndpoint: string,
    params?: QueryParams
): Promise<TData> {
    return queryClient.ensureQueryData<TData>({
        queryKey: pageQueryKey(apiEndpoint, params),
        queryFn: () => fetchPageData<TData>(apiEndpoint, params),
    });
}
