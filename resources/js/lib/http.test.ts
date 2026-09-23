import axios, { AxiosError } from 'axios';
import { describe, expect, it, vi } from 'vitest';

import { httpGet, httpPost, isHttpNotFound } from './http';

vi.mock('axios');

describe('http adapter', () => {
    it('unwraps response data on get', async () => {
        vi.mocked(axios.get).mockResolvedValue({ data: { status: 'success' } });
        await expect(httpGet('/api/posts', { per_page: 5 })).resolves.toEqual({
            status: 'success',
        });
        expect(axios.get).toHaveBeenCalledWith('/api/posts', { params: { per_page: 5 } });
    });

    it('unwraps response data on post', async () => {
        vi.mocked(axios.post).mockResolvedValue({ data: { ok: true } });
        await expect(httpPost('/api/x', { answers: [] })).resolves.toEqual({ ok: true });
        expect(axios.post).toHaveBeenCalledWith('/api/x', { answers: [] });
    });

    it('detects 404 without importing AxiosError at call sites', () => {
        const error = new AxiosError('not found');
        error.response = { status: 404 } as never;
        expect(isHttpNotFound(error)).toBe(true);
        expect(isHttpNotFound(new Error('other'))).toBe(false);
    });
});
