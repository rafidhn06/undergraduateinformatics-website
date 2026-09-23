import { Loader2 } from 'lucide-react';

import { ArticleContainer } from '../components/ArticleContainer';
import { type UseMsFormLogicReturn } from '../hooks/useMsFormLogic';
import { type MsRichText } from '../types/ms-forms';
import { MsFormField } from './MsFormField';
import { MsFormSuccess } from './MsFormStatus';
import { PrimaryButton } from './PrimaryButton';
import { RichText } from './RichText';
import { RichTextContent } from './RichTextContent';
import { SecondaryButton } from './SecondaryButton';
import { FieldDescription, FieldGroup } from './ui/field';

interface MsFormViewProps {
    logic: UseMsFormLogicReturn;
    title: MsRichText;
    description: MsRichText | null;
}

export function MsFormView({ logic, title, description }: MsFormViewProps) {
    const {
        control,
        currentSection,
        visibleQuestions,
        isFirstStep,
        hasNextSection,
        hasAnyAnswer,
        submitForm,
        submitError,
        fieldErrors,
        emptySubmitTried,
        handleNext,
        handlePrevious,
        handleValidSubmit,
        handleReset,
    } = logic;

    if (submitForm.isSuccess) {
        return <MsFormSuccess onReset={handleReset} />;
    }

    return (
        <ArticleContainer>
            <form noValidate>
                <h1>
                    <RichTextContent content={title} as="span" />
                </h1>
                {description && (
                    <RichTextContent
                        content={description}
                        as="div"
                        className="text-muted-foreground"
                    />
                )}
                {currentSection?.title && (
                    <h2>
                        <RichTextContent content={currentSection.title} as="span" />
                    </h2>
                )}
                {currentSection?.subtitle && (
                    <RichTextContent
                        content={currentSection.subtitle}
                        as="div"
                        className="text-muted-foreground"
                    />
                )}

                {visibleQuestions.map((question) => (
                    <section key={question.id} data-question-id={question.id}>
                        <h3>
                            <RichTextContent content={question.title} as="span" />
                            {question.required && (
                                <span aria-hidden="true" className="text-destructive ml-1">
                                    *
                                </span>
                            )}
                        </h3>
                        <FieldGroup>
                            {question.subtitle &&
                                (question.subtitle.text || question.subtitle.html) &&
                                (question.subtitle.html ? (
                                    <RichText
                                        as="div"
                                        className="text-muted-foreground [&>a:hover]:text-primary text-left text-base leading-normal font-normal group-has-data-horizontal/field:text-balance last:mt-0 nth-last-2:-mt-1 [&>a]:underline [&>a]:underline-offset-4 [[data-variant=legend]+&]:-mt-1.5"
                                        html={question.subtitle.html}
                                    />
                                ) : (
                                    <FieldDescription>{question.subtitle.text}</FieldDescription>
                                ))}
                            <MsFormField question={question} control={control} />
                            {logic.extraFieldErrors[question.id] && (
                                <p role="alert" className="text-destructive mt-0 text-base md:mt-0">
                                    {logic.extraFieldErrors[question.id]}
                                </p>
                            )}
                            {fieldErrors?.[question.id]?.map((message) => (
                                <p
                                    key={message}
                                    role="alert"
                                    className="text-destructive mt-0 text-base md:mt-0"
                                >
                                    {message}
                                </p>
                            ))}
                        </FieldGroup>
                    </section>
                ))}

                {submitError && (
                    <p role="alert" className="text-destructive">
                        {submitError}
                    </p>
                )}

                {emptySubmitTried && !hasAnyAnswer && (
                    <p role="alert" className="text-destructive">
                        Isi minimal satu jawaban terlebih dahulu.
                    </p>
                )}

                <div className="mt-[39.375px] flex items-center gap-2 md:mt-8.75">
                    {!isFirstStep && (
                        <SecondaryButton
                            type="button"
                            onClick={handlePrevious}
                            className="flex-1 md:flex-none"
                        >
                            Kembali
                        </SecondaryButton>
                    )}
                    {hasNextSection ? (
                        <PrimaryButton
                            type="button"
                            onClick={() => void handleNext()}
                            className="flex-1 md:flex-none"
                        >
                            Lanjut
                        </PrimaryButton>
                    ) : (
                        <PrimaryButton
                            type="button"
                            onClick={() => void handleValidSubmit()}
                            disabled={submitForm.isPending}
                            className="flex-1 md:flex-none"
                        >
                            {submitForm.isPending ? <Loader2 className="animate-spin" /> : null}
                            Kirim
                        </PrimaryButton>
                    )}
                </div>
            </form>
        </ArticleContainer>
    );
}
