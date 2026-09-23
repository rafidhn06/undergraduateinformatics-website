import { useState } from 'react';
import { type Control, Controller } from 'react-hook-form';

import { format, isValid, parse } from 'date-fns';
import { CalendarIcon } from 'lucide-react';

import { Calendar } from '@/components/ui/calendar';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Field,
    FieldError,
    FieldGroup,
    FieldLabel,
    FieldLegend,
    FieldSet,
} from '@/components/ui/field';
import {
    InputGroup,
    InputGroupAddon,
    InputGroupButton,
    InputGroupInput,
} from '@/components/ui/input-group';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Textarea } from '@/components/ui/textarea';

import { type MsFormQuestion, type MsFormValues } from '../types/ms-forms';
import { RichTextContent } from './RichTextContent';

const NEUTRAL_INVALID_CONTROL_CLASS =
    'aria-invalid:border-input aria-invalid:ring-0 aria-invalid:ring-transparent dark:aria-invalid:border-input dark:aria-invalid:ring-0 dark:aria-invalid:ring-transparent';

const NEUTRAL_INVALID_GROUP_CLASS =
    'has-[[data-slot][aria-invalid=true]]:border-input has-[[data-slot][aria-invalid=true]]:ring-0 has-[[data-slot][aria-invalid=true]]:ring-transparent dark:has-[[data-slot][aria-invalid=true]]:ring-0 dark:has-[[data-slot][aria-invalid=true]]:ring-transparent';

interface DateFieldInputProps {
    id: string;
    value: string;
    onChange: (value: string) => void;
    invalid: boolean;
}

function DateFieldInput({ id, value, onChange, invalid }: DateFieldInputProps) {
    const [open, setOpen] = useState(false);
    const [month, setMonth] = useState<Date | undefined>(
        value ? parse(value, 'yyyy-MM-dd', new Date()) : undefined
    );

    return (
        <InputGroup className={NEUTRAL_INVALID_GROUP_CLASS}>
            <InputGroupInput
                id={id}
                value={value}
                placeholder="YYYY-MM-DD"
                aria-invalid={invalid}
                onChange={(e) => {
                    const parsed = parse(e.target.value, 'yyyy-MM-dd', new Date());
                    onChange(isValid(parsed) ? format(parsed, 'yyyy-MM-dd') : e.target.value);
                }}
                onKeyDown={(e) => {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        setOpen(true);
                    }
                }}
            />
            <InputGroupAddon align="inline-end">
                <Popover open={open} onOpenChange={setOpen}>
                    <PopoverTrigger
                        render={
                            <InputGroupButton
                                variant="ghost"
                                size="icon-xs"
                                aria-label="Pilih tanggal"
                            />
                        }
                    >
                        <CalendarIcon />
                        <span className="sr-only">Pilih tanggal</span>
                    </PopoverTrigger>
                    <PopoverContent
                        className="w-auto overflow-hidden p-0"
                        align="end"
                        alignOffset={-8}
                        sideOffset={10}
                    >
                        <Calendar
                            mode="single"
                            selected={value ? parse(value, 'yyyy-MM-dd', new Date()) : undefined}
                            month={month}
                            onMonthChange={setMonth}
                            onSelect={(date) => {
                                onChange(date ? format(date, 'yyyy-MM-dd') : '');
                                setOpen(false);
                            }}
                        />
                    </PopoverContent>
                </Popover>
            </InputGroupAddon>
        </InputGroup>
    );
}

interface MsFormFieldProps {
    question: MsFormQuestion;
    control: Control<MsFormValues>;
}

export function MsFormField({ question, control }: MsFormFieldProps) {
    if (question.type === 'text' || question.type === 'date') {
        return (
            <Controller
                control={control}
                name={question.id}
                render={({ field, fieldState }) => (
                    <Field>
                        <FieldLabel htmlFor={question.id} className="sr-only">
                            {question.title.text}
                        </FieldLabel>
                        {question.type === 'text' ? (
                            <Textarea
                                {...field}
                                id={question.id}
                                rows={3}
                                aria-invalid={fieldState.invalid}
                                className={NEUTRAL_INVALID_CONTROL_CLASS}
                            />
                        ) : (
                            <DateFieldInput
                                id={question.id}
                                value={field.value as string}
                                onChange={field.onChange}
                                invalid={fieldState.invalid}
                            />
                        )}
                        {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                    </Field>
                )}
            />
        );
    }

    if (question.multiple) {
        return (
            <Controller
                control={control}
                name={question.id}
                render={({ field, fieldState }) => (
                    <FieldSet>
                        <FieldLegend variant="label" className="sr-only">
                            {question.title.text}
                        </FieldLegend>
                        <FieldGroup data-slot="checkbox-group">
                            {question.choices.map((choice) => {
                                const values = field.value as string[];
                                return (
                                    <FieldLabel
                                        key={choice.value}
                                        htmlFor={`${question.id}-${choice.value}`}
                                    >
                                        <Field orientation="horizontal">
                                            <Checkbox
                                                id={`${question.id}-${choice.value}`}
                                                aria-invalid={fieldState.invalid}
                                                className={NEUTRAL_INVALID_CONTROL_CLASS}
                                                checked={values.includes(choice.value)}
                                                onCheckedChange={(checked) =>
                                                    field.onChange(
                                                        checked
                                                            ? [...values, choice.value]
                                                            : values.filter(
                                                                  (value) => value !== choice.value
                                                              )
                                                    )
                                                }
                                            />
                                            <RichTextContent content={choice.label} as="span" />
                                        </Field>
                                    </FieldLabel>
                                );
                            })}
                        </FieldGroup>
                        {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                    </FieldSet>
                )}
            />
        );
    }

    return (
        <Controller
            control={control}
            name={question.id}
            render={({ field, fieldState }) => (
                <FieldSet>
                    <FieldLegend variant="label" className="sr-only" id={`${question.id}-title`}>
                        {question.title.text}
                    </FieldLegend>
                    <RadioGroup
                        name={field.name}
                        value={field.value as string}
                        onValueChange={field.onChange}
                        aria-labelledby={`${question.id}-title`}
                    >
                        {question.choices.map((choice) => (
                            <FieldLabel
                                key={choice.value}
                                htmlFor={`${question.id}-${choice.value}`}
                                onClick={(event) => {
                                    if (field.value === choice.value) {
                                        event.preventDefault();
                                        field.onChange('');
                                    }
                                }}
                            >
                                <Field orientation="horizontal">
                                    <RadioGroupItem
                                        value={choice.value}
                                        id={`${question.id}-${choice.value}`}
                                        aria-invalid={fieldState.invalid}
                                        className={NEUTRAL_INVALID_CONTROL_CLASS}
                                    />
                                    <RichTextContent content={choice.label} as="span" />
                                </Field>
                            </FieldLabel>
                        ))}
                    </RadioGroup>
                    {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </FieldSet>
            )}
        />
    );
}
