import { type ComponentProps } from 'react';

import { cn } from '../lib/utils';
import { type TextVariant, textButtonVariant } from './text-variants';
import { Button } from './ui/button';

type TextButtonProps = Omit<ComponentProps<typeof Button>, 'variant'> & {
    variant: TextVariant;
};

export function TextButton({ className, variant, ...props }: TextButtonProps) {
    return (
        <Button
            variant={textButtonVariant[variant]}
            className={cn(
                'h-auto p-0 text-lg md:text-base',
                variant === 'fade' &&
                    'text-muted-foreground hover:text-foreground disabled:text-foreground/50 hover:bg-transparent dark:hover:bg-transparent',
                variant === 'underline' && 'text-blue-600 dark:text-blue-400',
                className
            )}
            {...props}
        />
    );
}
