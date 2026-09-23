import { type ComponentProps } from 'react';

import { createLink } from '@tanstack/react-router';

import { cn } from '../lib/utils';
import { type TextVariant, textButtonVariant } from './text-variants';
import { buttonVariants } from './ui/button';

type LinkBaseProps = ComponentProps<'a'> & {
    variant: TextVariant;
};

function LinkBase({ className, variant, ...props }: LinkBaseProps) {
    return (
        <a
            className={cn(
                buttonVariants({
                    variant: textButtonVariant[variant],
                }),
                'h-auto p-0 text-lg md:text-base',
                variant === 'fade' &&
                    'text-muted-foreground hover:text-foreground hover:bg-transparent dark:hover:bg-transparent',
                variant === 'underline' && 'text-blue-600 dark:text-blue-400',
                className
            )}
            {...props}
        />
    );
}

export const Link = createLink(LinkBase);
