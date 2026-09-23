import { act, renderHook } from '@testing-library/react';
import { beforeEach, describe, expect, it } from 'vitest';

import { usePublicTheme } from './usePublicTheme';

describe('usePublicTheme', () => {
    beforeEach(() => {
        const store: Record<string, string> = {};

        Object.defineProperty(window, 'localStorage', {
            configurable: true,
            writable: true,
            value: {
                getItem: (key: string) => store[key] ?? null,
                setItem: (key: string, value: string) => {
                    store[key] = value;
                },
                removeItem: (key: string) => {
                    delete store[key];
                },
                clear: () => {
                    for (const key of Object.keys(store)) {
                        delete store[key];
                    }
                },
            },
        });
        document.documentElement.classList.remove('dark');
    });

    it('uses stored dark theme and applies dark class', () => {
        window.localStorage.setItem('public-theme', 'dark');

        const { result } = renderHook(() => usePublicTheme());

        expect(result.current.isDark).toBe(true);
        expect(document.documentElement.classList.contains('dark')).toBe(true);
    });

    it('toggles theme and persists selection', () => {
        window.localStorage.setItem('public-theme', 'light');

        const { result } = renderHook(() => usePublicTheme());

        act(() => {
            result.current.toggleTheme();
        });

        expect(result.current.isDark).toBe(true);
        expect(document.documentElement.classList.contains('dark')).toBe(true);
        expect(window.localStorage.getItem('public-theme')).toBe('dark');
    });

    it('follows theme changes from other tabs', () => {
        window.localStorage.setItem('public-theme', 'light');

        const { result } = renderHook(() => usePublicTheme());

        expect(result.current.isDark).toBe(false);

        act(() => {
            window.dispatchEvent(
                new StorageEvent('storage', { key: 'public-theme', newValue: 'dark' })
            );
        });

        expect(result.current.isDark).toBe(true);
        expect(document.documentElement.classList.contains('dark')).toBe(true);
    });
});
