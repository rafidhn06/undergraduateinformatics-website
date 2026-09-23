import { format } from 'date-fns';
import { id } from 'date-fns/locale';

import { Link } from '@/components/Link';

import { type LinkSummary } from '../types/link';

interface LinkCardProps {
    link: LinkSummary;
}

export function LinkCard({ link }: LinkCardProps) {
    return (
        <article className="not-typeset flex flex-col gap-2">
            <h3>
                <Link
                    variant="underline"
                    to={link.link}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="line-clamp-2 leading-6 whitespace-normal"
                >
                    {link.name}
                </Link>
            </h3>
            <div className="space-x-2 truncate leading-6">
                <Link
                    variant="fade"
                    to="/links"
                    hash={`link-section-${link.section.id}`}
                    className="text-muted-foreground hover:text-foreground inline text-sm md:text-sm"
                >
                    {link.section.name}
                </Link>
            </div>
            <p className="text-muted-foreground text-sm leading-6">
                {format(link.updated_at, 'd MMM yyyy', { locale: id })}
            </p>
        </article>
    );
}
