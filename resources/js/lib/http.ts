import axios, { AxiosError } from 'axios';

import type { QueryParams } from '../hooks/usePageData';

export async function httpGet<T>(url: string, params?: QueryParams): Promise<T> {
    const response = await axios.get(url, { params });
    return response.data as T;
}

export async function httpPost<T>(url: string, body?: unknown): Promise<T> {
    const response = await axios.post(url, body);
    return response.data as T;
}

export function isHttpNotFound(error: unknown): boolean {
    return error instanceof AxiosError && error.response?.status === 404;
}
