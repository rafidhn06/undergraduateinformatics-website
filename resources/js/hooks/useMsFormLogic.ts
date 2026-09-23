import { useEffect, useMemo, useRef, useState } from 'react';
import { type Control, useForm, useWatch } from 'react-hook-form';

import { zodResolver } from '@hookform/resolvers/zod';

import { buildMsFormAnswers, isEmptyAnswer } from '../lib/ms-form-answers';
import {
    computeReachableIds,
    flattenQuestions,
    getSectionQuestions,
    resolveNextSectionId,
} from '../lib/ms-form-branching';
import { buildMsFormDefaultValues, buildMsFormSchema } from '../schemas/ms-forms';
import { type MsFormQuestion, type MsFormSection, type MsFormValues } from '../types/ms-forms';
import { UNAVAILABLE_MESSAGE, useMsFormAvailability } from './useMsFormAvailability';
import { useMsFormNavigation } from './useMsFormNavigation';
import { useMsFormSubmission } from './useMsFormSubmission';

export interface MsFormExtension {
    fieldExtraErrors?: (values: MsFormValues) => Record<string, string | null>;
    fieldErrorsMap?: Record<string, string>;
    fieldErrorTranslator?: (message: string) => string;
    availabilityCheck?: {
        dateQuestionId: string;
        shiftQuestionId: string;
        check: (date: string, shift: string) => Promise<boolean>;
    };
}

export interface UseMsFormLogicOptions {
    questions: MsFormQuestion[];
    sections?: MsFormSection[];
    submitUrl: string;
    extension?: MsFormExtension;
}

export interface UseMsFormLogicReturn {
    control: Control<MsFormValues>;
    values: MsFormValues;
    currentSectionId: string;
    currentSection: MsFormSection | undefined;
    visibleQuestions: MsFormQuestion[];
    nextSectionId: string | null;
    hasNextSection: boolean;
    isFirstStep: boolean;
    hasAnyAnswer: boolean;
    submitForm: ReturnType<typeof useMsFormSubmission>['submitForm'];
    submitError: string | null;
    fieldErrors: Record<string, string[]> | null;
    emptySubmitTried: boolean;
    extraFieldErrors: Record<string, string | null>;
    extraErrorActive: boolean;
    availabilityUnavailable: boolean;
    handleNext: () => Promise<void>;
    handlePrevious: () => void;
    handleValidSubmit: () => Promise<void>;
    handleReset: () => void;
}

