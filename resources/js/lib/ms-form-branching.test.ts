import { describe, expect, it } from 'vitest';

import { type MsFormQuestion, type MsFormSection, type MsFormValues } from '../types/ms-forms';
import { computeReachableIds, getSectionIds, resolveNextSectionId } from './ms-form-branching';

const sections: MsFormSection[] = [
    { id: 'section-1', title: null, subtitle: null, questionIds: ['q1'] },
    { id: 'section-2', title: null, subtitle: null, questionIds: ['q2'] },
    { id: 'section-3', title: null, subtitle: null, questionIds: ['q3'] },
];

const questions: MsFormQuestion[] = [
    {
        id: 'q1',
        title: { text: 'Rahasiakan identitas?' },
        subtitle: { text: '' },
        type: 'choice',
        required: true,
        multiple: false,
        choices: [
            { value: 'Ya', label: { text: 'Ya' }, branchTargetId: 'q3' },
            { value: 'Tidak', label: { text: 'Tidak' }, branchTargetId: 'q2' },
        ],
    },
    {
        id: 'q2',
        title: { text: 'Nama Lengkap' },
        subtitle: { text: '' },
        type: 'text',
        required: false,
        multiple: false,
        choices: [],
    },
    {
        id: 'q3',
        title: { text: 'Jenis Masukan' },
        subtitle: { text: '' },
        type: 'choice',
        required: true,
        multiple: false,
        choices: [
            { value: 'Saran', label: { text: 'Saran' }, branchTargetId: null },
            { value: 'Keluhan', label: { text: 'Keluhan' }, branchTargetId: null },
        ],
    },
];

const endSections: MsFormSection[] = [
    { id: 'section-1', title: null, subtitle: null, questionIds: ['q1'] },
    { id: 'section-2', title: null, subtitle: null, questionIds: ['q2'] },
];

const endQuestions: MsFormQuestion[] = [
    {
        id: 'q1',
        title: { text: 'Rahasiakan identitas?' },
        subtitle: { text: '' },
        type: 'choice',
        required: true,
        multiple: false,
        choices: [
            { value: 'Ya', label: { text: 'Ya' }, branchTargetId: 'end' },
            { value: 'Tidak', label: { text: 'Tidak' }, branchTargetId: 'q2' },
        ],
    },
    {
        id: 'q2',
        title: { text: 'Nama Lengkap' },
        subtitle: { text: '' },
        type: 'text',
        required: false,
        multiple: false,
        choices: [],
    },
];

describe('getSectionIds', () => {
    it('falls back to a single section when sections are absent', () => {
        expect(getSectionIds(undefined)).toEqual(['section-1']);
    });
});

describe('resolveNextSectionId', () => {
    it('skips the identity section when identity is kept secret', () => {
        const answers: MsFormValues = { q1: 'Ya', q2: '', q3: '' };
        expect(resolveNextSectionId(sections, questions, answers, 'section-1')).toBe('section-3');
    });

    it('continues to the identity section when identity is public', () => {
        const answers: MsFormValues = { q1: 'Tidak', q2: '', q3: '' };
        expect(resolveNextSectionId(sections, questions, answers, 'section-1')).toBe('section-2');
    });

    it('continues linearly when the branch question is unanswered', () => {
        const answers: MsFormValues = { q1: '', q2: '', q3: '' };
        expect(resolveNextSectionId(sections, questions, answers, 'section-1')).toBe('section-2');
    });

    it('returns null on the last section without a branch', () => {
        const answers: MsFormValues = { q1: '', q2: '', q3: 'Saran' };
        expect(resolveNextSectionId(sections, questions, answers, 'section-3')).toBeNull();
    });

    it('returns null when a choice targets the end of the form', () => {
        const answers: MsFormValues = { q1: 'Ya', q2: '' };
        expect(resolveNextSectionId(endSections, endQuestions, answers, 'section-1')).toBeNull();
    });
});

describe('computeReachableIds', () => {
    it('excludes the identity questions when branching to a later section', () => {
        const answers: MsFormValues = { q1: 'Ya', q2: 'Budi', q3: '' };
        expect([...computeReachableIds(sections, questions, answers)].sort()).toEqual(['q1', 'q3']);
    });

    it('keeps every question reachable without branching', () => {
        const answers: MsFormValues = { q1: 'Tidak', q2: 'Budi', q3: 'Saran' };
        expect(computeReachableIds(sections, questions, answers)).toEqual(
            new Set(['q1', 'q2', 'q3'])
        );
    });

    it('stops at the end of the form', () => {
        const answers: MsFormValues = { q1: 'Ya', q2: 'Budi' };
        expect(computeReachableIds(endSections, endQuestions, answers)).toEqual(new Set(['q1']));
    });
});
