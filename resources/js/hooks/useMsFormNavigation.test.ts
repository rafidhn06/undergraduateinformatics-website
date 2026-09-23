import { act, renderHook } from '@testing-library/react';
import { describe, expect, it } from 'vitest';

import { useMsFormNavigation } from './useMsFormNavigation';

const sections = [
    { id: 's1', title: null, subtitle: null, questionIds: ['nama'] },
    { id: 's2', title: null, subtitle: null, questionIds: ['sesi'] },
];

describe('useMsFormNavigation', () => {
    it('starts at the first section and advances on goNext', () => {
        const { result } = renderHook(() => useMsFormNavigation(sections, [], () => ({})));

        expect(result.current.currentSectionId).toBe('s1');
        expect(result.current.isFirstStep).toBe(true);

        act(() => {
            result.current.goNext('s2');
        });

        expect(result.current.currentSectionId).toBe('s2');
        expect(result.current.isFirstStep).toBe(false);
    });

    it('goes back on goPrevious without dropping below the first section', () => {
        const { result } = renderHook(() => useMsFormNavigation(sections, [], () => ({})));

        act(() => {
            result.current.goNext('s2');
        });
        act(() => {
            result.current.goPrevious();
        });

        expect(result.current.currentSectionId).toBe('s1');

        act(() => {
            result.current.goPrevious();
        });

        expect(result.current.currentSectionId).toBe('s1');
    });

    it('jumps back to the first section on goFirst', () => {
        const { result } = renderHook(() => useMsFormNavigation(sections, [], () => ({})));

        act(() => {
            result.current.goNext('s2');
        });

        expect(result.current.currentSectionId).toBe('s2');

        act(() => {
            result.current.goFirst();
        });

        expect(result.current.currentSectionId).toBe('s1');
        expect(result.current.isFirstStep).toBe(true);
    });
});