export function useMsFormLogic({
    questions,
    sections,
    submitUrl,
    extension,
}: UseMsFormLogicOptions): UseMsFormLogicReturn {
    const schema = useMemo(() => buildMsFormSchema(questions), [questions]);

    const form = useForm<MsFormValues>({
        resolver: zodResolver(schema),
        defaultValues: buildMsFormDefaultValues(questions),
        mode: 'onChange',
    });

    const { control, getValues, setValue } = form;

    const navigation = useMsFormNavigation(sections, questions, getValues);
    const { currentSectionId, currentSection, isFirstStep } = navigation;
    const allQuestions = useMemo(
        () => flattenQuestions(sections, questions),
        [sections, questions]
    );
    const [emptySubmitTried, setEmptySubmitTried] = useState(false);

    const { submitForm, submitError, fieldErrors, resetSubmitError } = useMsFormSubmission(
        submitUrl,
        sections,
        questions
    );

    const mappedFieldErrors = useMemo(() => {
        if (!fieldErrors || !extension?.fieldErrorsMap) {
            return fieldErrors;
        }

        const translator = extension.fieldErrorTranslator;
        const mapped: Record<string, string[]> = {};

        for (const [fieldName, messages] of Object.entries(fieldErrors)) {
            const questionId = extension.fieldErrorsMap[fieldName];

            if (questionId) {
                mapped[questionId] = translator ? messages.map(translator) : messages;
            }
        }

        return mapped;
    }, [extension, fieldErrors]);

    const values = useWatch({ control }) as MsFormValues;

    const previousValuesRef = useRef(values);
    const hasServerErrors = fieldErrors !== null || submitError !== null;

    useEffect(() => {
        if (!hasServerErrors) {
            previousValuesRef.current = values;
            return;
        }

        const changed = questions.some(
            (question) => previousValuesRef.current[question.id] !== values[question.id]
        );

        if (changed) {
            resetSubmitError();
        }

        previousValuesRef.current = values;
    }, [values, hasServerErrors, questions, resetSubmitError]);

    const visibleQuestions = getSectionQuestions(sections, questions, currentSectionId);
    const nextSectionId = resolveNextSectionId(sections, questions, values, currentSectionId);
    const hasNextSection = nextSectionId !== null;

    const hasAnyAnswer = useMemo(
        () => buildMsFormAnswers(sections, questions, values).length > 0,
        [sections, questions, values]
    );

    const baseExtraErrors = useMemo(
        () => (extension?.fieldExtraErrors ? extension.fieldExtraErrors(values) : {}),
        [extension, values]
    );

    const extraErrorActive = Object.values(baseExtraErrors).some((message) => Boolean(message));

    const dateValue = values[extension?.availabilityCheck?.dateQuestionId ?? ''] as
        string | undefined;
    const shiftValue = values[extension?.availabilityCheck?.shiftQuestionId ?? ''] as
        string | undefined;

    const availabilityCheck = extension?.availabilityCheck;

    const { availabilityUnavailable } = useMsFormAvailability({
        dateValue: availabilityCheck ? dateValue : undefined,
        shiftValue: availabilityCheck ? shiftValue : undefined,
        check: (date, shift) => availabilityCheck!.check(date, shift),
    });

    const extraFieldErrors = useMemo(() => {
        if (!availabilityUnavailable || !extension?.availabilityCheck) {
            return baseExtraErrors;
        }

        return {
            ...baseExtraErrors,
            [extension.availabilityCheck.shiftQuestionId]: UNAVAILABLE_MESSAGE,
        };
    }, [baseExtraErrors, availabilityUnavailable, extension]);

    const scrollToField = (questionId: string | null) => {
        if (!questionId) {
            return;
        }

        document
            .querySelector(`[data-question-id="${questionId}"]`)
            ?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    const hadServerFieldErrors = useRef(false);

    useEffect(() => {
        const hasServerErrors =
            mappedFieldErrors !== null &&
            Object.values(mappedFieldErrors).some((messages) => messages.length > 0);

        if (hasServerErrors && !hadServerFieldErrors.current) {
            const firstErrorId = questions.find(
                (question) => (mappedFieldErrors?.[question.id]?.length ?? 0) > 0
            )?.id;

            scrollToField(firstErrorId ?? null);
        }

        hadServerFieldErrors.current = hasServerErrors;
    }, [mappedFieldErrors, questions]);

    const isFirstRender = useRef(true);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        const targetSection = sections?.find((section) => section.id === currentSectionId);
        const firstQuestionId = targetSection?.questionIds[0];

        scrollToField(firstQuestionId ?? null);
    }, [currentSectionId, sections]);

    const clearHiddenAnswers = () => {
        const reachable = computeReachableIds(sections, questions, getValues());

        for (const question of allQuestions) {
            if (reachable.has(question.id)) {
                continue;
            }

            const value = getValues(question.id);

            if (!isEmptyAnswer(value)) {
                setValue(question.id, question.multiple ? [] : '', { shouldDirty: true });
            }
        }
    };

    const scrollToFirstInvalid = (targetQuestions: MsFormQuestion[]) => {
        const result = buildMsFormSchema(targetQuestions).safeParse(getValues());
        const invalidIds = new Set(
            result.success ? [] : result.error.issues.map((issue) => issue.path[0])
        );
        const invalidId = targetQuestions.find((question) => invalidIds.has(question.id))?.id;

        scrollToField(invalidId ?? null);
    };

    const scrollToFirstExtraError = () => {
        const invalidId = questions.find((question) => extraFieldErrors[question.id])?.id;

        scrollToField(invalidId ?? null);
    };

    const handleNext = async () => {
        const valid = await form.trigger(visibleQuestions.map((question) => question.id));

        if (!valid || extraErrorActive || availabilityUnavailable) {
            if (extraErrorActive || availabilityUnavailable) {
                scrollToFirstExtraError();
            } else {
                scrollToFirstInvalid(visibleQuestions);
            }
            return;
        }

        clearHiddenAnswers();

        const nextId = resolveNextSectionId(sections, questions, getValues(), currentSectionId);

        if (nextId === null) {
            return;
        }

        navigation.goNext(nextId);
        resetSubmitError();
        setEmptySubmitTried(false);
    };

    const handlePrevious = () => {
        navigation.goPrevious();
        resetSubmitError();
        setEmptySubmitTried(false);
    };

    const handleValidSubmit = async () => {
        if (extraErrorActive || availabilityUnavailable) {
            if (extraErrorActive || availabilityUnavailable) {
                scrollToFirstExtraError();
            } else {
                scrollToFirstInvalid(visibleQuestions);
            }
            return;
        }

        const reachable = computeReachableIds(sections, questions, getValues());
        const valid = await form.trigger(
            questions
                .filter((question) => reachable.has(question.id))
                .map((question) => question.id)
        );

        if (!valid) {
            scrollToFirstInvalid(visibleQuestions);
            return;
        }

        if (buildMsFormAnswers(sections, questions, getValues()).length === 0) {
            setEmptySubmitTried(true);
            return;
        }

        clearHiddenAnswers();
        submitForm.mutate(getValues());
    };

    const handleReset = () => {
        form.reset(buildMsFormDefaultValues(questions));
        navigation.goFirst();
        resetSubmitError();
        setEmptySubmitTried(false);
        submitForm.reset();
    };

    return {
        control,
        values,
        currentSectionId,
        currentSection,
        visibleQuestions,
        nextSectionId,
        hasNextSection,
        isFirstStep,
        hasAnyAnswer,
        submitForm,
        submitError,
        fieldErrors: mappedFieldErrors,
        emptySubmitTried,
        extraFieldErrors,
        extraErrorActive,
        availabilityUnavailable,
        handleNext,
        handlePrevious,
        handleValidSubmit,
        handleReset,
    };
}
