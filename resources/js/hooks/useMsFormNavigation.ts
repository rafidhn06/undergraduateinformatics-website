import { useState } from 'react';

import { getSectionIds } from '../lib/ms-form-branching';
import type { MsFormQuestion, MsFormSection, MsFormValues } from '../types/ms-forms';

export function useMsFormNavigation(
    sections: MsFormSection[] | undefined,
    questions: MsFormQuestion[],
    getValues: () => MsFormValues
) {
    void questions;
    void getValues;
    const sectionIds = getSectionIds(sections);
    const [initialSectionId] = useState<string>(() => sectionIds[0]);
    const [history, setHistory] = useState<string[]>([initialSectionId]);
    const currentSectionId = history[history.length - 1];
    const currentSection = sections?.find((section) => section.id === currentSectionId);

    return {
        currentSectionId,
        currentSection,
        hasNextSection: sectionIds.indexOf(currentSectionId) < sectionIds.length - 1,
        isFirstStep: history.length === 1,
        goNext: (nextId: string) => setHistory((current) => [...current, nextId]),
        goPrevious: () =>
            setHistory((current) => (current.length > 1 ? current.slice(0, -1) : current)),
        goFirst: () => setHistory([initialSectionId]),
    };
}
